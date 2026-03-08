<?php
/**
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Site $site
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Pages $pages
 */

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
$episodeTotal = trim((string) $page->podcasterepisodetotal()->value());
if ($episodeTotal === '') {
  $episodeTotal = '-';
}

$podloveTemplate = asset('assets/podlove/tw-player-template.html')->url();

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
    <li class="episode-participants-item" aria-label="<?= esc($displayName) ?>">
      <?php if ($image): ?>
        <img
          class="episode-participants-avatar"
          src="<?= $image->crop(192, 192)->url() ?>"
          alt="<?= esc($displayName) ?>"
          loading="lazy"
        >
      <?php else: ?>
        <span class="episode-participants-avatar episode-participants-avatar--fallback" aria-hidden="true">
          <?= esc($getInitials($participant)) ?>
        </span>
      <?php endif; ?>
    </li>
  <?php
  endforeach;
};

snippet('layout', slots: true);
?>

<?php slot(); ?>
  <article class="episode-view">
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
    </header>

    <?php if ($page->podcasterAudio()->isNotEmpty()): ?>
      <div class="content narrow">
        <?php snippet(
          'podcast-player',
          [
            'page' => $page,
            'template' => $podloveTemplate,
            'transparent' => true,
            'mediaPosition' => 'left',
          ],
          slots: true,
        ); ?>
          <?php slot(); ?>
            <div class="text-xs">
              <strong class="text-strong text-secondary">
                S<?= $page->podcasterseason()->or('-') ?>
                ·
                E<?= $page->podcasterepisode()->or('-') ?>
                · #<?= esc($episodeTotal) ?>
              </strong><br />
              <span class="text-light">
                <?= esc($episodeTypeLabel) ?>
              </span>
            </div>

            <?php if ($hosts->isNotEmpty() || $guests->isNotEmpty()): ?>
              <div class="episode-participants" aria-label="Mitwirkende">
                <?php if ($hosts->isNotEmpty()): ?>
                  <div class="episode-participants-row">
                    <strong class="text-xs text-strong text-secondary">
                      Moderation
                    </strong>

                    <ul class="episode-participants-list">
                      <?php $renderParticipantAvatars($hosts); ?>
                    </ul>
                  </div>
                <?php endif; ?>
                <?php if ($guests->isNotEmpty()): ?>
                  <div class="episode-participants-row">
                    <strong class="text-xs text-strong text-secondary">
                      Gäste
                    </strong>

                    <ul class="episode-participants-list">
                      <?php $renderParticipantAvatars($guests); ?>
                    </ul>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          <?php endslot(); ?>
        <?php endsnippet(); ?>
      </div>
    <?php endif; ?>

    <?php if ($page->text()->isNotEmpty()): ?>
      <section class="episode-text content narrow">
        <?= $page->text()->kt() ?>
      </section>
    <?php endif; ?>

    <?php if ($page->commentsAreEnabled()): ?>
      <section class="episode-comments content medium">
        <h2>Kommentare (<?= $page->commentCount() ?>)</h2>
        <?php snippet('komments/list/comments', ['page' => $page]); ?>
        <?php snippet('komments/kommentform', ['page' => $page]); ?>
      </section>
    <?php endif; ?>
  </article>
<?php endslot(); ?>
<?php endsnippet(); ?>
