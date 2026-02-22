<?php
$withOgAudio = $withOgAudio ?? false;

snippet('layout', slots: true);
?>
<?php slot('head') ?>
  <?php if ($withOgAudio): ?>
    <?php snippet('podcaster-ogaudio'); ?>
  <?php endif ?>
<?php endslot() ?>

<?php slot('header') ?>
  <?php snippet('header'); ?>
<?php endslot() ?>

<?php slot() ?>
  <?= $slot ?>
<?php endslot() ?>
<?php endsnippet(); ?>
