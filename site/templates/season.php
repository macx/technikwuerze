<?php
/**
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Site $site
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Pages $pages
 */

$episodes = $page->children()->filterBy('intendedTemplate', 'episode')->published()->sortBy('date', 'desc');

snippet('layout/podcast', slots: true);
?>

<?php slot() ?>
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
<?php endslot() ?>
<?php endsnippet(); ?>
