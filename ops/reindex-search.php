<?php

/**
 * Rebuilds the Loupe search index outside the web request (the full rebuild takes about a minute and
 * exceeds PHP's web time limit). Meant to be piped to `php` on the server:
 *
 *   TW_BASE=/html [TW_FORCE=1] php -d max_execution_time=0 -d memory_limit=2G < ops/reindex-search.php
 *
 * Without TW_FORCE=1 the index is only rebuilt when it is missing or outdated (index format changed).
 */

$base = rtrim(getenv('TW_BASE') ?: dirname(__DIR__), '/');
$host = getenv('TW_HOST') ?: 'technikwuerze.de';
$_SERVER['HTTP_HOST'] = $host;
$_SERVER['SERVER_NAME'] = $host;

require $base . '/kirby/bootstrap.php';

if (class_exists('Dotenv\Dotenv') && file_exists($base . '/.env')) {
  Dotenv\Dotenv::createImmutable($base)->load();
}

$kirby = new Kirby([
  'roots' => [
    'index' => $base . '/public',
    'base' => $base,
    'content' => $base . '/content',
    'site' => $base . '/site',
    'accounts' => $base . '/site/accounts',
    'cache' => $base . '/site/cache',
    'sessions' => $base . '/site/sessions',
    'kirby' => $base . '/kirby',
    'vendor' => $base . '/vendor',
  ],
]);
$kirby->impersonate('kirby');

if (getenv('TW_FORCE') !== '1' && !twSearchNeedsReindex()) {
  echo "Search index is current, nothing to do.\n";
  exit(0);
}

$startedAt = microtime(true);
$count = twSearchReindexAll();
echo 'Indexed ' . $count . ' documents in ' . round(microtime(true) - $startedAt, 1) . "s.\n";
