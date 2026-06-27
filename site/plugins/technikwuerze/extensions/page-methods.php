<?php

declare(strict_types=1);

return [
  'participationHostCount' => function (): int {
    return twParticipantStats($this)['hostCount'];
  },
  'participationGuestCount' => function (): int {
    return twParticipantStats($this)['guestCount'];
  },
  'participationTotalCount' => function (): int {
    return twParticipantStats($this)['totalCount'];
  },
  'episodeTypeLabel' => function (): string {
    $episodeType = trim((string) $this->podcasterepisodetype()->value());
    if ($episodeType === '') {
      return '-';
    }

    return match ($episodeType) {
      'full' => 'Reguläre Folge',
      'trailer' => 'Trailer',
      'bonus' => 'Bonusmaterial',
      default => $episodeType,
    };
  },
];
