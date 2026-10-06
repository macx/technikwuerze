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
  'transcriptWordsUrl' => function (): ?string {
    return twTranscriptWordsFile($this) === null ? null : $this->url() . '/transcript-words';
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
