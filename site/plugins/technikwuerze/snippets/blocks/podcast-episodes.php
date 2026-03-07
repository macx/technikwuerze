<?php
/**
 * @var Kirby\Cms\Block $block
 */

use Kirby\Cms\Pages;

$headline = trim((string) $block->headline()->value());
$source = trim((string) $block->source()->value());
$showMediathekButton = $block->show_mediathek_button()->toBool();
$amount = (int) $block->amount()->or('3')->value();
$colorScheme = strtolower(trim((string) $block->color_scheme()->value()));

if (!in_array($colorScheme, ['primary', 'secondary'], true)) {
  $colorScheme = 'secondary';
}

$colorSchemeCssVar = '--clr-' . $colorScheme;
$colorSchemeCssValue = 'var(' . $colorSchemeCssVar . ')';

if (!in_array($amount, [3, 6, 9, 12], true)) {
  $amount = 3;
}

if (!in_array($source, ['latest', 'popular', 'random'], true)) {
  $source = 'latest';
}

$mediathek = site()->find('mediathek');
$episodesBase = $mediathek?->index()->filterBy('intendedTemplate', 'episode')->listed();

if ($episodesBase === null || $episodesBase->isEmpty()) {
  return;
}

$latestFallback = $episodesBase->sortBy(
  'date',
  'desc',
  'podcasterseason',
  'desc',
  'podcasterepisode',
  'desc',
);

$resolvePopularEpisodes = function () use ($episodesBase, $latestFallback, $amount): Pages {
  if (option('mauricerenck.podcaster.statsInternal', false) !== true) {
    return $latestFallback->limit($amount);
  }

  $podcastUtil = new \mauricerenck\Podcaster\Podcast();
  $statsEpisode = $latestFallback->first();
  $feed = $statsEpisode ? $podcastUtil->getFeedOfEpisode($statsEpisode) : null;

  if ($feed === null) {
    return $latestFallback->limit($amount);
  }

  $dbType = option('mauricerenck.podcaster.statsType', 'sqlite');
  $stats =
    $dbType === 'sqlite'
      ? new \mauricerenck\Podcaster\PodcasterStatsSqlite()
      : new \mauricerenck\Podcaster\PodcasterStatsMysql();

  $results = $stats->getTopEpisodes((string) $feed->podcastId()->value());
  if ($results === false) {
    return $latestFallback->limit($amount);
  }

  $slugOrder = [];
  foreach ($results->toArray() as $row) {
    $slug = trim((string) ($row->slug ?? ''));
    if ($slug !== '') {
      $slugOrder[] = $slug;
    }
  }

  if ($slugOrder === []) {
    return $latestFallback->limit($amount);
  }

  $lookup = [];
  $lookupByTwNumber = [];
  foreach ($episodesBase as $episode) {
    $lookup[$episode->uid()] = $episode;
    if (preg_match('~Technikw(?:u|ü|uer)rze\s+(\d+)~iu', (string) $episode->title()->value(), $m)) {
      $lookupByTwNumber[(int) $m[1]] = $episode;
    }
  }

  $popularEpisodes = [];
  foreach ($slugOrder as $slug) {
    if (isset($lookup[$slug])) {
      $popularEpisodes[] = $lookup[$slug];
      continue;
    }

    if (preg_match('~tw(\d+)~i', $slug, $m)) {
      $twNumber = (int) $m[1];
      if (isset($lookupByTwNumber[$twNumber])) {
        $popularEpisodes[] = $lookupByTwNumber[$twNumber];
      }
    }
  }

  if ($popularEpisodes === []) {
    return $latestFallback->limit($amount);
  }

  $popularPages = new Pages($popularEpisodes);
  if ($popularPages->count() >= $amount) {
    return $popularPages->limit($amount);
  }

  // If stats data returns only a subset, fill up with latest episodes.
  $seenIds = $popularPages->pluck('id');
  foreach ($latestFallback as $episode) {
    if (in_array($episode->id(), $seenIds, true)) {
      continue;
    }
    $popularPages = $popularPages->add($episode);
    $seenIds[] = $episode->id();
    if ($popularPages->count() >= $amount) {
      break;
    }
  }

  return $popularPages->limit($amount);
};

// Important: stats are only used for "popular".
if ($source === 'random') {
  $episodes = $episodesBase->shuffle()->limit($amount);
} elseif ($source === 'popular') {
  $episodes = $resolvePopularEpisodes();
} else {
  $episodes = $latestFallback->limit($amount);
}

