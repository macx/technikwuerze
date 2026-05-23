<?php snippet('layout', slots: true); ?>

  <?php slot(); ?>
    <div class="page-header content narrow">
      <h1 class="title"><?= $page->title()->html() ?></h1>
    </div>

    <div class="page-content content-text content narrow">
      <?= $page->text()->toBlocks() ?>
    </div>
  <?php endslot(); ?>

<?php endsnippet(); ?>
