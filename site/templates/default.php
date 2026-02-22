<?php snippet('layout', slots: true); ?>
<?php slot('header') ?>
  <?php snippet('header'); ?>
<?php endslot() ?>

<?php slot() ?>
  <?= $page->text()->kt() ?>
<?php endslot() ?>
<?php endsnippet(); ?>
