<?php

$baseOptions = require __DIR__ . '/base.php';
$emailOptions = require __DIR__ . '/email.php';

// Local dev: suppress vendor deprecation noise on PHP 8.4 (e.g. mf2/mf2)
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

$options = [
  'debug' => true,
  'panel' => [
    'install' => true,
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
