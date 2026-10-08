<?php

declare(strict_types=1);

use Kirby\Cms\Page;

if (!function_exists('twEpisodeParticipantIds')) {
  /**
   * @return array<int, string> Page ids of all hosts and guests of an episode
   */
  function twEpisodeParticipantIds(Page $episode): array
  {
    return array_values(
      array_unique([
        ...$episode->podcasterhosts()->toPages()->keys(),
        ...$episode->podcasterguests()->toPages()->keys(),
      ]),
    );
  }
}

if (!function_exists('twFlagParticipantsForTextReview')) {
  /**
   * @param array<int, string> $participantIds
   */
  function twFlagParticipantsForTextReview(array $participantIds): void
  {
    foreach ($participantIds as $participantId) {
      $participant = kirby()->page($participantId);

      if ($participant === null || $participant->text_review()->toBool()) {
        continue;
      }

      kirby()->impersonate('kirby', fn() => $participant->update(['text_review' => 'true']));
    }
  }
}

if (!function_exists('twFlagEpisodeParticipantsForTextReview')) {
  function twFlagEpisodeParticipantsForTextReview(Page $newPage, ?Page $oldPage = null): void
  {
    if ($newPage->intendedTemplate()->name() !== 'episode' || !$newPage->isListed()) {
      return;
    }

    $newIds = twEpisodeParticipantIds($newPage);
    $oldIds = $oldPage !== null && $oldPage->isListed() ? twEpisodeParticipantIds($oldPage) : [];

    twFlagParticipantsForTextReview(
      array_merge(array_diff($newIds, $oldIds), array_diff($oldIds, $newIds)),
    );
  }
}
