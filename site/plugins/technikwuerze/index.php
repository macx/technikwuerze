<?php

Kirby::plugin('tw/brand', [
  'blueprints' => [
    'blocks/brand-logo' => __DIR__ . '/blueprints/blocks/brand-logo.yml',
    'blocks/podcast-networks' => __DIR__ . '/blueprints/blocks/podcast-networks.yml',
    'blocks/last-episode' => __DIR__ . '/blueprints/blocks/last-episode.yml',
    'blocks/podcast-episodes' => __DIR__ . '/blueprints/blocks/podcast-episodes.yml',
    'blocks/teaser' => __DIR__ . '/blueprints/blocks/teaser.yml',
    'blocks/participants-list' => __DIR__ . '/blueprints/blocks/participants-list.yml',
    'blocks/handwritten' => __DIR__ . '/blueprints/blocks/handwritten.yml',
    'blocks/testimonials' => __DIR__ . '/blueprints/blocks/testimonials.yml',
  ],
  'snippets' => [
    'blocks/brand-logo' => __DIR__ . '/snippets/blocks/brand-logo.php',
    'blocks/podcast-networks' => __DIR__ . '/snippets/blocks/podcast-networks.php',
    'blocks/last-episode' => __DIR__ . '/snippets/blocks/last-episode.php',
    'blocks/podcast-episodes' => __DIR__ . '/snippets/blocks/podcast-episodes.php',
    'blocks/teaser' => __DIR__ . '/snippets/blocks/teaser.php',
    'blocks/participants-list' => __DIR__ . '/snippets/blocks/participants-list.php',
    'blocks/handwritten' => __DIR__ . '/snippets/blocks/handwritten.php',
    'blocks/testimonials' => __DIR__ . '/snippets/blocks/testimonials.php',
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
      twUpdateAudioDurationWithFfprobe($file);
    },
    'file.replace:after' => function ($newFile, $oldFile) {
      twGenerateParticipantProfileVariants($newFile);
      twUpdateAudioDurationWithFfprobe($newFile);
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

function twUpdateAudioDurationWithFfprobe($file): void
{
  if ($file->type() !== 'audio') {
    return;
  }

  if ($file->template() !== 'podcaster-episode') {
    return;
  }

  $ffprobeBin = (string) kirby()->option('tw.audioDuration.ffprobeBin', 'ffprobe');
  $ffmpegBin = (string) kirby()->option('tw.audioCover.ffmpegBin', 'ffmpeg');
  $root = $file->root();

  if ($root === null || !is_file($root)) {
    return;
  }

  $cmd = sprintf(
    '%s -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s 2>/dev/null',
    escapeshellarg($ffprobeBin),
    escapeshellarg($root),
  );
  $raw = shell_exec($cmd);

  $duration = null;
  if (is_string($raw)) {
    $seconds = (float) trim($raw);
    if ($seconds > 0) {
      $duration = twFormatAudioDuration($seconds);
    }
  }

  $cover = twExtractAudioCoverImage($file, $ffprobeBin, $ffmpegBin);
  $coverUuid = null;
  if ($cover !== null) {
    $coverUuid = (string) $cover->uuid();
    if ($coverUuid !== '' && str_starts_with($coverUuid, 'file://') === false) {
      $coverUuid = 'file://' . $coverUuid;
    }
  }

  $updates = [];
  if ($duration !== null && (string) $file->duration()->value() !== $duration) {
    $updates['duration'] = $duration;
  }

  if ($coverUuid !== null && (string) $file->cover()->value() !== $coverUuid) {
    $updates['cover'] = $coverUuid;
  }

  if ($updates === []) {
    return;
  }

  try {
    $file->update($updates);
  } catch (\Throwable $e) {
    kirby()->log('audio-metadata')->error($e->getMessage());
  }
}

function twExtractAudioCoverImage($file, string $ffprobeBin, string $ffmpegBin)
{
  $root = $file->root();
  if ($root === null || !is_file($root)) {
    return null;
  }

  $coversPage = site()->find('covers');
  if ($coversPage === null) {
    return null;
  }

  $streamCheckCmd = sprintf(
    '%s -v error -select_streams v:0 -show_entries stream=codec_type -of csv=p=0 %s 2>/dev/null',
    escapeshellarg($ffprobeBin),
    escapeshellarg($root),
  );
  $streamCheckRaw = shell_exec($streamCheckCmd);

  if (!is_string($streamCheckRaw) || trim($streamCheckRaw) !== 'video') {
    return null;
  }

  $base = pathinfo($file->filename(), PATHINFO_FILENAME);
  if ($base === '') {
    return null;
  }

  $tempPath =
    rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) .
    DIRECTORY_SEPARATOR .
    'twz-cover-' .
    uniqid('', true) .
    '.jpg';
  $extractCmd = sprintf(
    '%s -v error -i %s -map 0:v:0 -frames:v 1 -q:v 2 -y %s 2>/dev/null',
    escapeshellarg($ffmpegBin),
    escapeshellarg($root),
    escapeshellarg($tempPath),
  );
  shell_exec($extractCmd);

  if (!is_file($tempPath) || filesize($tempPath) === 0) {
    if (is_file($tempPath)) {
      @unlink($tempPath);
    }
    return null;
  }

  $targetFilename = $base . '-embedded-cover.jpg';
  $existing = $coversPage->file($targetFilename);

  try {
    if ($existing !== null) {
      $existing->replace($tempPath);
      $coverFile = $existing;
    } else {
      $coverFile = $coversPage->createFile([
        'source' => $tempPath,
        'filename' => $targetFilename,
        'template' => 'podcaster-cover',
      ]);
    }
  } catch (\Throwable $e) {
    kirby()->log('audio-cover')->error($e->getMessage());
    @unlink($tempPath);
    return null;
  }

  @unlink($tempPath);

  return $coverFile;
}

function twFormatAudioDuration(float $seconds): string
{
  $rounded = max(0, (int) round($seconds));
  $hours = intdiv($rounded, 3600);
  $minutes = intdiv($rounded % 3600, 60);
  $restSeconds = $rounded % 60;

  return sprintf('%02d:%02d:%02d', $hours, $minutes, $restSeconds);
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
