<?php snippet('layout', slots: true); ?>

  <?php slot(); ?>
    <?= $page->text()->toBlocks() ?>
  <?php endslot(); ?>

<?php endsnippet(); ?>
