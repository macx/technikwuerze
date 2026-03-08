<?php

// Production configuration
$projectRoot = dirname(__DIR__, 2);
$dbPath = $projectRoot . '/content/.db/';
$emailOptions = require __DIR__ . '/partials/email.php';
$cacheOptions = require __DIR__ . '/partials/cache.php';
$translationOptions = require __DIR__ . '/partials/translations.php';

$options = [
  'debug' => false,
  'panel.vue.compiler' => true,

  // Podcaster setup (analytics + player metadata)
  'mauricerenck.podcaster' => [
    'statsInternal' => true,
    'statsType' => 'sqlite',
    'sqlitePath' => $dbPath,
    'doNotTrackBots' => true,
    'useApi' => false,
    'setId3Data' => true,
  ],

  // Audio metadata extraction
  'tw.audioDuration.ffprobeBin' => 'ffprobe',
  'tw.audioCover.ffmpegBin' => 'ffmpeg',

  // Komments setup
  'mauricerenck.komments.storage.type' => 'sqlite',
  'mauricerenck.komments.storage.sqlitePath' => $dbPath,
  'mauricerenck.komments.panel.enabled' => true,
  'mauricerenck.komments.panel.webmentions' => true,
  'mauricerenck.komments.panel.showPublished' => true,
  'mauricerenck.komments.privacy.storeEmail' => true,
  'mauricerenck.komments.autoDisable.datefield' => 'date',

  // Git Content on production: manual commits/pushes via panel area
  'thathoff.git-content' => [
    'commit' => true,
    'push' => false,
    'pull' => false,
    'gitBin' => 'git',
    'branch' => 'main',
  ],
];

return array_replace_recursive($options, $emailOptions, $cacheOptions, $translationOptions);
