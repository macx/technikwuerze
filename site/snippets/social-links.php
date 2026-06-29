<?php

/**
 * @var array<int, array<string, string>> $links
 * @var string|null $label
 * @var string|null $class
 */

$links = $links ?? [];

if ($links === []) {
  return;
}

$spritemapUrl = '/dist/assets/social.svg';

$networkLabels = [
  'amazon' => 'Amazon',
  'bluesky' => 'Bluesky',
  'discord' => 'Discord',
  'github' => 'GitHub',
  'instagram' => 'Instagram',
  'linkedin' => 'LinkedIn',
  'mastodon' => 'Mastodon',
  'other' => 'Profil',
  'threads' => 'Threads',
  'tiktok' => 'TikTok',
  'twitch' => 'Twitch',
  'website' => 'Website',
  'x' => 'X',
  'youtube' => 'YouTube',
];

$navLabel = trim((string) ($label ?? 'Externe Links'));
$navClass = trim('social-links ' . ($class ?? ''));
$tooltipIdPrefix = 'social-tooltip-' . substr(md5($navClass . '|' . $navLabel), 0, 8);
?>

<nav class="<?= esc($navClass) ?>" aria-label="<?= esc($navLabel) ?>">
  <?php foreach ($links as $index => $link): ?>
    <?php
    $url = trim((string) ($link['url'] ?? ''));
    $network = trim((string) ($link['network'] ?? ''));

    if ($url === '') {
      continue;
    }

    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
      continue;
    }

    $linkLabel = trim((string) ($link['label'] ?? ''));

    if ($linkLabel === '') {
      $linkLabel = $networkLabels[$network] ?? ucfirst($network !== '' ? $network : 'Profil');
    }

    $iconSourcePath = '';
    if ($network !== '') {
      $candidateIconSourcePath = kirby()->root('base') . '/src/assets/social/' . $network . '.svg';

      if (is_file($candidateIconSourcePath)) {
        $iconSourcePath = $candidateIconSourcePath;
      }
    }

    if ($iconSourcePath === '') {
      continue;
    }

    $target = trim((string) ($link['target'] ?? '_blank'));
    $rel = trim((string) ($link['rel'] ?? 'noopener'));
    $tooltipId = $tooltipIdPrefix . '-' . (int) $index;
    ?>
    <span class="social-links-item has-tooltip">
      <a href="<?= esc($url) ?>" class="social-links-link" target="<?= esc(
  $target,
) ?>" rel="<?= esc($rel) ?>">
        <span class="social-links-sr-label"><?= esc($linkLabel) ?></span>
        <span class="social-links-icon" aria-hidden="true">
          <?= tw_sprite_icon($iconSourcePath, $spritemapUrl, $network) ?>
        </span>
      </a>
      <span id="<?= esc($tooltipId) ?>" role="tooltip" class="tooltip" aria-hidden="true">
        <?= esc($linkLabel) ?>
      </span>
    </span>
  <?php endforeach; ?>
</nav>
