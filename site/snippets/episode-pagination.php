<?php
/**
 * @var Kirby\Cms\Page $page  The current episode page
 */

$season = $page->parent();

$prevEpisode = $page->prevListed();
$prevCrossSeason = false;
if (!$prevEpisode) {
  $prevSeason = $season->prevListed();
  if ($prevSeason) {
    $prevEpisode = $prevSeason->children()->listed()->last();
    $prevCrossSeason = true;
  }
}

$nextEpisode = $page->nextListed();
$nextCrossSeason = false;
if (!$nextEpisode) {
  $nextSeason = $season->nextListed();
  if ($nextSeason) {
    $nextEpisode = $nextSeason->children()->listed()->first();
    $nextCrossSeason = true;
  }
}

$episodeShortLabel = static function (Kirby\Cms\Page $ep): string {
  $s = trim((string) $ep->podcasterseason()->value());
  $e = trim((string) $ep->podcasterepisode()->value());
  $parts = [];
  if ($s !== '') {
    $parts[] = 'P' . $s;
  }
  if ($e !== '') {
    $parts[] = 'E' . $e;
  }
  return $parts !== [] ? implode(' · ', $parts) : esc($ep->title());
};

$episodeA11yDetail = static function (Kirby\Cms\Page $ep): string {
  $s = trim((string) $ep->podcasterseason()->value());
  $e = trim((string) $ep->podcasterepisode()->value());
  $parts = [];
  if ($s !== '') {
    $parts[] = 'Phase ' . $s;
  }
  if ($e !== '') {
    $parts[] = 'Folge ' . $e;
  }
  $detail = $parts !== [] ? implode(', ', $parts) . ' – ' . esc($ep->title()) : esc($ep->title());
  return $detail;
};

$prevVisualLabel = $prevEpisode ? $episodeShortLabel($prevEpisode) : '';
$prevAriaLabel = $prevEpisode ? 'Vorige Folge: ' . $episodeA11yDetail($prevEpisode) : '';

$nextVisualLabel = $nextEpisode ? $episodeShortLabel($nextEpisode) : '';
$nextAriaLabel = $nextEpisode ? 'Nächste Folge: ' . $episodeA11yDetail($nextEpisode) : '';

$currentVisualLabel = (static function (Kirby\Cms\Page $ep): string {
  $s = trim((string) $ep->podcasterseason()->value());
  $e = trim((string) $ep->podcasterepisode()->value());
  $total = trim((string) $ep->podcasterepisodetotal()->value());
  $parts = [];
  if ($s !== '') {
    $parts[] = "Phase\u{00A0}" . $s;
  }
  if ($e !== '') {
    $parts[] = "Episode\u{00A0}" . $e;
  }
  if ($total !== '') {
    $parts[] = "Technikwürze\u{00A0}" . $total;
  }
  return implode(' · ', $parts);
})($page);
?>
<nav class="pagination-nav content medium" aria-label="Navigation zwischen Folgen">
  <div class="pagination-nav-slot pagination-nav-prev">
    <?php if ($prevEpisode): ?>
      <a
        href="<?= $prevEpisode->url() ?>"
        class="button"
        aria-label="<?= $prevAriaLabel ?>"
      >
        <i class="msi-arrow-back" aria-hidden="true"></i>
        <span aria-hidden="true"><?= $prevVisualLabel ?></span>
      </a>
    <?php endif; ?>
  </div>

  <div class="pagination-nav-current">
    <span><?= $currentVisualLabel ?></span>
  </div>

  <div class="pagination-nav-slot pagination-nav-next">
    <?php if ($nextEpisode): ?>
      <a
        href="<?= $nextEpisode->url() ?>"
        class="button button-primary"
        data-icon-position="right"
        aria-label="<?= $nextAriaLabel ?>"
      >
        <i class="msi-arrow-forward" aria-hidden="true"></i>
        <span aria-hidden="true"><?= $nextVisualLabel ?></span>
      </a>
    <?php endif; ?>
  </div>
</nav>
