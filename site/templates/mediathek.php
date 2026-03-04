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
    <div class="page-header">
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
      <?php if ($seasonEpisodes->isNotEmpty()): ?>
        <section class="season">
          <header class="section-header">
            <h2>
              <?= $season->title()->html() ?>
            </h2>

            <a href="<?= $season->url() ?>" class="button" data-icon-position="right">
              <i class="msi-arrow-forward" aria-hidden="true"></i>
              Zur Staffel
            </a>
          </header>

          <?= $season->lead()->kt() ?>

          <ul class="season-list">
            <?php foreach ($seasonEpisodes as $episode): ?>
              <li>
                <a href="<?= $episode->url() ?>">
                  <?= $episode->title()->value() ?>
                </a><br />

                <div class="text-small">
                  <?= $episode->podcastersubtitle()->value() ?>
                  /
                  <?php if ($episode->date()->isNotEmpty()): ?>
                    <span><?= $episode->date()->toDate('d.m.Y') ?></span>
                  <?php endif; ?>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </section>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php endslot(); ?>
<?php endsnippet(); ?>
