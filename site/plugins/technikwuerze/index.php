<?php

Kirby::plugin('tw/brand', [
  'blueprints' => [
    'blocks/brand-logo' => __DIR__ . '/blueprints/blocks/brand-logo.yml',
    'blocks/podcast-networks' => __DIR__ . '/blueprints/blocks/podcast-networks.yml',
    'blocks/last-episode' => __DIR__ . '/blueprints/blocks/last-episode.yml',
  ],
  'snippets' => [
    'blocks/brand-logo' => __DIR__ . '/snippets/blocks/brand-logo.php',
    'blocks/podcast-networks' => __DIR__ . '/snippets/blocks/podcast-networks.php',
    'blocks/last-episode' => __DIR__ . '/snippets/blocks/last-episode.php',
  ],
  'hooks' => [
    'file.create:after' => function ($file) {
      twGenerateParticipantProfileVariants($file);
    },
    'file.replace:after' => function ($newFile, $oldFile) {
      twGenerateParticipantProfileVariants($newFile);
    },
  ],
]);

function twGenerateParticipantProfileVariants($file): void
{
  if ($file->type() !== 'image') {
    return;
  }

  $parent = $file->parent();

  if ($parent === null || $parent->intendedTemplate()->name() !== 'participant') {
    return;
  }

  try {
    $file->thumb([
      'width' => 800,
      'height' => 800,
      'crop' => true,
      'quality' => 82,
      'format' => 'webp',
    ]);

    $file->thumb([
      'width' => 800,
      'height' => 800,
      'crop' => true,
      'quality' => 84,
      'format' => 'jpg',
    ]);
  } catch (\Throwable $e) {
    kirby()->log('participant-image')->error($e->getMessage());
  }
}
