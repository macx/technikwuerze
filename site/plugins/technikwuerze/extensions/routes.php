<?php

declare(strict_types=1);

return [
  [
    'pattern' => '(:all)/transcript-words',
    'method' => 'GET',
    'action' => function (string $path) {
      $page = page($path);
      if ($page === null || $page->intendedTemplate()->name() !== 'episode') {
        $this->next();
      }

      return twTranscriptWordsResponse($page) ?? $this->next();
    },
  ],
];
