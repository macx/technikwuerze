<?php

$base = dirname(__DIR__);
require $base . '/kirby/bootstrap.php';

// Load environment variables via Dotenv
if (class_exists('Dotenv\Dotenv') && file_exists($base . '/.env')) {
  Dotenv\Dotenv::createImmutable($base)->load();
}

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
