<?php
$networks = $block->networks()->toStructure();
$favoriteNetwork = trim((string) $block->favorite_network()->value());

$networkOptions = [
  'rss' => 'RSS Feed',
  'overcast' => 'Overcast',
  'applepodcasts' => 'Apple Podcasts',
  'youtubemusic' => 'YouTube Music',
  'amazonmusic' => 'Amazon Music',
  'spotify' => 'Spotify',
  'pocketcasts' => 'Pocket Casts',
];

$activeNetworks = [];

foreach ($networks as $network) {
  $networkKey = trim((string) $network->network()->value());
  $url = trim((string) $network->url()->value());

  if ($url === '' || $networkKey === '' || !array_key_exists($networkKey, $networkOptions)) {
    continue;
  }

  $mode = trim((string) $network->mode()->value());
  if ($mode !== 'copy') {
    $mode = 'open';
  }

  $activeNetworks[] = [
    'id' => $networkKey,
    'label' => $networkOptions[$networkKey],
    'url' => esc($url),
    'icon' => $networkKey,
    'mode' => $mode,
    'hoverText' => trim((string) $network->hover_text()->value()),
    'copiedText' => trim((string) $network->copied_text()->value()),
  ];
}

if (empty($activeNetworks)) {
  return;
}

$defaultNetwork = $activeNetworks[0];
$hasFavoritePointer = false;

if ($favoriteNetwork !== '') {
  foreach ($activeNetworks as $network) {
    if ($network['id'] === $favoriteNetwork) {
      $defaultNetwork = $network;
      $hasFavoritePointer = true;
      break;
    }
  }
}

$defaultIsCustom = $defaultNetwork['mode'] === 'copy' && $defaultNetwork['hoverText'] !== '';
?>
<div class="tw-brand-networks<?= $hasFavoritePointer ? ' has-default-pointer' : '' ?>">
  <div class="pointer">
    <span class="pointer-text<?= $defaultIsCustom ? ' is-rss-custom' : '' ?>">
      <span class="pointer-start">
        <?= esc($block->listento_start()->value()) ?>
      </span>
      <span class="pointer-network">
        <?= esc($defaultNetwork['label']) ?>
      </span>
      <span class="pointer-end">
        <?= esc($block->listento_end()->value()) ?>
      </span>
      <span class="pointer-custom"><?= $defaultIsCustom
        ? esc($defaultNetwork['hoverText'])
        : '' ?></span>
    </span>
    <div class="pointer-arrow">
      <?= asset('assets/networks/pointer.svg')->read() ?>
    </div>
  </div>

  <ul>
    <?php foreach ($activeNetworks as $network): ?>
      <?php $iconAsset = asset('assets/networks/' . $network['icon'] . '.svg'); ?>
      <li>
        <a
          href="<?= $network['url'] ?>"
          aria-label="<?= esc($network['label'], 'attr') ?>"
          class="<?= $network['id'] === $defaultNetwork['id'] ? 'is-pointer-target' : '' ?>"
          data-network-id="<?= esc($network['id'], 'attr') ?>"
          data-network-label="<?= esc($network['label'], 'attr') ?>"
          data-network-mode="<?= esc($network['mode'], 'attr') ?>"
          data-network-hover-text="<?= esc($network['hoverText'], 'attr') ?>"
          data-network-copied-text="<?= esc($network['copiedText'], 'attr') ?>"
        >
          <?php if ($iconAsset->exists()): ?>
            <?= $iconAsset->read() ?>
          <?php else: ?>
            <?= esc($network['label']) ?>
          <?php endif; ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
