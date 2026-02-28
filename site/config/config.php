<?php

$projectRoot = dirname(__DIR__, 2);
$dbPath = $projectRoot . '/content/.db/';

return [
  'debug' => true,

  'panel' => [
    'install' => true,
  ],

  // Podcaster setup (analytics + player metadata)
  'mauricerenck.podcaster' => [
    'statsInternal' => true,
    'statsType' => 'sqlite',
    'sqlitePath' => $dbPath,
    'doNotTrackBots' => true,
    'setId3Data' => true,
    'useApi' => false,
  ],

  // Komments setup
  'mauricerenck.komments.storage.type' => 'sqlite',
  'mauricerenck.komments.storage.sqlitePath' => $dbPath,
  'mauricerenck.komments.panel.enabled' => true,
  'mauricerenck.komments.panel.webmentions' => true,
  'mauricerenck.komments.panel.showPublished' => true,
  'mauricerenck.komments.privacy.storeEmail' => true,
  'mauricerenck.komments.autoDisable.datefield' => 'date',

  // Optional HTTP Basic Auth for frontend routes (browser password prompt)
  'tw.siteBasicAuth' => [
    'enabled' => false,
    'realm' => 'Technikwuerze',
    'users' => array_filter([
      env('TW_BASIC_AUTH_USER') => env('TW_BASIC_AUTH_PASSWORD'),
    ]),
  ],

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
