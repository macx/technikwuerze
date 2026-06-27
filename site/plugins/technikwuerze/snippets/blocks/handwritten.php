<?php
/**
 * @var Kirby\Cms\Block $block
 */

$text = trim((string) $block->text()->value());
$showArrow = $block->show_arrow()->toBool();
$arrowAlign = trim((string) $block->arrow_align()->value());

if ($text === '') {
  return;
}

if (!in_array($arrowAlign, ['left', 'right'], true)) {
  $arrowAlign = 'right';
}

$arrowAsset = asset('assets/networks/pointer.svg');
$hasArrow = $showArrow && $arrowAsset->exists();
?>
<section class="tw-handwritten<?= $hasArrow
  ? ' has-arrow arrow-' . esc($arrowAlign, 'attr')
  : '' ?>">
  <?php if ($hasArrow): ?>
    <span class="tw-handwritten-arrow" aria-hidden="true"><?= $arrowAsset->read() ?></span>
  <?php endif; ?>

  <div class="tw-handwritten-text">
    <?= $block->text()->kt() ?>
  </div>
</section>
