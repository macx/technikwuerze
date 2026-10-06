<?php

declare(strict_types=1);

use Kirby\Cms\File;
use Kirby\Cms\Page;
use Kirby\Http\Range;
use Kirby\Toolkit\Str;

return [
  'kirbytext:after' => function (string $text): string {
    return preg_replace_callback(
      '/<(h[2-4])>(.*?)<\/\1>/is',
      fn($m) => "<{$m[1]} id=\"" . Str::slug(strip_tags($m[2])) . "\">{$m[2]}</{$m[1]}>",
      $text,
    );
  },
  'file.create:after' => function ($file) {
    twGenerateParticipantProfileVariants($file);
  },
  'file.replace:after' => function ($newFile, $oldFile) {
    twGenerateParticipantProfileVariants($newFile);
  },
  'page.create:after' => function (Page $page) {
    twSearchIndexPageAndComments($page);
  },
  'page.update:after' => function (Page $newPage) {
    twSearchIndexPageAndComments($newPage);

    if ($newPage->id() === 'suche') {
      try {
        kirby()->cache('pages')->flush();
      } catch (Throwable) {
      }
    }
  },
  'page.delete:before' => function (...$args) {
    foreach ($args as $arg) {
      if ($arg instanceof Page) {
        twSearchDeleteDocumentsByPageUuid($arg->uuid()->toString());
        return;
      }
    }
  },
  'page.changeStatus:after' => function (Page $newPage) {
    twSearchIndexPageAndComments($newPage);
  },
  'komments.comment.received' => function ($comment) {
    twSearchHandleCommentChange($comment);
  },
  'komments.comment.published' => function ($comment) {
    twSearchHandleCommentChange($comment);
  },
  'komments.comment.replied' => function ($comment) {
    twSearchHandleCommentChange($comment);
  },
  'route:after' => function ($route, string $path, string $method, $result, bool $final) {
    if (option('debug') !== true) {
      return $result;
    }

    if ($method !== 'GET') {
      return $result;
    }

    if (str_contains($path, '/download/') !== true) {
      return $result;
    }

    if ($result instanceof File !== true) {
      return $result;
    }

    $file = $result->root();
    $size = filesize($file);
    $rangeHeader = kirby()->request()->header('Range');
    $range = $rangeHeader ? Range::parse($rangeHeader, $size) : [0, $size - 1];

    if ($range === false) {
      http_response_code(416);
      header('Content-Range: bytes */' . $size);
      exit();
    }

    [$start, $end] = $range;
    http_response_code($rangeHeader ? 206 : 200);
    header('Content-Type: ' . $result->mime());
    header('Accept-Ranges: bytes');
    header('Content-Length: ' . ($end - $start + 1));
    if ($rangeHeader) {
      header("Content-Range: bytes {$start}-{$end}/{$size}");
    }

    while (ob_get_level() > 0) {
      ob_end_clean();
    }

    $handle = fopen($file, 'rb');
    fseek($handle, $start);
    for ($left = $end - $start + 1; $left > 0 && !connection_aborted(); ) {
      $chunk = fread($handle, min(1048576, $left));
      if ($chunk === false || $chunk === '') {
        break;
      }
      echo $chunk;
      flush();
      $left -= strlen($chunk);
    }
    fclose($handle);
    exit();
  },
];
