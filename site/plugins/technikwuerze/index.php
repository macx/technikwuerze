<?php

require_once __DIR__ . '/lib/participant-image.php';
require_once __DIR__ . '/lib/participant-stats.php';

$pageMethods = require __DIR__ . '/extensions/page-methods.php';
$hooks = require __DIR__ . '/extensions/hooks.php';

Kirby::plugin('tw/brand', [
  'blueprints' => [
    'blocks/brand-logo' => __DIR__ . '/blueprints/blocks/brand-logo.yml',
    'blocks/podcast-networks' => __DIR__ . '/blueprints/blocks/podcast-networks.yml',
    'blocks/last-episode' => __DIR__ . '/blueprints/blocks/last-episode.yml',
    'blocks/podcast-episodes' => __DIR__ . '/blueprints/blocks/podcast-episodes.yml',
    'blocks/podcast-stats' => __DIR__ . '/blueprints/blocks/podcast-stats.yml',
    'blocks/teaser' => __DIR__ . '/blueprints/blocks/teaser.yml',
    'blocks/participants-list' => __DIR__ . '/blueprints/blocks/participants-list.yml',
    'blocks/handwritten' => __DIR__ . '/blueprints/blocks/handwritten.yml',
    'blocks/testimonials' => __DIR__ . '/blueprints/blocks/testimonials.yml',
  ],
  'snippets' => [
    'blocks/brand-logo' => __DIR__ . '/snippets/blocks/brand-logo.php',
    'blocks/podcast-networks' => __DIR__ . '/snippets/blocks/podcast-networks.php',
    'blocks/last-episode' => __DIR__ . '/snippets/blocks/last-episode.php',
    'blocks/podcast-episodes' => __DIR__ . '/snippets/blocks/podcast-episodes.php',
    'blocks/podcast-stats' => __DIR__ . '/snippets/blocks/podcast-stats.php',
    'blocks/teaser' => __DIR__ . '/snippets/blocks/teaser.php',
    'blocks/participants-list' => __DIR__ . '/snippets/blocks/participants-list.php',
    'blocks/handwritten' => __DIR__ . '/snippets/blocks/handwritten.php',
    'blocks/testimonials' => __DIR__ . '/snippets/blocks/testimonials.php',
  ],
  'pageMethods' => $pageMethods,
  'hooks' => $hooks,
]);