$formatDuration = static function ($episode): string {
  $rawDuration = '';

  $podcast = new \mauricerenck\Podcaster\Podcast();
  $audio = $podcast->getAudioFile($episode);
  if ($audio !== null) {
    $rawDuration = trim((string) $audio->duration()->value());
  }

  if ($rawDuration === '') {
    $twNumber = null;
    $subtitle = trim((string) $episode->podcastersubtitle()->value());
    $title = trim((string) $episode->title()->value());

    if (preg_match('~Technikw(?:u|ü|uer)rze\s*(\d+)~iu', $subtitle, $m)) {
      $twNumber = (int) $m[1];
    } elseif (preg_match('~Technikw(?:u|ü|uer)rze\s*(\d+)~iu', $title, $m)) {
      $twNumber = (int) $m[1];
    }

    if ($twNumber !== null) {
      $audioPage = site()->find('audio');
      if ($audioPage !== null) {
        $metaPath = $audioPage->root() . '/tw' . $twNumber . '.mp3.txt';
        if (is_file($metaPath)) {
          $meta = file_get_contents($metaPath);
          if (
            is_string($meta) &&
            preg_match('/^Duration:\s*([0-9]{2}:[0-9]{2}:[0-9]{2})$/mi', $meta, $m)
          ) {
            $rawDuration = trim($m[1]);
          }
        }
      }
    }
  }

  if ($rawDuration === '') {
    return '–';
  }

  if (preg_match('/^(\d+):([0-5]\d):([0-5]\d)$/', $rawDuration, $m)) {
    $hours = (int) $m[1];
    $minutes = (int) $m[2];
    $seconds = (int) $m[3];

    if ($hours > 0) {
      return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
    }

    return sprintf('%d:%02d', $minutes, $seconds);
  }

  if (preg_match('/^([0-5]?\d):([0-5]\d)$/', $rawDuration)) {
    return $rawDuration;
  }

  if (is_numeric($rawDuration)) {
    $seconds = max(0, (int) round((float) $rawDuration));
    $hours = intdiv($seconds, 3600);
    $minutes = intdiv($seconds % 3600, 60);
    $restSeconds = $seconds % 60;

    if ($hours > 0) {
      return sprintf('%d:%02d:%02d', $hours, $minutes, $restSeconds);
    }

    return sprintf('%d:%02d', $minutes, $restSeconds);
  }

  return $rawDuration;
};
?>
<section class="tw-podcast-episodes content" style="--color-scheme: <?= esc(
  $colorSchemeCssValue,
  'attr',
) ?>">
  <?php if ($headline !== ''): ?>
    <h2><?= esc($headline) ?></h2>
  <?php endif; ?>

  <ul class="tw-podcast-episodes-list">
    <?php foreach ($episodes as $episode): ?>
      <?php
      $title = trim((string) $episode->title()->value());
      $subtitle = trim((string) $episode->podcastersubtitle()->value());
      $publishedDate = $episode->date()->isNotEmpty() ? $episode->date()->toDate('d.m.Y') : '';
      $hosts = $episode->podcasterhosts()->toPages();
      $guests = $episode->podcasterguests()->toPages();
      $hostCount = $hosts->count();
      $guestCount = $guests->count();
      ?>
      <li class="tw-podcast-episodes-item">
        <article>
            <header>
              <h3><?= esc($title) ?></h3>

              <?php if ($subtitle !== ''): ?>
                <div class="tw-podcast-episodes-subtitle">
                  <?= esc($subtitle) ?>
                </div>
              <?php endif; ?>
            </header>

            <div class="tw-podcast-episodes-meta">
              <?php if ($formatDuration($episode) !== '–'): ?>
                <div class="tw-podcast-episodes-duration">
                  <span class="msi-schedule" aria-hidden="true"></span>
                  <span><?= esc($formatDuration($episode)) ?></span>
                </div>
              <?php endif; ?>

              <div class="tw-podcast-episodes-persons" aria-label="Mitwirkende">
                <?php for ($i = 0; $i < $hostCount; $i++): ?>
                  <span class="msi-person-outline" aria-hidden="true"></span>
                <?php endfor; ?>
                <?php for ($i = 0; $i < $guestCount; $i++): ?>
                  <span class="msi-person-filled" aria-hidden="true"></span>
                <?php endfor; ?>
              </div>

              <?php if ($publishedDate !== ''): ?>
                <div class="tw-podcast-episodes-date">
                  <span class="msi-calendar" aria-hidden="true"></span>
                  <?= esc($publishedDate) ?>
                </div>
              <?php endif; ?>
            </div>

            <p class="tw-podcast-episodes-teaser">
              <?= esc($episode->podcasterdescription()->short(120)) ?>
            </p>

            <div>
              <a href="<?= $episode->url() ?>" class="button-primary" data-icon-position="right" >
                <i class="msi-arrow-forward" aria-hidden="true"></i>
                <span>Zur Folge</span>
              </a>
            </div>
        </article>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php if ($showMediathekButton && $mediathek): ?>
    <div class="tw-podcast-episodes-footer">
      <a class="button" data-icon-position="right" href="<?= $mediathek->url() ?>">
        <i class="msi-arrow-forward" aria-hidden="true"></i>
        <span>Zur Mediathek</span>
      </a>
    </div>
  <?php endif; ?>
</section>
