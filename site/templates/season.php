<?php
$mediathekUrl = page('mediathek')?->url() ?? '#';
$teamUrl = page('team')?->url() ?? '#';
$episodes = $page->children()->filterBy('intendedTemplate', 'episode')->published()->sortBy('date', 'desc');
?>
<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $page->title() ?> | <?= $site->title() ?></title>
    <?= vite()->css('src/index.ts', [], true) ?>
    <?= vite()->js('src/index.ts', [], true) ?>
  </head>
  <body>
    <header>
      <nav>
        <a href="<?= $site->url() ?>">Start</a> /
        <a href="<?= $mediathekUrl ?>">Mediathek</a> /
        <a href="<?= $teamUrl ?>">Team</a>
      </nav>
      <h1><?= $page->title() ?></h1>
      <?= $page->text()->kt() ?>
    </header>

    <main>
      <?php foreach ($episodes as $episode): ?>
        <article>
          <h3>
            <a href="<?= $episode->url() ?>"><?= $episode->title()->html() ?></a>
            <?php if ($episode->date()->isNotEmpty()): ?>
              (<?= $episode->date()->toDate('d.m.Y') ?>)
            <?php endif ?>
          </h3>
          <?php snippet('podcaster-player', ['page' => $episode]); ?>
        </article>
      <?php endforeach ?>
    </main>
  </body>
</html>
