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
    // Local/develop: no automatic git writes from panel edits.
    'commit' => false,
    'push' => false,
    'pull' => false,
    'gitBin' => 'git',
    'branch' => 'main',
  ],
];
