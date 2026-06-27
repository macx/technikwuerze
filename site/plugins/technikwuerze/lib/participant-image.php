<?php

declare(strict_types=1);

if (!function_exists('twGenerateParticipantProfileVariants')) {
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
}
