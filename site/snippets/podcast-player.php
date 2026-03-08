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
$template = isset($template) ? trim((string) $template) : '';
$variant = isset($variant) ? trim((string) $variant) : '';
$templateInline = isset($templateInline) ? trim((string) $templateInline) : '';
$transparent = !empty($transparent);
$debug = !empty($debug);
$mediaPosition = trim((string) ($mediaPosition ?? 'right'));
if (!in_array($mediaPosition, ['left', 'right'], true)) {
  $mediaPosition = 'right';
}

$sectionClasses = trim('podcast-player' . ($sectionClass !== '' ? ' ' . $sectionClass : ''));
$introSlot = trim((string) ($slot ?? ''));
?>

<div class="<?= esc($sectionClasses) ?>" data-media-position="<?= esc($mediaPosition, 'attr') ?>">
  <div class="podcast-player-container <?= esc($containerClass) ?>">
    <div class="podcast-player-intro">
      <?php if ($introSlot !== ''): ?>
        <?= $introSlot ?>
      <?php endif; ?>
    </div>

    <div class="podcast-player-media">
      <?php snippet('podcast-media', [
        'page' => $page,
        'template' => $template,
        'variant' => $variant,
        'templateInline' => $templateInline,
        'transparent' => $transparent,
        'debug' => $debug,
      ]); ?>
    </div>
  </div>
</div>
