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
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="HandheldFriendly" content="true">

  <?php if ($viteHasDevServer || $viteHasManifest): ?>
    <?= vite()->css('src/index.ts', [], true) ?>
    <?= vite()->js('src/index.ts', [], true) ?>
  <?php endif; ?>
  <?= css('/assets/mediathek.css') ?>
  <?= css('/assets/participants.css') ?>
  <?= css('/media/plugins/mauricerenck/komments/komments.css', ['defer' => true]) ?>
  <?= css('/assets/komments-default.css') ?>

  <?php if (!$page->is('error')): ?>
    <link rel="canonical" href="<?php echo $sharing['url']; ?>">
  <?php endif; ?>
  <link rel="icon" href="/assets/images/favicon.ico">

  <meta name="theme-color" content="#fff">

  <meta name="description" content="<?php echo $sharing['description']; ?>">
  <?php if ($page->metaKeywords()): ?>
    <meta name="keywords" content="<?php echo $page->metaKeywords(); ?>">
  <?php endif; ?>
  <link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml">
</head>

<body>
  <?php snippet('layout/header'); ?>

  <main>
    <?= $slot ?>
  </main>

  <?php snippet('layout/footer'); ?>
</body>
</html>
