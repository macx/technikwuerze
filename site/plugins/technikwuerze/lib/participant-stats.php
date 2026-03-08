<?php

declare(strict_types=1);

if (!function_exists('twParticipantStats')) {
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
}
