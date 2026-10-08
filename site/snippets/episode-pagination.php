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
  $total = trim((string) $ep->podcasterepisodetotal()->value());
  $parts = [];
  if ($s !== '') {
    $parts[] = 'P' . $s;
  }
  if ($total !== '') {
    $parts[] = 'TW' . $total;
  }
  return $parts !== [] ? implode("\u{2002}", $parts) : esc($ep->title());
};

$episodeA11yDetail = static function (Kirby\Cms\Page $ep): string {
  $s = trim((string) $ep->podcasterseason()->value());
  $total = trim((string) $ep->podcasterepisodetotal()->value());
  $parts = [];
  if ($s !== '') {
    $parts[] = 'Phase ' . $s;
  }
  if ($total !== '') {
    $parts[] = 'Technikwürze ' . $total;
  }

  if ($parts === []) {
    return '';
  }

  return ' (' . implode(', ', $parts) . ') – ' . esc($ep->title());
};

$prevVisualLabel = $prevEpisode ? $episodeShortLabel($prevEpisode) : '';
$prevA11yDetail = $prevEpisode ? $episodeA11yDetail($prevEpisode) : '';

$nextVisualLabel = $nextEpisode ? $episodeShortLabel($nextEpisode) : '';
$nextA11yDetail = $nextEpisode ? $episodeA11yDetail($nextEpisode) : '';

$currentVisualLabel = (static function (Kirby\Cms\Page $ep): string {
  $s = trim((string) $ep->podcasterseason()->value());
  $total = trim((string) $ep->podcasterepisodetotal()->value());
  $parts = [];
  if ($s !== '') {
    $parts[] = "Phase\u{00A0}" . $s;
  }
  if ($total !== '') {
    $parts[] = "Technikwürze\u{00A0}" . $total;
  }
  return implode(', ', $parts);
})($page);
?>
<nav class="pagination-nav content medium" aria-label="Navigation zwischen Folgen">
  <div class="pagination-nav-slot pagination-nav-prev">
    <?php if ($prevEpisode): ?>
      <a href="<?= $prevEpisode->url() ?>" class="button">
        <i class="msi-arrow-back" aria-hidden="true"></i>
        <span><span class="sr-only">Vorige Folge: </span><?= $prevVisualLabel ?><span class="sr-only"><?= $prevA11yDetail ?></span></span>
      </a>
    <?php endif; ?>
  </div>

  <div class="pagination-nav-current">
    <span><?= $currentVisualLabel ?></span>
  </div>

  <div class="pagination-nav-slot pagination-nav-next">
    <?php if ($nextEpisode): ?>
      <a href="<?= $nextEpisode->url() ?>" class="button button-primary" data-icon-position="right">
        <i class="msi-arrow-forward" aria-hidden="true"></i>
        <span><span class="sr-only">Nächste Folge: </span><?= $nextVisualLabel ?><span class="sr-only"><?= $nextA11yDetail ?></span></span>
      </a>
    <?php endif; ?>
  </div>
</nav>
