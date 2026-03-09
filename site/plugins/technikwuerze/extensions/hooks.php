<?php

declare(strict_types=1);

use Kirby\Cms\Page;

return [
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
];
