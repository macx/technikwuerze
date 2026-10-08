<?php
/**
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Site $site
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Pages $pages
 */

use Kirby\Toolkit\Str;

$hosts = $page->podcasterhosts()->toPages();
$guests = $page->podcasterguests()->toPages();
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
$episodeTypeLabel = $page->episodeTypeLabel();
$downloadFile = (new \mauricerenck\Podcaster\Podcast())->getAudioFile($page) ?: null;
$episodeTypeNote =
  $episodeTypeLabel !== '-' ? esc($episodeTypeLabel) . ($downloadFile !== null ? ',' : '') : '';
$episodeTotal = trim((string) $page->podcasterepisodetotal()->value());
if ($episodeTotal === '') {
  $episodeTotal = '-';
}

$contentBlocks = $page
  ->blocks()
  ->toBlocks()
  ->map(function ($block) {
    if (
      $block->type() === 'tw-transcript' &&
      trim((string) $block->headline()->value()) === 'Transkript'
    ) {
      $block->content()->update(['headline' => 'Transkript der Folge']);
    }

    return $block;
  })
  ->collectFootnotes();
$transcriptWordsUrl = $page->transcriptWordsUrl();
$footnotesHtml = count(Footnotes::$footnotes) > 0 ? Footnotes::footnotes() : '';

