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
  <div class="page-header">
    <h1 class="title">
      <?= $page->title()->html() ?>
    </h1>

    <?php if ($page->lead()->isNotEmpty()): ?>
      <p class="lead">
        <?= $page->lead()->kti() ?>
      </p>
    <?php endif; ?>
  </div>

  <?= $page->blocks()->toBlocks() ?>

  <?php if ($episodes->isNotEmpty()): ?>
    <section class="season content">
      <ul class="season-list">
        <?php foreach ($episodes as $episode): ?>
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
<?php endslot(); ?>
<?php endsnippet(); ?>
