<?php

declare(strict_types=1);

namespace mauricerenck\Podcaster;

$podcast = new Podcast();
$episode = $episode ?? $page;
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
  data-podlove-player
  data-podlove-config="<?= esc($configJson ?? 'null', 'attr') ?>"
  data-podlove-episode="<?= esc($episodeJson ?? 'null', 'attr') ?>"
></div>
