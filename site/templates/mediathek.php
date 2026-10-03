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
  ->sortBy('title', 'desc'); ?>

<?php snippet('layout', slots: true); ?>
  <?php slot(); ?>
    <div class="page-header content narrow">
      <h1 class="title">
        <?= $page->title()->html() ?>
      </h1>

      <p class="lead">
        <?= $page->lead()->kti() ?>
      </p>
    </div>

    <?= $page->blocks()->toBlocks() ?>

    <?php foreach ($seasons as $season): ?>
      <?php $seasonEpisodes = $season
        ->children()
        ->filterBy('intendedTemplate', 'episode')
        ->published()
        ->sortBy('date', 'desc'); ?>
      <?php $isAnnounced = $seasonEpisodes->isEmpty() && $season->announce()->toBool(); ?>
      <?php if ($seasonEpisodes->isNotEmpty() || $isAnnounced): ?>
        <section class="season content">
          <header class="section-header">
            <div>
              <h2>
                <?= $season->title()->html() ?>
              </h2>

              <?php if ($seasonMeta = twSeasonMeta($season)): ?>
                <span class="season-meta"><?= esc($seasonMeta) ?></span>
              <?php endif; ?>
            </div>

            <?php if (!$isAnnounced): ?>
              <a href="<?= $season->url() ?>" class="button" data-icon-position="right">
                <i class="msi-arrow-forward" aria-hidden="true"></i>
                Zur Phase
              </a>
            <?php endif; ?>
          </header>


          <?= $season->lead()->kt() ?>

          <?php if ($isAnnounced && $season->upcomingNote()->isNotEmpty()): ?>
            <p class="season-upcoming"><?= $season->upcomingNote()->kti() ?></p>
          <?php endif; ?>

          <ul class="episodes-list">
            <?php foreach ($seasonEpisodes as $episode): ?>
              <?php $episodeNumber = trim((string) $episode->podcasterepisodetotal()->value()); ?>
              <li<?php e(
                $episodeNumber !== '',
                ' data-episode-number="' . esc($episodeNumber) . '"',
              ); ?>>
                <a href="<?= $episode->url() ?>">
                  <?= $episode->title()->value() ?><br />
                  <span class="text-s">
                    <?php if ($episode->date()->isNotEmpty()): ?>
                      <span><?= $episode->date()->toDate('d.m.Y') ?></span>
                    <?php endif; ?>
                    <?php if ($episode->podcasterAudio()->isEmpty()): ?>
                      <span class="episode-unavailable">· nicht mehr verfügbar</span>
                    <?php endif; ?>
                  </span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </section>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php endslot(); ?>
<?php endsnippet(); ?>
