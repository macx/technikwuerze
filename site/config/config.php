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
    'sqlitePath' => 'content/',
    'doNotTrackBots' => true,
    'setId3Data' => true,
    'useApi' => false,
  ],

  // Kirby Git Content plugin configuration
  'thathoff.git-content' => [
    'commit' => [
      'enabled' => true,
    ],
    'push' => [
      'enabled' => false, // Enable on production server only
    ],
    'pull' => [
      'enabled' => false,
    ],
    'gitBinary' => 'git',
    'branch' => 'main',
  ],
];
