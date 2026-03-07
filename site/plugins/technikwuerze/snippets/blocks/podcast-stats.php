<?php
/**
 * @var Kirby\Cms\Block $block
 */

$headline = trim((string) $block->headline()->value());

$formatInteger = static function (int $value): string {
  if (function_exists('number')) {
    return (string) number($value, 0);
  }

  return number_format($value, 0, ',', '.');
};

$publishedEpisodeCount =
  site()
    ->find('mediathek')
    ?->index()
    ->filterBy('intendedTemplate', 'episode')
    ->published()
    ->count() ?? 0;

$stats = [];
foreach ($block->stats()->toStructure() as $item) {
  $label = trim((string) $item->label()->value());

  if ($label === '') {
    continue;
  }

  $valueType = trim((string) $item->value_type()->or('integer')->value());
  $value = '';

  if ($valueType === 'total_episodes') {
    $value = $formatInteger((int) $publishedEpisodeCount);
  } elseif ($valueType === 'percent') {
    $rawPercent = trim((string) $item->percent_value()->value());
    if ($rawPercent === '') {
      continue;
    }

    $percentValue = (float) $rawPercent;
    $percentString = rtrim(rtrim(number_format($percentValue, 2, '.', ''), '0'), '.');
    $value = $percentString . '%';
  } else {
    $rawInteger = trim((string) $item->integer_value()->value());
    if ($rawInteger === '') {
      continue;
    }

    $integerValue = (int) round((float) $rawInteger);
    $value = $formatInteger($integerValue);
  }

  $stats[] = [
    'icon' => trim((string) $item->icon()->or('msi-schedule')->value()),
    'label' => $label,
    'value' => $value,
  ];
}

if ($stats === []) {
  $stats = [
    [
      'icon' => 'msi-schedule',
      'label' => 'Durchschnittliche Hördauer',
      'value' => $formatInteger(42),
    ],
    [
      'icon' => 'msi-headphones',
      'label' => 'Folgen gesamt',
      'value' => $formatInteger((int) $publishedEpisodeCount),
    ],
    ['icon' => 'msi-groups', 'label' => 'Stammhörende', 'value' => $formatInteger(3400)],
    ['icon' => 'msi-podcasts', 'label' => 'Abschlussquote', 'value' => '82.5%'],
  ];
}

$stats = array_slice($stats, 0, 4);
?>
<section class="tw-podcast-stats">
  <div class="content medium">
    <?php if ($headline !== ''): ?>
      <h2><?= esc($headline) ?></h2>
    <?php endif; ?>

    <ul class="tw-podcast-stats-grid">
      <?php foreach ($stats as $stat): ?>
        <li class="tw-podcast-stats-item">
          <i class="<?= esc($stat['icon'], 'attr') ?> tw-podcast-stats-icon" aria-hidden="true"></i>
          <span class="tw-podcast-stats-label"><?= esc($stat['label']) ?></span>
          <span class="tw-podcast-stats-value"><?= esc($stat['value']) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
