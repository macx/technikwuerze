<?php

/**
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Site $site
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Pages $pages
 */

$allEpisodes = $page
  ->index()
  ->filterBy('intendedTemplate', 'episode')
  ->published()
  ->sortBy('date', 'desc', 'podcasterseason', 'desc', 'podcasterepisode', 'desc');

$featuredEpisodes = $allEpisodes->limit(3);
$featuredIds = $featuredEpisodes->pluck('id');

$seasons = $page
  ->children()
  ->filterBy('intendedTemplate', 'season')
  ->published()
  ->sortBy('title', 'desc');
?>

<?php snippet('layout', slots: true); ?>
  <?php slot(); ?>
    <div class="mediathek-view">
      <section class="mediathek-featured">
        <h2>Die letzten drei Folgen</h2>
        <?php foreach ($featuredEpisodes as $episode): ?>
          <article class="mediathek-featured-item">
            <header class="mediathek-featured-header">
              <h3><a href="<?= $episode->url() ?>"><?= $episode->title()->html() ?></a></h3>
              <?php if ($episode->date()->isNotEmpty()): ?>
                <p><?= $episode->date()->toDate('d.m.Y') ?></p>
              <?php endif; ?>
            </header>
            <?php snippet('podcaster-player', ['page' => $episode]); ?>
          </article>
        <?php endforeach; ?>
      </section>

      <?php foreach ($seasons as $season): ?>
        <?php $seasonEpisodes = $season
          ->children()
          ->filterBy('intendedTemplate', 'episode')
          ->published()
          ->sortBy('date', 'desc'); ?>
        <?php if ($seasonEpisodes->isNotEmpty()): ?>
          <section class="mediathek-list">
            <h2><a href="<?= $season->url() ?>"><?= $season->title()->html() ?></a></h2>
            <ul>
              <?php foreach ($seasonEpisodes as $episode): ?>
                <li>
                  <a href="<?= $episode->url() ?>">
                    <?= $episode->title()->html() ?>
                  </a>
                  <?php if ($episode->date()->isNotEmpty()): ?>
                    <span><?= $episode->date()->toDate('d.m.Y') ?></span>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </section>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  <?php endslot(); ?>

<?php endsnippet(); ?>
