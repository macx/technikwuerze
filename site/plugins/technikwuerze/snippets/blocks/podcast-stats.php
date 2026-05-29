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

$formatPercent = static function (float $value): string {
  $percentString = rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
  return $percentString . '%';
};

$publishedEpisodeCount =
  site()
    ->find('mediathek')
    ?->index()
    ->filterBy('intendedTemplate', 'episode')
    ->published()
    ->count() ?? 0;

$listedEpisodeCount =
  site()->find('mediathek')?->index()->filterBy('intendedTemplate', 'episode')->listed()->count() ??
  0;

$resolveTotalDownloads = static function (): ?int {
  if (option('mauricerenck.podcaster.statsInternal', false) !== true) {
    return null;
  }

  $feedPage = site()->index()->filterBy('intendedTemplate', 'podcasterfeed')->first();
  if ($feedPage === null) {
    return null;
  }

  $podcastId = trim((string) $feedPage->podcastId()->value());
  if ($podcastId === '') {
    return null;
  }

  $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
  $stats =
    $dbType === 'sqlite'
      ? new \mauricerenck\Podcaster\PodcasterStatsSqlite()
      : new \mauricerenck\Podcaster\PodcasterStatsMysql();

  $year = date('Y');
  $month = date('n');
  $reports = $stats->getQuickReports($podcastId, $year, $month);

  if ($reports === false) {
    return null;
  }

  $overallRows = $reports['overall']->toArray();
  $totalDownloads = (int) round((float) ($overallRows[0]->downloads ?? 0));

  return $totalDownloads;
};

$resolveDownloadsLastMonth = static function (): ?int {
  if (option('mauricerenck.podcaster.statsInternal', false) !== true) {
    return null;
  }

  $feedPage = site()->index()->filterBy('intendedTemplate', 'podcasterfeed')->first();
  if ($feedPage === null) {
    return null;
  }

  $podcastId = trim((string) $feedPage->podcastId()->value());
  if ($podcastId === '') {
    return null;
  }

  $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
  $stats =
    $dbType === 'sqlite'
      ? new \mauricerenck\Podcaster\PodcasterStatsSqlite()
      : new \mauricerenck\Podcaster\PodcasterStatsMysql();

  $lastMonthTimestamp = strtotime('first day of last month');
  $year = date('Y', $lastMonthTimestamp);
  $month = date('n', $lastMonthTimestamp);
  $reports = $stats->getQuickReports($podcastId, $year, $month);

  if ($reports === false) {
    return null;
  }

  $monthDownloads = 0;
  foreach ($reports['detailed']->toArray() as $result) {
    $monthDownloads += (int) round((float) ($result->downloads ?? 0));
  }

  return $monthDownloads;
};

$resolveTopTenShare = static function (?int $totalDownloads): ?float {
  if ($totalDownloads === null || $totalDownloads <= 0) {
    return null;
  }

  if (option('mauricerenck.podcaster.statsInternal', false) !== true) {
    return null;
  }

  $feedPage = site()->index()->filterBy('intendedTemplate', 'podcasterfeed')->first();
  if ($feedPage === null) {
    return null;
  }

  $podcastId = trim((string) $feedPage->podcastId()->value());
  if ($podcastId === '') {
    return null;
  }

  $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
  $stats =
    $dbType === 'sqlite'
      ? new \mauricerenck\Podcaster\PodcasterStatsSqlite()
      : new \mauricerenck\Podcaster\PodcasterStatsMysql();

  $topEpisodes = $stats->getTopEpisodes($podcastId);
  if ($topEpisodes === false) {
    return null;
  }

  $topTenDownloads = 0;
  foreach ($topEpisodes->toArray() as $episode) {
    $topTenDownloads += (int) round((float) ($episode->downloads ?? 0));
  }

  if ($topTenDownloads <= 0) {
    return null;
  }

  return min(100.0, ($topTenDownloads / $totalDownloads) * 100.0);
};

$totalDownloads = $resolveTotalDownloads();
$downloadsLastMonth = $resolveDownloadsLastMonth();
$topTenShare = $resolveTopTenShare($totalDownloads);

$stats = [];
foreach ($block->stats()->toStructure() as $item) {
  $label = trim((string) $item->label()->value());

  if ($label === '') {
    continue;
  }

  $valueType = trim((string) $item->value_type()->or('integer')->value());
  $value = '';

  if ($valueType === 'total_downloads') {
    if ($totalDownloads === null) {
      $rawInteger = trim((string) $item->integer_value()->value());
      $value =
        $rawInteger === '' ? $formatInteger(0) : $formatInteger((int) round((float) $rawInteger));
    } else {
      $value = $formatInteger((int) $totalDownloads);
    }
  } elseif ($valueType === 'downloads_last_month') {
    if ($downloadsLastMonth === null) {
      $rawInteger = trim((string) $item->integer_value()->value());
      $value =
        $rawInteger === '' ? $formatInteger(0) : $formatInteger((int) round((float) $rawInteger));
    } else {
      $value = $formatInteger((int) $downloadsLastMonth);
    }
  } elseif ($valueType === 'top10_share') {
    if ($topTenShare === null) {
      $rawPercent = trim((string) $item->percent_value()->value());
      if ($rawPercent === '') {
        $value = $formatPercent(0.0);
      } else {
        $value = $formatPercent((float) $rawPercent);
      }
    } else {
      $value = $formatPercent((float) $topTenShare);
    }
  } elseif ($valueType === 'published_episodes') {
    $value = $formatInteger((int) $listedEpisodeCount);
  } elseif ($valueType === 'total_episodes') {
    $value = $formatInteger((int) $publishedEpisodeCount);
  } elseif ($valueType === 'percent') {
    $rawPercent = trim((string) $item->percent_value()->value());
    if ($rawPercent === '') {
      continue;
    }

    $value = $formatPercent((float) $rawPercent);
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
          <span class="tw-podcast-stats-value"><?= esc($stat['value']) ?></span>
          <span class="tw-podcast-stats-label"><?= esc($stat['label']) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
