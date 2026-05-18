<?php

// Production configuration
$baseOptions = require __DIR__ . '/base.php';
$emailOptions = require __DIR__ . '/email.php';
$cacheOptions = require __DIR__ . '/cache.php';

$options = [
  'debug' => false,
  'panel.vue.compiler' => true,

  // Git Content on production: manual commits/pushes via panel area
  'thathoff.git-content' => [
    'commit' => true,
    'push' => false,
    'pull' => false,
    'gitBin' => 'git',
    'branch' => 'main',
  ],
];

return array_replace_recursive($baseOptions, $options, $emailOptions, $cacheOptions);
