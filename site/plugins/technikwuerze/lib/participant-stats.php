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

if (!function_exists('twParticipantAppearances')) {
  /**
   * Alle veröffentlichten Folgen je Teilnehmer:in (Team/Gastmoderation + Gäste),
   * aufsteigend nach Datum. Schlüssel: page://-UUID. Einmal pro Request berechnet.
   *
   * @return array<string, array<int, Kirby\Cms\Page>>
   */
  function twParticipantAppearances(): array
  {
    static $map = null;

    if ($map !== null) {
      return $map;
    }

    $map = [];
    $episodes = site()
      ->find('mediathek')
      ?->index()
      ->filterBy('intendedTemplate', 'episode')
      ->published();

    if ($episodes === null) {
      return $map;
    }

    foreach ($episodes->sortBy('date', 'asc') as $episode) {
      $people = $episode
        ->podcasterhosts()
        ->toPages()
        ->add($episode->podcasterguests()->toPages());

      foreach ($people as $person) {
        $map[$person->uuid()->toString()][] = $episode;
      }
    }

    return $map;
  }
}

if (!function_exists('twParticipantMeta')) {
  /**
   * Meta-Zeile für die Teilnehmenden-Liste (TN-1).
   * - Herausgeber: keine
   * - Gast mit genau einer Folge: Hauptthema (Link zur Folge) + Jahr in Klammern, sonst „1 Folge (Jahr)“
   * - alle anderen: „N Folgen (Zeitraum)“, bei Gastmoderation mit Präfix
   *
   * @return array{label: string, topic: string, url: string, text: string}|null
   */
  function twParticipantMeta(Kirby\Cms\Page $participant): ?array
  {
    if (in_array('publisher', $participant->additional_roles()->split(), true)) {
      return null;
    }

    $episodes = twParticipantAppearances()[$participant->uuid()->toString()] ?? [];
    $count = count($episodes);

    if ($count === 0) {
      return null;
    }

    $isGuest = $participant->participant_role()->value() === 'guest';
    $isGuestHost =
      $isGuest && in_array('guest_moderation', $participant->guest_roles()->split(), true);
    $label = $isGuestHost ? 'Gastmoderation' : '';

    $firstYear = $episodes[0]->date()->toDate('Y');
    $lastYear = $episodes[$count - 1]->date()->toDate('Y');
    $period = $firstYear === $lastYear ? $firstYear : $firstYear . '–' . $lastYear;

    if ($isGuest && !$isGuestHost && $count === 1) {
      $topic = $episodes[0]->topics()->split(',')[0] ?? '';

      if ($topic !== '') {
        return ['label' => '', 'topic' => $topic, 'url' => $episodes[0]->url(), 'text' => $period];
      }
    }

    $text = ($count === 1 ? '1 Folge' : $count . ' Folgen') . ' (' . $period . ')';

    return ['label' => $label, 'topic' => '', 'url' => '', 'text' => $text];
  }
}
