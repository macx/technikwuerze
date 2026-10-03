<?php

declare(strict_types=1);

use Kirby\Cms\Page;
use Kirby\Cms\Pages;

function twEpisodeKey(string $slugOrUid): string
{
  $normalized = preg_replace('/^\d+_/', '', strtolower(trim($slugOrUid))) ?? '';

  if (preg_match('/^tw(\d+)(?:-|$)/', $normalized, $match)) {
    return 'tw' . (int) $match[1];
  }

  return $normalized;
}

/**
 * Downloads per episode, merged across all slugs an episode was ever tracked under.
 *
 * @return array<string, int> episode key => downloads, sorted descending
 */
function twEpisodeDownloads(): array
{
  static $downloads = null;

  if (is_array($downloads)) {
    return $downloads;
  }

  $downloads = [];

  if (option('mauricerenck.podcaster.statsInternal', false) !== true) {
    return $downloads;
  }

  $feedPage = site()->index()->filterBy('intendedTemplate', 'podcasterfeed')->first();
  $podcastId = $feedPage ? trim((string) $feedPage->podcastId()->value()) : '';

  if ($podcastId === '') {
    return $downloads;
  }

  try {
    $database = (new \mauricerenck\Podcaster\PodcasterDatabase())->connect(
      option('mauricerenck.podcaster.statsType', 'sqlite'),
    );
    $rows = $database?->query(
      'SELECT episode_slug AS slug, SUM(downloads) AS downloads FROM episodes WHERE podcast_slug = ? GROUP BY episode_slug',
      [$podcastId],
    );
  } catch (Throwable) {
    return $downloads;
  }

  if (!$rows) {
    return $downloads;
  }

  foreach ($rows->toArray() as $row) {
    $key = twEpisodeKey((string) ($row->slug ?? ''));
    if ($key === '') {
      continue;
    }

    $downloads[$key] = ($downloads[$key] ?? 0) + (int) round((float) ($row->downloads ?? 0));
  }

  arsort($downloads);

  return $downloads;
}

function twEpisodeDownloadCount(Page $episode): ?int
{
  return twEpisodeDownloads()[twEpisodeKey($episode->uid())] ?? null;
}

function twPopularEpisodes(Pages $episodes, int $limit): Pages
{
  $byKey = [];
  foreach ($episodes as $episode) {
    $byKey[twEpisodeKey($episode->uid())] = $episode;
  }

  $popular = [];
  foreach (array_keys(twEpisodeDownloads()) as $key) {
    if (isset($byKey[$key])) {
      $popular[] = $byKey[$key];
    }

    if (count($popular) >= $limit) {
      break;
    }
  }

  return new Pages($popular);
}

/**
 * Keeps track of episodes already rendered on the current page so blocks can avoid duplicates.
 */
function twShownEpisodes(?Page $add = null): array
{
  static $shown = [];

  if ($add !== null) {
    $shown[$add->id()] = true;
  }

  return $shown;
}

/**
 * Live summary of a season, e.g. "2009–2013 · 61 Folgen". Null for seasons without episodes.
 */
function twSeasonMeta(Page $season): ?string
{
  $episodes = $season
    ->children()
    ->filterBy('intendedTemplate', 'episode')
    ->published()
    ->filter(static fn($episode) => $episode->date()->isNotEmpty());

  $count = $episodes->count();
  if ($count === 0) {
    return null;
  }

  $years = $episodes->map(static fn($episode) => (int) $episode->date()->toDate('Y'))->values();
  $from = min($years);
  $to = max($years);
  $range = $from === $to ? (string) $from : $from . '–' . $to;

  return sprintf('%s · %d %s', $range, $count, $count === 1 ? 'Folge' : 'Folgen');
}
