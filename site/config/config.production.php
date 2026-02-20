<?php

// Production configuration
return [
  'debug' => false,

  // Podcaster setup (analytics + player metadata)
  'mauricerenck.podcaster' => [
    'statsInternal' => true,
    'statsType' => 'sqlite',
    'sqlitePath' => 'content/',
    'doNotTrackBots' => true,
    'setId3Data' => true,
    'useApi' => false,
  ],

  // Enable Git push on production server
  'thathoff.git-content' => [
    'commit' => [
      'enabled' => true,
    ],
    'push' => [
      'enabled' => true,
    ],
    'pull' => [
      'enabled' => false,
    ],
    'branch' => 'main',
  ],
];
