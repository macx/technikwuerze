<?php
$mediathekUrl = page('mediathek')?->url() ?? '#';
$teamUrl = page('team')?->url() ?? '#';
$seasons = $page->children()->filterBy('intendedTemplate', 'season')->published()->sortBy('title', 'asc');
?>
<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $page->title() ?> | <?= $site->title() ?></title>
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
      <?= $page->text()->kt() ?>
    </header>

    <main>
      <?php foreach ($seasons as $season): ?>
        <?php $episodes = $season->children()->filterBy('intendedTemplate', 'episode')->published()->sortBy('date', 'desc'); ?>
        <section>
          <h2><a href="<?= $season->url() ?>"><?= $season->title()->html() ?></a></h2>
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
        </section>
      <?php endforeach ?>
    </main>
  </body>
</html>
