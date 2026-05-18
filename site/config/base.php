<?php

$projectRoot = dirname(__DIR__, 2);
$dbPath = $projectRoot . '/content/.db/';
$emailOptions = require __DIR__ . '/email.php';

return [
  'ready' => static function ($kirby) {
    $cacheRoot = $kirby->root('cache');
    if (!is_string($cacheRoot) || trim($cacheRoot) === '') {
      return [];
    }

    $loupePath = rtrim($cacheRoot, '/') . '/kirby-loupe';
    if (!is_dir($loupePath)) {
      @mkdir($loupePath, 0777, true);
    }

    return [];
  },

  'panel' => [
    'css' => 'assets/panel.css',
  ],

  'arnoson.kirby-form-builder' => [
    'clientValidation' => true,
    'gridColumns' => 6,
    'autoComplete' => false,
    'addEmptyPlaceholder' => true,
    'defaultEntryStatus' => 'draft',
    'fromEmails' => array_values(array_filter([$emailOptions['email']['noreply'] ?? null])),
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
  'mauricerenck.komments.spam.verification.filterUnverified' => false,
  'mauricerenck.komments.panel.enabled' => true,
  'mauricerenck.komments.panel.webmentions' => true,
  'mauricerenck.komments.panel.showPublished' => true,
  'mauricerenck.komments.privacy.storeEmail' => true,
  'mauricerenck.komments.autoDisable.datefield' => 'date',
];
