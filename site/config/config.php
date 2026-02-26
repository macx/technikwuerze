<?php

return [
  'debug' => true,

  'panel' => [
    'install' => true,
  ],

  // Podcaster setup (analytics + player metadata)
  'mauricerenck.podcaster' => [
    'statsInternal' => true,
    'statsType' => 'sqlite',
    'sqlitePath' => 'content/.db/',
    'doNotTrackBots' => true,
    'setId3Data' => true,
    'useApi' => false,
  ],

  // Komments setup
  'mauricerenck.komments.storage.type' => 'sqlite',
  'mauricerenck.komments.storage.sqlitePath' => 'content/.db/',
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
