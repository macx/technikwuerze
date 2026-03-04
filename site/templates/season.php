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
  <div class="season-view">
    <h1>
      <?= $page->title()->html() ?>
    </h1>

    <p>
      <?= $page->text()->value() ?>
    </p>

    <?php if ($episodes->isNotEmpty()): ?>
      <ul class="season-episodes-list">
        <?php foreach ($episodes as $episode): ?>
          <li>
            <div class="season-episodes-main">
              <a href="<?= $episode->url() ?>"><?= $episode->title()->html() ?></a>
              <?php if ($episode->podcastersubtitle()->isNotEmpty()): ?>
                <span class="mediathek-subtitle"><?= $episode->podcastersubtitle()->html() ?></span>
              <?php endif; ?>
            </div>
            <?php if ($episode->date()->isNotEmpty()): ?>
              <span class="mediathek-date"><?= $episode->date()->toDate('d.m.Y') ?></span>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
<?php endslot(); ?>
<?php endsnippet(); ?>
