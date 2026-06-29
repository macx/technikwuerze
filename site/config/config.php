<?php

$baseOptions = require __DIR__ . '/base.php';
$emailOptions = require __DIR__ . '/email.php';

$options = [
  'debug' => true,
  'panel' => [
    'install' => false,
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

return array_replace_recursive($baseOptions, $options, $emailOptions);
