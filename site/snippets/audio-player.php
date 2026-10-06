<?php

declare(strict_types=1);

namespace mauricerenck\Podcaster;

/**
 * Native audio player (custom element `tw-audio-player`, see src/scripts/components/audio-player.ts).
 * Renders a plain `<audio controls>` as no-JS fallback; the script replaces the native controls.
 *
 * @var \Kirby\Cms\Page $page
 */

$episode = $episode ?? $page;
$feed = (new Podcast())->getFeedOfEpisode($episode);
if (!$feed) {
  return;
}

$data = (new Podcast())->getPodloveEpisodeJson($episode);
$audioUrl = $data['audio'][0]['url'] ?? '';
if ($audioUrl === '') {
  return;
}

$toSeconds = static function (string $timestamp): float {
  $parts = array_map('floatval', explode(':', $timestamp));
  $seconds = 0.0;
  foreach ($parts as $part) {
    $seconds = $seconds * 60 + $part;
  }
  return $seconds;
};

$chapters = array_map(
  static fn(array $chapter): array => [
    'start' => $toSeconds((string) $chapter['start']),
    'title' => html_entity_decode((string) $chapter['title'], ENT_QUOTES | ENT_HTML5),
  ],
  $data['chapters'] ?? [],
);

$jsonFlags =
  JSON_UNESCAPED_SLASHES |
  JSON_UNESCAPED_UNICODE |
  JSON_HEX_TAG |
  JSON_HEX_AMP |
  JSON_HEX_APOS |
  JSON_HEX_QUOT;
?>
<tw-audio-player
  class="audio-player"
  data-episode-id="<?= esc($episode->uuid()->id() ?? $episode->id(), 'attr') ?>"
  data-dock="<?= ($dock ?? 'always') === 'started' ? 'started' : 'always' ?>"
  data-title="<?= esc((string) $data['title'], 'attr') ?>"
  data-number="<?= esc(trim((string) $episode->podcasterepisodetotal()->value()), 'attr') ?>"
  data-show="<?= esc((string) $data['show']['title'], 'attr') ?>"
  data-poster="<?= esc((string) ($data['poster'] ?? ''), 'attr') ?>"
  data-duration="<?= $toSeconds((string) $data['duration']) ?>"
  data-chapters="<?= esc(json_encode($chapters, $jsonFlags), 'attr') ?>"
  data-skip-back="15"
  data-skip-forward="30"
>
  <audio controls preload="none" src="<?= esc($audioUrl, 'attr') ?>"></audio>
</tw-audio-player>
