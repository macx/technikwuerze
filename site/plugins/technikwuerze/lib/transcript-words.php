<?php

declare(strict_types=1);

use Kirby\Cms\Page;
use Kirby\Http\Response;

function twTranscriptWordsFile(Page $page): ?string
{
  $number = (int) $page->podcasterepisodetotal()->value();
  if ($number < 1) {
    return null;
  }

  $file = kirby()->root('content') . '/.transcripts/tw' . $number . '.json.gz';
  return is_file($file) ? $file : null;
}

function twTranscriptWordsResponse(Page $page): ?Response
{
  $file = twTranscriptWordsFile($page);
  if ($file === null) {
    return null;
  }

  $etag = '"' . md5($file . filemtime($file)) . '"';
  $headers = ['ETag' => $etag, 'Cache-Control' => 'public, max-age=3600'];

  if (kirby()->request()->header('If-None-Match') === $etag) {
    return new Response(code: 304, headers: $headers);
  }

  $archive = json_decode((string) gzdecode((string) file_get_contents($file)), true);
  $words = $archive['words'] ?? null;
  if (!is_array($words)) {
    return null;
  }

  $payload = ['t' => [], 'w' => []];
  foreach ($words as $word) {
    $payload['t'][] = (int) round($word[1] * 1000);
    $payload['w'][] = $word[0];
  }

  return new Response(
    json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    'application/json',
    200,
    $headers,
  );
}
