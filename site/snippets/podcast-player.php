<?php

declare(strict_types=1);

/**
 * Reusable two-column podcast player layout with slot-based intro content.
 *
 * @var Kirby\Cms\Page $page
 */

$page = $page ?? null;
if (!$page) {
  return;
}

$containerClass = trim((string) ($containerClass ?? 'content narrow'));
$sectionClass = trim((string) ($sectionClass ?? ''));
$dock = ($dock ?? 'always') === 'started' ? 'started' : 'always';
$mediaPosition = trim((string) ($mediaPosition ?? 'right'));
if (!in_array($mediaPosition, ['left', 'right'], true)) {
  $mediaPosition = 'right';
}

$sectionClasses = trim('podcast-player' . ($sectionClass !== '' ? ' ' . $sectionClass : ''));
$introSlot = trim((string) ($slot ?? ''));
$mediaNote = isset($slots) ? trim((string) ($slots->mediaNote() ?? '')) : '';
?>

<div class="<?= esc($sectionClasses) ?>" data-media-position="<?= esc($mediaPosition, 'attr') ?>">
  <div class="podcast-player-container card <?= esc($containerClass) ?>">
    <div class="podcast-player-intro">
      <?php if ($introSlot !== ''): ?>
        <?= $introSlot ?>
      <?php endif; ?>
    </div>

    <?php if ($mediaNote !== ''): ?>
      <div class="podcast-player-aside">
    <?php endif; ?>
      <div class="podcast-player-media">
        <?php snippet('audio-player', ['page' => $page, 'dock' => $dock]); ?>
      </div>

      <?php if ($mediaNote !== ''): ?>
        <div class="podcast-player-media-note text-xs text-light">
          <?= $mediaNote ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
