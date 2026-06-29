<?php snippet('layout', slots: true); ?>

<?php slot(); ?>
  <?php $buildSearchPageUrl = static function (int $targetPage) use (
    $page,
    $query,
    $category,
  ): string {
    $params = [
      'q' => $query,
      'category' => $category,
    ];

    if ($targetPage > 1) {
      $params['p'] = $targetPage;
    }

    return $page->url() . '?' . http_build_query($params);
  }; ?>
  <?php $highlightQuery = static function (string $text, string $query): string {
    $query = trim($query);
    if ($query === '') {
      return esc($text);
    }

    $terms = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($query), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $terms = array_values(
      array_unique(array_filter($terms, static fn(string $term): bool => mb_strlen($term) >= 2)),
    );

    if ($terms === []) {
      return esc($text);
    }

    usort($terms, static fn(string $a, string $b): int => mb_strlen($b) <=> mb_strlen($a));
    $pattern =
      '/(' .
      implode('|', array_map(static fn(string $term): string => preg_quote($term, '/'), $terms)) .
      ')/iu';

    $parts = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    if (!is_array($parts) || $parts === []) {
      return esc($text);
    }

    $result = '';
    foreach ($parts as $index => $part) {
      if ($part === '') {
        continue;
      }

      if ($index % 2 === 1) {
        $result .= '<mark class="search-result-mark">' . esc($part) . '</mark>';
      } else {
        $result .= esc($part);
      }
    }

    return $result;
  }; ?>
  <?php $renderSearchForm = function () use (
    $page,
    $query,
    $category,
    $categories,
    $settings,
  ): void {
    ?>
    <div class="form search-page-form">
      <h2>Suchparameter</h2>

      <form method="get" action="<?= $page->url() ?>" role="search">
        <div class="form-row">
          <div class="form-column">
            <div class="form-field">
              <label for="search-page-query">Suchbegriff</label>
              <input
                id="search-page-query"
                type="search"
                name="q"
                value="<?= esc($query) ?>"
                placeholder="<?= esc(
                  (string) ($settings['placeholder'] ?? 'z. B. Typografie, Podcast, Kirb…'),
                ) ?>"
                required
              >
            </div>
          </div>

          <div class="form-column">
            <div class="form-field">
              <label for="search-page-category">Kategorie</label>
              <select id="search-page-category" name="category">
                <?php foreach ($categories as $key => $label): ?>
                  <option value="<?= $key ?>"<?= $key === $category ? ' selected' : '' ?>><?= esc(
  $label,
) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <button class="button-primary" type="submit">Suchen</button>
      </form>
    </div>
  <?php
  }; ?>
  <?php $searchResultCta = static fn(string $entity): string => match ($entity) {
    'episode' => 'Zur Folge',
    'participant' => 'Profil ansehen',
    'comment' => 'Zum Kommentar',
    default => 'Seite ansehen',
  }; ?>
  <?php $renderLeadTemplate = static function (
    string $template,
    int $count,
    string $query,
    string $categoryLabel,
  ): string {
    return strtr(esc($template), [
      '{count}' => esc((string) $count),
      '{query}' => esc($query),
      '{category}' => esc($categoryLabel),
    ]);
  }; ?>
  <article class="search-page card-grid content">
    <header class="page-header">
      <h1 class="title"><?= esc((string) $settings['result_title']) ?></h1>
      <p class="lead balance">
        <?php if ($query === ''): ?>
          Gib einen Begriff ein, um Treffer zu sehen.
        <?php elseif ($total > 0): ?>
          <?= $renderLeadTemplate(
            (string) $settings['result_lead_success'],
            $total,
            $query,
            $categories[$category],
          ) ?>
        <?php else: ?>
          <?= $renderLeadTemplate(
            (string) $settings['result_lead_empty'],
            $total,
            $query,
            $categories[$category],
          ) ?>
        <?php endif; ?>
      </p>
    </header>

    <?php if ($total === 0): ?>
      <ol class="card-grid-list">
        <li class="card-grid-item">
          <article>
            <div class="card-grid-link">
              <?php $renderSearchForm(); ?>
            </div>
          </article>
        </li>
      </ol>
    <?php else: ?>
      <ol class="card-grid-list">
        <li class="card-grid-item">
          <article>
            <div class="card-grid-link">
              <?php $renderSearchForm(); ?>
            </div>
          </article>
        </li>

        <?php foreach ($results as $result): ?>
          <?php $resultDate =
            $result['entity'] === 'episode' && $result['updatedTs'] > 0
              ? date('d.m.Y', $result['updatedTs'])
              : ''; ?>
          <li class="card-grid-item" data-entity="<?= esc($result['entity']) ?>">
            <article>
              <a class="card-grid-link" href="<?= esc($result['url']) ?>">
                <header>
                  <h3><?= $highlightQuery((string) $result['title'], $query) ?></h3>

                  <?php if ($result['subtitle'] !== ''): ?>
                    <div class="card-grid-subtitle"><?= $highlightQuery(
                      (string) $result['subtitle'],
                      $query,
                    ) ?></div>
                  <?php endif; ?>
                </header>

                <div class="card-grid-meta">
                  <div class="search-result-category">
                    <span class="msi-tag" aria-hidden="true"></span>
                    <span><?= esc($result['entityLabel']) ?></span>
                  </div>

                  <?php if ($resultDate !== ''): ?>
                    <div class="search-result-date">
                      <span class="msi-calendar" aria-hidden="true"></span>
                      <?= esc($resultDate) ?>
                    </div>
                  <?php endif; ?>
                </div>

                <?php if ($result['text'] !== ''): ?>
                  <p class="card-grid-teaser"><?= $highlightQuery(
                    (string) $result['text'],
                    $query,
                  ) ?></p>
                <?php endif; ?>

                <div>
                  <span class="button-primary" data-icon-position="right" aria-hidden="true">
                    <i class="msi-arrow-forward" aria-hidden="true"></i>
                    <span><?= esc($searchResultCta($result['entity'])) ?></span>
                  </span>
                </div>
              </a>
            </article>
          </li>
        <?php endforeach; ?>
      </ol>

      <?php if ($totalPages > 1): ?>
        <nav class="pagination-nav search-pagination" aria-label="Suchergebnisseiten">
          <div class="pagination-nav-slot pagination-nav-prev">
            <?php if ($currentPage > 1): ?>
              <a
                class="button"
                href="<?= esc($buildSearchPageUrl($currentPage - 1)) ?>"
                aria-label="Vorige Ergebnisseite"
              >
                <i class="msi-arrow-back" aria-hidden="true"></i>
                <span>Zurück</span>
              </a>
            <?php endif; ?>
          </div>

          <div class="pagination-nav-current">
            <span>Seite <?= $currentPage ?> von <?= $totalPages ?></span>
          </div>

          <div class="pagination-nav-slot pagination-nav-next">
            <?php if ($currentPage < $totalPages): ?>
              <a
                class="button button-primary"
                data-icon-position="right"
                href="<?= esc($buildSearchPageUrl($currentPage + 1)) ?>"
                aria-label="Nächste Ergebnisseite"
              >
                <i class="msi-arrow-forward" aria-hidden="true"></i>
                <span>Weiter</span>
              </a>
            <?php endif; ?>
          </div>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </article>
<?php endslot(); ?>

<?php endsnippet(); ?>
