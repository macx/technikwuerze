<?php snippet('layout/podcast', ['withOgAudio' => true], slots: true); ?>

<?php slot() ?>
  <?php if ($page->date()->isNotEmpty()): ?>
    <p><strong>Datum:</strong> <?= $page->date()->toDate('d.m.Y H:i') ?></p>
  <?php endif ?>

  <?= $page->text()->kt() ?>
  <?php snippet('podcaster-player'); ?>
<?php endslot() ?>
<?php endsnippet(); ?>
