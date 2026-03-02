<?php
/**
 * @var Kirby\Cms\Block $block
 */

$title = trim((string) $block->title()->value());
$foreword = trim((string) $block->foreword()->value());
$afterword = trim((string) $block->afterword()->value());
$showBadge = $block->show_badge()->toBool();
$badgeText = trim((string) $block->badge_text()->value());
$hasBadge = $showBadge && $badgeText !== '';

if ($title === '' && $foreword === '' && $afterword === '') {
  return;
}
?>
<section class="tw-teaser">
  <header class="tw-teaser-title-wrap">
    <?php if ($title !== ''): ?>
      <h2 class="tw-teaser-title"><?= $block->title()->kt() ?></h2>
    <?php endif; ?>

    <?php if ($hasBadge): ?>
      <span class="tw-teaser-badge"><?= esc($badgeText) ?></span>
    <?php endif; ?>
  </header>

  <?php if ($foreword !== ''): ?>
    <div class="tw-teaser-foreword"><?= $block->foreword()->kt() ?></div>
  <?php endif; ?>

  <?php if ($afterword !== ''): ?>
    <div class="tw-teaser-afterword"><?= $block->afterword()->kt() ?></div>
  <?php endif; ?>
</section>
