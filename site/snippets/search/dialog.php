<?php

declare(strict_types=1);

$searchPage = page('suche');
$searchUrl = $searchPage ? $searchPage->url() : url('suche');
$queryValue = trim((string) get('q'));
$selectedCategory = 'content';
$categories = twSearchCategories();
$settings = twSearchSettings();
?>
<dialog id="search-dialog" class="card light form search-dialog" data-search-dialog style="--color-tone: var(--clr-secondary);">
  <header>
    <h2><?= esc((string) ($settings['dialog_title'] ?? 'Suche')) ?></h2>

    <button
      class="main-nav-button"
      type="button"
      aria-label="Suche schließen"
      data-search-dialog-close
    >
      <i class="msi-close"></i>
    </button>
  </header>

  <form method="get" action="<?= $searchUrl ?>" role="search">
    <div class="form-row">
      <div class="form-column" style="--span:4">
        <div class="form-field">
          <label for="search-dialog-query">Suchbegriff</label>
          <input
          id="search-dialog-query"
          type="search"
          name="q"
          value="<?= esc($queryValue) ?>"
          placeholder="<?= esc(
            (string) ($settings['placeholder'] ?? 'z. B. Typografie, Podcast, Kirb…'),
          ) ?>"
            required
            data-search-dialog-input
          >
        </div>
      </div>

      <div class="form-column" style="--span:2">
        <div class="form-field">
          <label for="search-dialog-category">Kategorie</label>
          <select
          id="search-dialog-category"
          name="category"
          >
          <?php foreach ($categories as $key => $label): ?>
            <option value="<?= $key ?>"<?= $key === $selectedCategory ? ' selected' : '' ?>><?= esc(
  $label,
) ?></option>
          <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>

    <button class="button-primary" type="submit">Suchen</button>
  </form>
</dialog>
