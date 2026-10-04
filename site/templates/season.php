<?php
/**
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Site $site
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Pages $pages
 */

$episodes = $page
  ->children()
  ->filterBy('intendedTemplate', 'episode')
  ->published()
  ->sortBy('date', 'desc');

snippet('layout', slots: true);
?>

<?php slot(); ?>
  <div class="page-header content">
    <?php snippet('breadcrumb'); ?>
    <h1 class="title">
      <?= $page->title()->html() ?>
    </h1>


    <?php if ($page->lead()->isNotEmpty()): ?>
      <p class="lead">
        <?= $page->lead()->kti() ?>
      </p>
    <?php endif; ?>

    <?php if ($seasonMeta = twSeasonMeta($page)): ?>
      <p class="season-meta"><?= esc($seasonMeta) ?></p>
    <?php endif; ?>
  </div>

  <?= $page->blocks()->toBlocks() ?>

  <section class="season content">
    <?php if ($episodes->isEmpty() && $page->upcomingNote()->isNotEmpty()): ?>
      <ul class="episodes-list">
        <li data-episode-number="→">
          <div class="episode-title">
            <?= $page->upcomingNote()->kti() ?>
          </div>
        </li>
      </ul>
    <?php endif; ?>

    <?php if ($episodes->isNotEmpty()): ?>
      <ul class="episodes-list">
        <?php foreach ($episodes as $episode): ?>
          <?php $episodeNumber = trim((string) $episode->podcasterepisodetotal()->value()); ?>
          <li<?php e(
            $episodeNumber !== '',
            ' data-episode-number="' . esc($episodeNumber) . '"',
          ); ?>>
            <a href="<?= $episode->url() ?>" class="episode-title">
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
    <?php endif; ?>
  </section>
<?php endslot(); ?>
<?php endsnippet(); ?>
