<?php
/** @var Kirby\Cms\Site $site */
/** @var Kirby\Cms\Page $page */

use Kirby\Filesystem\F;

$viteConfigPath = kirby()->root('config') . '/vite.config.php';
$viteOutDir = 'dist';
if (F::exists($viteConfigPath)) {
  $viteConfig = require $viteConfigPath;
  $viteOutDir = $viteConfig['outDir'] ?? $viteOutDir;
}

$viteDevDir = kirby()->root('base') ?? kirby()->root('index');
$viteHasDevServer = F::exists($viteDevDir . '/.dev');
$viteHasManifest = F::exists(kirby()->root('index') . '/' . $viteOutDir . '/manifest.json');

$sharing = [
  'url' => $page->url(),
  'title' => $site->title()->html(),
  'name' => 'technikwürze',
  'description' => Escape::html($site->description()->value() || ''),
  'tags' => '',
  'image' => $site->url() . '/assets/images/resp/sharing/lauftrainer-david-hannover-fb.jpg',
  'twitter' => 'technikwürze',
];
?>
<!doctype html>
<html lang="de" class="no-js">

<head>
  <meta charset="UTF-8">
  <title><?= $sharing['title'] ?></title>

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <?php if ($viteHasDevServer || $viteHasManifest): ?>
    <?= vite()->css('src/index.ts', [], true) ?>
    <?= vite()->js('src/index.ts', [], true) ?>
  <?php endif; ?>
</head>

<body>
  <?php snippet('layout/header'); ?>

  <main>
    <?= $slot ?>
  </main>

  <?php snippet('layout/footer'); ?>
</body>
</html>
