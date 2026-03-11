<?php

$base = __DIR__;
require $base . '/kirby/bootstrap.php';

// Load environment variables via Dotenv
if (class_exists('Dotenv\Dotenv') && file_exists($base . '/.env')) {
  Dotenv\Dotenv::createImmutable($base)->load();
}

echo (new Kirby())->render();