$getInitials = static function (Kirby\Cms\Page $participant): string {
  $firstName = trim((string) $participant->first_name()->value());
  $lastName = trim((string) $participant->last_name()->value());

  $initials = '';
  if ($firstName !== '') {
    $firstNameParts = preg_split('/\s+/u', $firstName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    foreach ($firstNameParts as $part) {
      $initials .= mb_strtoupper(mb_substr($part, 0, 1));
    }
  }

  if ($lastName !== '') {
    $initials .= mb_strtoupper(mb_substr($lastName, 0, 1));
  }

  if ($initials !== '') {
    return $initials;
  }

  $title = trim((string) $participant->title()->value());
  if ($title === '') {
    return '?';
  }

  $titleParts = preg_split('/\s+/u', $title, -1, PREG_SPLIT_NO_EMPTY) ?: [];
  $fallback = '';
  foreach (array_slice($titleParts, 0, 3) as $part) {
    $fallback .= mb_strtoupper(mb_substr($part, 0, 1));
  }

  return $fallback !== '' ? $fallback : '?';
};

$renderParticipantAvatars = static function (Kirby\Cms\Pages $participants) use (
  $getInitials,
): void {
  foreach ($participants as $participant):

    $fullName = trim(
      (string) ($participant->first_name()->value() . ' ' . $participant->last_name()->value()),
    );
    $displayName = $fullName !== '' ? $fullName : $participant->title()->value();
    $image = $participant->profile_image()->toFile();
    ?>
    <li class="episode-participants-item">
      <a class="episode-participants-link" href="<?= $participant->url() ?>">
        <?php if ($image): ?>
          <img
            class="episode-participants-avatar"
            src="<?= $image->crop(192, 192)->url() ?>"
            alt=""
            aria-hidden="true"
            loading="lazy"
          >
        <?php else: ?>
          <span class="episode-participants-avatar episode-participants-avatar-fallback" aria-hidden="true">
            <?= esc($getInitials($participant)) ?>
          </span>
        <?php endif; ?>
        <span class="episode-participants-name"><?= esc($displayName) ?></span>
      </a>
    </li>
  <?php
  endforeach;
};

snippet('layout', slots: true);
?>

<?php slot(); ?>
  <article class="episode-view">
    <?php if ($publishedDatetime !== ''): ?>
      <script type="application/ld+json"><?= json_encode(
        array_filter([
          '@context' => 'https://schema.org',
          '@type' => 'PodcastEpisode',
          'name' => $page->title()->value(),
          'url' => $page->url(),
          'datePublished' => $publishedDatetime,
          'dateModified' => $reReleaseDate ? $updatedDatetime : null,
          'description' => $page->podcasterdescription()->isNotEmpty()
            ? trim(strip_tags((string) $page->podcasterdescription()->kti()))
            : null,
          'episodeNumber' => ctype_digit($episodeTotal) ? (int) $episodeTotal : null,
          'inLanguage' => 'de',
          'partOfSeries' => [
            '@type' => 'PodcastSeries',
            'name' => $site->title()->value(),
            'url' => $page->parent()?->parent()?->url() ?? $site->url(),
          ],
        ]),
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG,
      ) ?></script>
    <?php endif; ?>
    <header class="page-header content">
      <h1 class="title">
        <?= $page->title()->html() ?>
        <?php if ($page->podcastersubtitle()->isNotEmpty()): ?>
          <span class="subtitle">
            <?= $page->podcastersubtitle()->html() ?>
          </span>
        <?php endif; ?>
      </h1>

      <?php if ($page->podcasterdescription()->isNotEmpty()): ?>
        <p class="lead">
          <?= $page->podcasterdescription()->kti() ?>
        </p>
      <?php endif; ?>
      <?php snippet('breadcrumb', ['visible' => false]); ?>
    </header>

    <div class="episode-sections">
      <?php if ($page->podcasterAudio()->isNotEmpty()): ?>
        <div class="episode-player-wrapper">
          <div class="content narrow">
          <?php snippet(
            'podcast-player',
            [
              'page' => $page,
              'mediaPosition' => 'left',
            ],
            slots: true,
          ); ?>
          <?php slot(); ?>
            <div class="episode-info">
              <?php foreach (
                ['Moderation' => $hosts, 'Gäste' => $guests]
                as $groupLabel => $groupParticipants
              ): ?>
                <?php if ($groupParticipants->isNotEmpty()): ?>
                  <div class="episode-participants" role="group" aria-labelledby="participants-<?= Str::slug(
                    $groupLabel,
                  ) ?>">
                    <strong class="text-eyebrow" id="participants-<?= Str::slug($groupLabel) ?>">
                      <?= $groupLabel ?>
                    </strong>

                    <ul class="episode-participants-list">
                      <?php $renderParticipantAvatars($groupParticipants); ?>
                    </ul>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          <?php endslot(); ?>

          <?php if ($downloadFile !== null || $episodeTypeNote !== ''): ?>
            <?php slot('mediaNote'); ?>
              <?= $episodeTypeNote ?>
              <?php if ($downloadFile !== null): ?>
                <a
                  href="<?= $page->url() .
                    '/' .
                    option('mauricerenck.podcaster.downloadTriggerPath', 'download') .
                    '/' .
                    $downloadFile->filename() ?>"
                  download
                >Download (MP3, <?= max(
                  1,
                  (int) round($downloadFile->size() / 1048576),
                ) ?>&nbsp;MB)</a>
              <?php endif; ?>
            <?php endslot(); ?>
          <?php endif; ?>
        <?php endsnippet(); ?>
        </div>
      <?php endif; ?>

      <?php if ($contentBlocks !== ''): ?>
        <section
          class="content-text content narrow"
          <?= $transcriptWordsUrl
            ? 'data-transcript-words="' . esc($transcriptWordsUrl, 'attr') . '"'
            : '' ?>
        >
          <?= $contentBlocks ?>

          <?php if ($footnotesHtml !== ''): ?>
            <?= $footnotesHtml ?>
          <?php endif; ?>
        </section>
      <?php endif; ?>
    </div>

    <?php if ($page->commentsAreEnabled()): ?>
      <hr />

      <?php
      $actualComments = $page->comments()->filterBy('type', 'comment');
      $actualMentions = $page->comments()->filterBy('type', '!=', 'comment');
      ?>

      <?php if ($actualMentions->count() > 0): ?>
      <section class="episode-mentions content medium">
        <h2 class="comments-headline section-headline">
          Erwähnungen
          <span class="count-bubble"><?= $actualMentions->count() ?></span>
        </h2>

        <div class="mentions-grid">
          <?php snippet('komments/list/likes', ['page' => $page]); ?>
          <?php snippet('komments/list/reposts', ['page' => $page]); ?>
        </div>

        <div class="mentions-list">
          <?php snippet('komments/list/mentions', ['page' => $page]); ?>
          <?php snippet('komments/list/replies', ['page' => $page]); ?>
        </div>
      </section>
      <?php endif; ?>

      <section class="episode-comments content medium">
        <h2 class="comments-headline section-headline">
          Kommentare
          <span class="count-bubble"><?= $actualComments->count() ?></span>
        </h2>

        <?php snippet('komments/list/comments', ['page' => $page]); ?>
        <?php snippet('komments/kommentform', ['page' => $page]); ?>
      </section>
    <?php endif; ?>

    <?php snippet('episode-pagination', ['page' => $page]); ?>
  </article>
<?php endslot(); ?>
<?php endsnippet(); ?>
