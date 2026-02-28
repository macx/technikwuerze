<?php

Kirby::plugin('tw/brand', [
  'blueprints' => [
    'blocks/brand-logo' => __DIR__ . '/blueprints/blocks/brand-logo.yml',
    'blocks/podcast-networks' => __DIR__ . '/blueprints/blocks/podcast-networks.yml',
    'blocks/last-episode' => __DIR__ . '/blueprints/blocks/last-episode.yml',
    'blocks/participants-list' => __DIR__ . '/blueprints/blocks/participants-list.yml',
    'blocks/handwritten' => __DIR__ . '/blueprints/blocks/handwritten.yml',
  ],
  'snippets' => [
    'blocks/brand-logo' => __DIR__ . '/snippets/blocks/brand-logo.php',
    'blocks/podcast-networks' => __DIR__ . '/snippets/blocks/podcast-networks.php',
    'blocks/last-episode' => __DIR__ . '/snippets/blocks/last-episode.php',
    'blocks/participants-list' => __DIR__ . '/snippets/blocks/participants-list.php',
    'blocks/handwritten' => __DIR__ . '/snippets/blocks/handwritten.php',
  ],
  'pageMethods' => [
    'participationHostCount' => function (): int {
      return twParticipantStats($this)['hostCount'];
    },
    'participationGuestCount' => function (): int {
      return twParticipantStats($this)['guestCount'];
    },
    'participationTotalCount' => function (): int {
      return twParticipantStats($this)['totalCount'];
    },
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

function twParticipantStats($participant): array
{
  if ($participant === null || $participant->intendedTemplate()->name() !== 'participant') {
    return [
      'hostCount' => 0,
      'guestCount' => 0,
      'totalCount' => 0,
    ];
  }

  $episodes = site()
    ->find('mediathek')
    ?->index()
    ->filterBy('intendedTemplate', 'episode')
    ->published();

  if ($episodes === null) {
    return [
      'hostCount' => 0,
      'guestCount' => 0,
      'totalCount' => 0,
    ];
  }

  $hostCount = 0;
  $guestCount = 0;
  $totalCount = 0;

  foreach ($episodes as $episode) {
    $isHost = $episode->podcasterhosts()->toPages()->has($participant);
    $isGuest = $episode->podcasterguests()->toPages()->has($participant);

    if ($isHost) {
      $hostCount++;
    }
    if ($isGuest) {
      $guestCount++;
    }
    if ($isHost || $isGuest) {
      $totalCount++;
    }
  }

  return [
    'hostCount' => $hostCount,
    'guestCount' => $guestCount,
    'totalCount' => $totalCount,
  ];
}
