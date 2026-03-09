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
  <article class="search-page content medium">
    <header class="page-header">
      <h1 class="title">Suche</h1>
      <p class="lead">Durchsuche Inhalte, Episoden, Teilnehmende und Kommentare.</p>
    </header>

    <form class="search-page-form" method="get" action="<?= $page->url() ?>" role="search">
      <label class="search-page-label" for="search-page-query">Suchbegriff</label>
      <input
        id="search-page-query"
        class="search-page-input"
        type="search"
        name="q"
        value="<?= esc($query) ?>"
        placeholder="<?= esc(
          (string) ($settings['placeholder'] ?? 'z. B. Typografie, Podcast, Kirb…'),
        ) ?>"
        required
      >

      <div class="theme-switch" data-enhanced="true">
        <label class="search-page-label" for="search-page-category">Kategorie</label>
        <select
          class="theme-switch-select"
          id="search-page-category"
          name="category"
          aria-label="Kategorie auswählen"
          title="Kategorie auswählen"
        >
          <?php foreach ($categories as $key => $label): ?>
            <option value="<?= $key ?>"<?= $key === $category ? ' selected' : '' ?>><?= esc(
  $label,
) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <button type="submit" class="button-primary">Suchen</button>
    </form>

    <?php if ($query === ''): ?>
      <p class="search-page-state">Gib einen Begriff ein, um Treffer zu sehen.</p>
    <?php elseif ($total === 0): ?>
      <p class="search-page-state">Keine Treffer für „<?= esc($query) ?>“ in „<?= esc(
  $categories[$category],
) ?>“.</p>
    <?php else: ?>
      <p class="search-page-state"><?= $total ?> Treffer für „<?= esc($query) ?>“ in „<?= esc(
  $categories[$category],
) ?>“.</p>

      <ol class="search-results-list">
        <?php foreach ($results as $result): ?>
          <li class="search-result-item search-result-item--<?= esc($result['entity']) ?>">
            <a class="search-result-link" href="<?= esc($result['url']) ?>">
              <span class="search-result-type"><?= esc($result['entityLabel']) ?></span>
              <h2 class="search-result-title"><?= $highlightQuery(
                (string) $result['title'],
                $query,
              ) ?></h2>
              <?php if ($result['subtitle'] !== ''): ?>
                <p class="search-result-subtitle"><?= $highlightQuery(
                  (string) $result['subtitle'],
                  $query,
                ) ?></p>
              <?php endif; ?>
              <?php if ($result['text'] !== ''): ?>
                <p class="search-result-text"><?= $highlightQuery(
                  (string) $result['text'],
                  $query,
                ) ?></p>
              <?php endif; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ol>

      <?php if ($totalPages > 1): ?>
        <nav class="search-pagination" aria-label="Suchergebnisseiten">
          <?php if ($currentPage > 1): ?>
            <a class="button-secondary" href="<?= esc(
              $buildSearchPageUrl($currentPage - 1),
            ) ?>">Zurück</a>
          <?php endif; ?>

          <span class="search-pagination-info">Seite <?= $currentPage ?> von <?= $totalPages ?></span>

          <?php if ($currentPage < $totalPages): ?>
            <a class="button-secondary" href="<?= esc(
              $buildSearchPageUrl($currentPage + 1),
            ) ?>">Weiter</a>
          <?php endif; ?>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </article>
<?php endslot(); ?>

<?php endsnippet(); ?>
