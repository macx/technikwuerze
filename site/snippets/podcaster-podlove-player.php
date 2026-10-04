<?php

namespace mauricerenck\Podcaster;

$podcast = new Podcast();

$episode = isset($episode) ? $episode : $page;
?>

<div id="podlovePlayerContainer"></div>

<script src="<?= url('assets/podlove/web-player/embed.js') ?>"></script>
<script>
  const config = <?= json_encode([
    ...$podcast->getPodloveConfigJson($episode),
    'base' => url('assets/podlove/web-player') . '/',
    'reference' => ['base' => url('assets/podlove/web-player') . '/'],
  ]) ?>;
  const episode = <?= json_encode($podcast->getPodloveEpisodeJson($episode)) ?>;
  window.podlovePlayer('#podlovePlayerContainer', episode, config);
</script>
