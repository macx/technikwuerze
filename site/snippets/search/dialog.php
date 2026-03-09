<?php

declare(strict_types=1);

$searchPage = page('suche');
$searchUrl = $searchPage ? $searchPage->url() : url('suche');
$queryValue = trim((string) get('q'));
$selectedCategory = 'content';
$categories = twSearchCategories();
$settings = twSearchSettings();
?>
<dialog id="site-search-dialog" class="site-search-dialog" data-search-dialog>
  <form class="site-search-dialog-form" method="get" action="<?= $searchUrl ?>" role="search">
    <header class="site-search-dialog-header">
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

    <label class="site-search-dialog-label" for="site-search-dialog-query">Suchbegriff</label>
    <input
      id="site-search-dialog-query"
      class="site-search-dialog-input"
      type="search"
      name="q"
      value="<?= esc($queryValue) ?>"
      placeholder="<?= esc(
        (string) ($settings['placeholder'] ?? 'z. B. Typografie, Podcast, Kirb…'),
      ) ?>"
      required
      data-search-dialog-input
    >

    <div class="site-search-dialog-actions">
      <div class="theme-switch" data-enhanced="true">
        <label class="site-search-dialog-label" for="site-search-dialog-category">Kategorie</label>
        <select
          class="theme-switch-select"
          id="site-search-dialog-category"
          name="category"
          aria-label="Kategorie auswählen"
          title="Kategorie auswählen"
        >
          <?php foreach ($categories as $key => $label): ?>
            <option value="<?= $key ?>"<?= $key === $selectedCategory ? ' selected' : '' ?>><?= esc(
  $label,
) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <button class="button-primary" type="submit">Suchen</button>
    </div>
  </form>
</dialog>
