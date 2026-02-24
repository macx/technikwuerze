<?php

Kirby::plugin('tw/brand', [
  'blueprints' => [
    'blocks/brand-logo' => __DIR__ . '/blueprints/blocks/brand-logo.yml',
    'blocks/podcast-networks' => __DIR__ . '/blueprints/blocks/podcast-networks.yml',
  ],
  'snippets' => [
    'blocks/brand-logo' => __DIR__ . '/snippets/blocks/brand-logo.php',
    'blocks/podcast-networks' => __DIR__ . '/snippets/blocks/podcast-networks.php',
  ],
]);
