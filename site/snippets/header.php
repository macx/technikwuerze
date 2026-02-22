<?php
$navItems = $site->children()->listed(); ?>
<header class="main-header">
  <a href="<?= $site->url() ?>" class="website-title">
    Technikwürze
  </a>

  <nav aria-label="Hauptnavigation" id="main-nav">
    <ul id="main-nav-list">
      <?php foreach ($navItems as $item): ?>
        <li>
          <a href="<?= $item->url() ?>"<?= e($item->isOpen(), ' aria-current="page"') ?>>
            <?= $item->title()->html() ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>

    <template id="main-nav-button">
      <button type="button" class="main-nav-button" aria-expanded="false" aria-label="Menü" aria-controls="main-nav-list">
        <i class="msi-menu"></i>
      </button>
    </template>
  </nav>

  <div class="header-tools">
    <?php snippet('theme-switch'); ?>
  </div>
</header>
