<?php
$episodes = page('mediathek')
  ?->index()
  ->filterBy('intendedTemplate', 'episode')
  ->published()
  ->sortBy('date', 'desc') ?? new Kirby\Cms\Pages([]);

$latest = $episodes->first();
$recent = $episodes->offset(1)->limit(3);
$popularManual = $page->popularEpisodes()->toPages()->published();
$popular = $popularManual->count() > 0 ? $popularManual->limit(3) : $episodes->limit(3);

$mediathekUrl = page('mediathek')?->url() ?? '#';
$teamUrl = page('team')?->url() ?? '#';
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
      <?php if ($latest): ?>
        <section>
          <h2>Aktuelle Folge</h2>
          <article>
            <h3><a href="<?= $latest->url() ?>"><?= $latest->title()->html() ?></a></h3>
            <?php if ($latest->date()->isNotEmpty()): ?>
              <p><?= $latest->date()->toDate('d.m.Y') ?></p>
            <?php endif ?>
            <p><?= $latest->text()->excerpt(180) ?></p>
            <?php snippet('podcaster-player', ['page' => $latest]); ?>
          </article>
        </section>
      <?php endif ?>

      <section>
        <h2>Aktuelle Folgen</h2>
        <?php foreach ($recent as $episode): ?>
          <article>
            <h3>
              <a href="<?= $episode->url() ?>"><?= $episode->title()->html() ?></a>
            </h3>
            <?php if ($episode->date()->isNotEmpty()): ?>
              <p><?= $episode->date()->toDate('d.m.Y') ?></p>
            <?php endif ?>
            <?php snippet('podcaster-player', ['page' => $episode]); ?>
          </article>
        <?php endforeach ?>
      </section>

      <section>
        <h2>Beliebteste Folgen</h2>
        <?php foreach ($popular as $episode): ?>
          <article>
            <h3>
              <a href="<?= $episode->url() ?>"><?= $episode->title()->html() ?></a>
            </h3>
            <?php snippet('podcaster-player', ['page' => $episode]); ?>
          </article>
        <?php endforeach ?>
      </section>
    </main>
  </body>
</html>
