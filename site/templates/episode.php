<?php
/**
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Site $site
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Pages $pages
 */

$hosts = $page->podcasterhosts()->toPages();
$guests = $page->podcasterguests()->toPages();
$summary = $page->podcasterdescription()->isNotEmpty()
  ? $page->podcasterdescription()->kt()
  : '';

snippet('layout', slots: true);
?>

<?php slot() ?>
  <article class="episode-view">
    <header>
      <h1><?= $page->title()->html() ?></h1>
      <?php if ($page->podcastersubtitle()->isNotEmpty()): ?>
        <p><strong><?= $page->podcastersubtitle()->html() ?></strong></p>
      <?php endif; ?>

      <?php if ($page->date()->isNotEmpty()): ?>
        <p><strong>Datum:</strong> <?= $page->date()->toDate('d.m.Y H:i') ?></p>
      <?php endif; ?>
      <p>
        <strong>Staffel/Folge:</strong>
        S<?= $page->podcasterseason()->or('-') ?> · E<?= $page->podcasterepisode()->or('-') ?>
      </p>

      <?php if ($hosts->isNotEmpty()): ?>
        <p>
          <strong>Moderation:</strong>
          <?= esc(implode(', ', $hosts->pluck('title'))) ?>
        </p>
      <?php endif; ?>

      <?php if ($guests->isNotEmpty()): ?>
        <p>
          <strong>Gäste:</strong>
          <?= esc(implode(', ', $guests->pluck('title'))) ?>
        </p>
      <?php endif; ?>
    </header>

    <section class="episode-player">
      <?php snippet('podcaster-player', ['page' => $page]); ?>
    </section>

    <?php if ($summary !== ''): ?>
      <section>
        <h2>Zusammenfassung</h2>
        <?= $summary ?>
      </section>
    <?php endif; ?>

    <?php if ($page->text()->isNotEmpty()): ?>
      <section>
        <h2>Inhalt</h2>
        <?= $page->text()->kt() ?>
      </section>
    <?php endif; ?>

    <?php if ($page->commentsAreEnabled()): ?>
      <section class="episode-comments">
        <h2>Kommentare (<?= $page->commentCount() ?>)</h2>
        <?php snippet('komments/list/comments', ['page' => $page]); ?>
        <?php snippet('komments/kommentform', ['page' => $page]); ?>
      </section>
    <?php endif; ?>
  </article>
<?php endslot() ?>
<?php endsnippet(); ?>
