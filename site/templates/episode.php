<?php
$mediathekUrl = page('mediathek')?->url() ?? '#';
$teamUrl = page('team')?->url() ?? '#';
?>
<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $page->title() ?> | <?= $site->title() ?></title>
    <?php snippet('podcaster-ogaudio'); ?>
    <?= vite()->css('src/index.ts') ?>
    <?= vite()->js('src/index.ts') ?>
  </head>
  <body>
    <header>
      <nav>
        <a href="<?= $site->url() ?>">Start</a> /
        <a href="<?= $mediathekUrl ?>">Mediathek</a> /
        <a href="<?= $teamUrl ?>">Team</a>
      </nav>
      <h1><?= $page->title() ?></h1>
    </header>

    <main>
      <?php if ($page->date()->isNotEmpty()): ?>
        <p><strong>Datum:</strong> <?= $page->date()->toDate('d.m.Y H:i') ?></p>
      <?php endif ?>

      <?= $page->text()->kt() ?>
      <?php snippet('podcaster-player'); ?>
    </main>
  </body>
</html>
