<?php
/**
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Site $site
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Pages $pages
 */

$hosts = $page->podcasterhosts()->toPages();
$guests = $page->podcasterguests()->toPages();
$summary = $page->podcasterdescription()->isNotEmpty() ? $page->podcasterdescription()->kt() : '';
$publishedDate = $page->date()->isNotEmpty() ? $page->date() : null;
$publishedDatetime = $publishedDate ? $publishedDate->toDate('c') : '';
$publishedLabel = $publishedDate ? $publishedDate->toDate('d.m.Y H:i') : '';
$reReleaseDate = $page->rerelease()->isNotEmpty() ? $page->rerelease() : null;
$updatedDatetime = $reReleaseDate ? $reReleaseDate->toDate('c') : '';
$updatedLabel = $reReleaseDate ? $reReleaseDate->toDate('d.m.Y H:i') : '';
if ($updatedLabel === '') {
  $updatedTimestamp = $page->modified();
  $updatedDatetime = $updatedTimestamp ? date('c', $updatedTimestamp) : '';
  $updatedLabel = $updatedTimestamp ? date('d.m.Y H:i', $updatedTimestamp) : '';
}
$episodeType = trim((string) $page->podcasterepisodetype()->value());
if ($episodeType === '') {
  $episodeType = '-';
}

snippet('layout', slots: true);
?>

<?php slot(); ?>
  <article class="episode-view content">
    <header class="page-header">
      <h1 class="title">
        <?= $page->title()->html() ?>
        <?php if ($page->podcastersubtitle()->isNotEmpty()): ?>
          <span class="subtitle">
            <?= $page->podcastersubtitle()->html() ?>
          </span>
        <?php endif; ?>
      </h1>


      <p>
        <strong>Meta:</strong>
        S<?= $page->podcasterseason()->or('-') ?> · E<?= $page->podcasterepisode()->or('-') ?>
        · Typ <?= esc($episodeType) ?>
        <?php if ($publishedLabel !== ''): ?>
          ·
          <span>
            Veröffentlicht
            <time itemprop="datePublished" datetime="<?= esc($publishedDatetime, 'attr') ?>">
              <?= esc($publishedLabel) ?>
            </time>
          </span>
        <?php endif; ?>
        <?php if ($updatedLabel !== ''): ?>
          ·
          <span>
            Aktualisiert
            <time itemprop="dateModified" datetime="<?= esc($updatedDatetime, 'attr') ?>">
              <?= esc($updatedLabel) ?>
            </time>
          </span>
        <?php endif; ?>
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

    <?php if ($page->podcasterAudio()->isNotEmpty()): ?>
      <section class="episode-player">
        <?php snippet('podcaster-player', ['page' => $page]); ?>
      </section>
    <?php endif; ?>

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
<?php endslot(); ?>
<?php endsnippet(); ?>
