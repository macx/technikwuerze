<?php

require dirname(__DIR__) . '/kirby/bootstrap.php';

echo (new Kirby([
  'roots' => [
    'index' => __DIR__,
    'base' => ($base = dirname(__DIR__)),
    'content' => $base . '/content',
    'site' => $base . '/site',
    'accounts' => $base . '/site/accounts',
    'cache' => $base . '/site/cache',
    'sessions' => $base . '/site/sessions',
    'kirby' => $base . '/kirby',
    'vendor' => $base . '/vendor',
  ],
]))->render();
