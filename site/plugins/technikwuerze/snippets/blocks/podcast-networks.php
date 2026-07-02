<?php
/**
 * @var Kirby\Cms\Block $block
 */

$networksSpritemapUrl = '/dist/assets/networks.svg';
$networksIconSourceDir = kirby()->root('base') . '/src/assets/networks/';

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
  'youtube' => 'YouTube',
  'twitch' => 'Twitch',
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

  $type = trim((string) $network->type()->value());
  if ($type !== 'video') {
    $type = 'audio';
  }

  $activeNetworks[] = [
    'id' => $networkKey,
    'label' => $networkOptions[$networkKey],
    'url' => esc($url),
    'icon' => $networkKey,
    'mode' => $mode,
    'type' => $type,
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
$isVideoDefault = $defaultNetwork['type'] === 'video';

$pointerStart = $isVideoDefault ? $block->watch_start() : $block->listento_start();
$pointerEnd = $isVideoDefault ? $block->watch_end() : $block->listento_end();
$pointerMobileField = $isVideoDefault ? $block->watch_mobile() : $block->listento_mobile();

$mobilePointerText = trim((string) $block->pointer_mobile()->value());
if ($mobilePointerText === '') {
  $mobilePointerText = trim((string) $pointerMobileField->value());
}

$listenStartText = trim((string) $block->listento_start()->value());
$listenEndText = trim((string) $block->listento_end()->value());
$watchStartText = trim((string) $block->watch_start()->value());
$watchEndText = trim((string) $block->watch_end()->value());
?>
<div
  class="tw-brand-networks<?= $hasFavoritePointer ? ' has-default-pointer' : '' ?>"
  data-listen-start="<?= esc($listenStartText, 'attr') ?>"
  data-listen-end="<?= esc($listenEndText, 'attr') ?>"
  data-watch-start="<?= esc($watchStartText, 'attr') ?>"
  data-watch-end="<?= esc($watchEndText, 'attr') ?>"
>
  <?php if ($mobilePointerText !== ''): ?>
    <p class="pointer-mobile handwriting">
      <?= esc($mobilePointerText) ?>
    </p>
  <?php endif; ?>

  <div class="pointer">
    <span class="pointer-text handwriting<?= $defaultIsCustom ? ' is-rss-custom' : '' ?>">
      <span class="pointer-start">
        <?= esc($pointerStart->value()) ?>
      </span>
      <span class="pointer-network">
        <?= esc($defaultNetwork['label']) ?>
      </span>
      <span class="pointer-end">
        <?= esc($pointerEnd->value()) ?>
      </span>
      <span class="pointer-custom"><?= $defaultIsCustom
        ? esc($defaultNetwork['hoverText'])
        : '' ?></span>
    </span>
    <div class="pointer-arrow">
      <?= tw_sprite_icon(
        $networksIconSourceDir . 'pointer.svg',
        $networksSpritemapUrl,
        'pointer',
      ) ?>
    </div>
  </div>

  <ul>
    <?php foreach ($activeNetworks as $network): ?>
      <?php $iconSourcePath = $networksIconSourceDir . $network['icon'] . '.svg'; ?>
      <li>
        <a
          href="<?= $network['url'] ?>"
          aria-label="<?= $network['mode'] === 'copy'
            ? esc($network['label']) . ' (kopiert Feed-Adresse)'
            : esc($network['label']) ?>"
          class="<?= $network['id'] === $defaultNetwork['id'] ? 'is-pointer-target' : '' ?>"
          data-network-id="<?= esc($network['id'], 'attr') ?>"
          data-network-label="<?= esc($network['label'], 'attr') ?>"
          data-network-mode="<?= esc($network['mode'], 'attr') ?>"
          data-network-type="<?= esc($network['type'], 'attr') ?>"
          data-network-hover-text="<?= esc($network['hoverText'], 'attr') ?>"
          data-network-copied-text="<?= esc($network['copiedText'], 'attr') ?>"
        >
          <?php if (is_file($iconSourcePath)): ?>
            <?= tw_sprite_icon($iconSourcePath, $networksSpritemapUrl, $network['icon']) ?>
          <?php else: ?>
            <?= esc($network['label']) ?>
          <?php endif; ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
