<?php

/**
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Site $site
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Pages $pages
 */

$seasons = $page
  ->children()
  ->filterBy('intendedTemplate', 'season')
  ->published()
  ->sortBy('title', 'asc'); ?>

<?php snippet('layout', slots: true); ?>
  <?php slot(); ?>
    <?php foreach ($seasons as $season): ?>
      <?php $episodes = $season
        ->children()
        ->filterBy('intendedTemplate', 'episode')
        ->published()
        ->sortBy('date', 'desc'); ?>
      <section>
        <h2><a href="<?= $season->url() ?>"><?= $season->title()->html() ?></a></h2>
        <?php foreach ($episodes as $episode): ?>
          <article>
            <h3>
              <a href="<?= $episode->url() ?>"><?= $episode->title()->html() ?></a>
              <?php if ($episode->date()->isNotEmpty()): ?>
                (<?= $episode->date()->toDate('d.m.Y') ?>)
              <?php endif; ?>
            </h3>
            <?php snippet('podcaster-player', ['page' => $episode]); ?>
          </article>
        <?php endforeach; ?>
      </section>
    <?php endforeach; ?>
  <?php endslot(); ?>

<?php endsnippet(); ?>
