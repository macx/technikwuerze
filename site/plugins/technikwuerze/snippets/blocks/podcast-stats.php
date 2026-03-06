<?php
/**
 * @var Kirby\Cms\Block $block
 */

$headline = trim((string) $block->headline()->value());

$stats = [];
foreach ($block->stats()->toStructure() as $item) {
  $label = trim((string) $item->label()->value());
  $value = trim((string) $item->value()->value());

  if ($label === '' || $value === '') {
    continue;
  }

  $stats[] = [
    'icon' => trim((string) $item->icon()->or('msi-schedule')->value()),
    'label' => $label,
    'value' => $value,
  ];
}

if ($stats === []) {
  $stats = [
    ['icon' => 'msi-schedule', 'label' => 'Durchschnittliche Hördauer', 'value' => '42:15'],
    ['icon' => 'msi-headphones', 'label' => 'Folgen gesamt', 'value' => '189'],
    ['icon' => 'msi-groups', 'label' => 'Stammhörende', 'value' => '3.400'],
    ['icon' => 'msi-podcasts', 'label' => 'Veröffentlichungsjahre', 'value' => '20'],
  ];
}

$stats = array_slice($stats, 0, 4);
?>
<section class="tw-podcast-stats">
  <?php if ($headline !== ''): ?>
    <h2><?= esc($headline) ?></h2>
  <?php endif; ?>

  <ul class="tw-podcast-stats-grid">
    <?php foreach ($stats as $stat): ?>
      <li class="tw-podcast-stats-item">
        <span class="<?= esc(
          $stat['icon'],
          'attr',
        ) ?> tw-podcast-stats-icon" aria-hidden="true"></span>
        <span class="tw-podcast-stats-label"><?= esc($stat['label']) ?></span>
        <span class="tw-podcast-stats-value"><?= esc($stat['value']) ?></span>
      </li>
    <?php endforeach; ?>
  </ul>
</section>
