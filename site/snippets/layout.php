<?php
/** @var Kirby\Cms\Site $site */
/** @var Kirby\Cms\Page $page */

$sharing = [
  'url' => $page->url(),
  'title' => $page->isHomePage()
    ? $site->title()->html()
    : $page->title()->html() . ' · ' . $site->title()->html(),
  'name' => 'technikwürze',
  'description' => $site->description()->value(),
  'tags' => $site->tags()->split(),
  'twitter' => 'technikwürze',
];

$favicon = asset('assets/favicon.svg');
?>
<!doctype html>
<html lang="de" class="no-js">

<head>
  <meta charset="UTF-8">
  <title><?= $sharing['title'] ?></title>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="HandheldFriendly" content="true">

  <?php snippet('layout/preload-fonts'); ?>

  <?= vite()->css('src/index.ts', [], true) ?>
  <?= vite()->js('src/index.ts', [], true) ?>

  <?php if (!$page->is('error')): ?>
    <link rel="canonical" href="<?php echo $sharing['url']; ?>">
  <?php endif; ?>
  <?php if ($favicon->exists()): ?>
    <link rel="icon" href="<?= $favicon->url() ?>">
  <?php else: ?>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🎙️</text></svg>">
  <?php endif; ?>

  <?php snippet('webmention-endpoint'); ?>

  <meta name="theme-color" content="#fff">

  <meta name="description" content="<?php echo $sharing['description']; ?>">
  <?php if ($sharing['tags']): ?>
    <meta name="keywords" content="<?= implode(', ', $sharing['tags']) ?>">
  <?php endif; ?>
  <link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml">
  <link rel="alternate" type="application/rss+xml" title="<?= $sharing[
    'name'
  ] ?> Podcast" href="https://technikwuerze.de/mediathek/feed">
</head>

<body>
  <a href="#main-content" class="sr-only skip-link">Zum Inhalt springen</a>
  <?php snippet('layout/header'); ?>
  <?php snippet('search/dialog'); ?>

  <main id="main-content">
    <?= $slot ?>
  </main>

  <?php snippet('layout/footer'); ?>
</body>
</html>
