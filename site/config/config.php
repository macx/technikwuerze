<?php

$projectRoot = dirname(__DIR__, 2);
$dbPath = $projectRoot . '/content/.db/';
$emailOptions = require __DIR__ . '/partials/email.php';
$translationOptions = require __DIR__ . '/partials/translations.php';

// Local dev: suppress vendor deprecation noise on PHP 8.4 (e.g. mf2/mf2)
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

$options = [
  'debug' => true,

  'panel' => [
    'install' => true,
    'css' => 'assets/panel.css',
  ],

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

  // Kirby Git Content plugin configuration
  'thathoff.git-content' => [
    // Local/develop: no automatic git writes from panel edits.
    'commit' => false,
    'push' => false,
    'pull' => false,
    'gitBin' => 'git',
    'branch' => 'main',
  ],
];

return array_replace_recursive($options, $emailOptions, $translationOptions);
