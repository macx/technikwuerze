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
    'route:before' => function ($route, string $path, string $method) {
      return twEnforceSiteBasicAuth($path);
    },
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

function twEnforceSiteBasicAuth(string $path)
{
  $config = kirby()->option('tw.siteBasicAuth', []);
  $enabled = (bool) ($config['enabled'] ?? false);

  if ($enabled !== true) {
    return null;
  }

  // Keep Panel/API access unaffected.
  if (str_starts_with($path, 'panel') || str_starts_with($path, 'api')) {
    return null;
  }

  $users = $config['users'] ?? [];
  if (!is_array($users) || $users === []) {
    return null;
  }

  [$username, $password] = twReadBasicAuthCredentials();

  if (
    $username !== null &&
    isset($users[$username]) &&
    hash_equals((string) $users[$username], (string) $password)
  ) {
    return null;
  }

  $realm = (string) ($config['realm'] ?? 'Protected Area');

  return new \Kirby\Cms\Response('Authentication required', 'text/plain', 401, [
    'WWW-Authenticate' => 'Basic realm="' . $realm . '"',
  ]);
}

function twReadBasicAuthCredentials(): array
{
  $username = $_SERVER['PHP_AUTH_USER'] ?? null;
  $password = $_SERVER['PHP_AUTH_PW'] ?? null;

  if ($username !== null && $password !== null) {
    return [$username, $password];
  }

  $header = $_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
  if (!is_string($header) || !str_starts_with($header, 'Basic ')) {
    return [null, null];
  }

  $decoded = base64_decode(substr($header, 6), true);
  if (!is_string($decoded) || !str_contains($decoded, ':')) {
    return [null, null];
  }

  return explode(':', $decoded, 2);
}
