<?php

declare(strict_types=1);

namespace mauricerenck\Podcaster;

$podcast = new Podcast();
$episode = $episode ?? $page;
$variant = isset($variant) ? trim((string) $variant) : '';
$template = isset($template) ? trim((string) $template) : '';
$templateInline = isset($templateInline) ? trim((string) $templateInline) : '';
$transparent = !empty($transparent);
$debug = !empty($debug);

$customPlayerRequested =
  $variant !== '' ||
  $template !== '' ||
  $templateInline !== '' ||
  $transparent === true ||
  $debug === true;

if ($customPlayerRequested) {

  $playerContainerId = 'podlove-player-' . uniqid();
  $configJson = json_encode(
    $podcast->getPodloveConfigJson($episode),
    JSON_UNESCAPED_SLASHES |
      JSON_UNESCAPED_UNICODE |
      JSON_HEX_TAG |
      JSON_HEX_AMP |
      JSON_HEX_APOS |
      JSON_HEX_QUOT,
  );
  $episodeJson = json_encode(
    $podcast->getPodloveEpisodeJson($episode),
    JSON_UNESCAPED_SLASHES |
      JSON_UNESCAPED_UNICODE |
      JSON_HEX_TAG |
      JSON_HEX_AMP |
      JSON_HEX_APOS |
      JSON_HEX_QUOT,
  );
  ?>
  <div
    id="<?= $playerContainerId ?>"
    class="podlove-player-host"
    data-podlove-player
    data-podlove-config="<?= esc($configJson ?? 'null', 'attr') ?>"
    data-podlove-episode="<?= esc($episodeJson ?? 'null', 'attr') ?>"
    <?php if ($variant !== ''): ?>
      data-podlove-variant="<?= esc($variant, 'attr') ?>"
    <?php endif; ?>
    <?php if ($template !== ''): ?>
      data-podlove-template="<?= esc($template, 'attr') ?>"
    <?php endif; ?>
    <?php if ($transparent): ?>
      data-podlove-transparent="1"
    <?php endif; ?>
    <?php if ($debug): ?>
      data-podlove-debug="1"
    <?php endif; ?>
  >
    <?php if ($templateInline !== ''): ?>
      <?= $templateInline ?>
    <?php endif; ?>
  </div>
  <?php return;
}

$feed = isset($feed) ? $feed : $podcast->getFeedOfEpisode($episode);
if (!$feed) {
  return;
}

switch ($feed->playerType()) {
  case 'podlove':
    snippet('podcaster-podlove-player', ['page' => $episode]);
    break;
  case 'html5':
    snippet('podcaster-html5-player', ['page' => $episode, 'feed' => $feed]);
    break;
}
