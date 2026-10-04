<?php
$commentList = $page->comments();
$comments = $commentList->filterBy('type', 'comment')->sortBy('createdAt', 'asc');
$threads = $comments->filterBy('parentId', 'maxlength', 0);
$visibleThreadCount = 5;
$visibleThreads = $threads->limit($visibleThreadCount);
$hiddenThreads = $threads->offset($visibleThreadCount);
$visibleIds = array_map(
  static fn($comment) => (string) $comment->id(),
  array_values($visibleThreads->data()),
);
$hiddenCommentCount =
  $comments->count() -
  count(
    array_filter(
      $comments->data(),
      static fn($comment) => in_array((string) $comment->id(), $visibleIds, true) ||
        in_array((string) $comment->parentId(), $visibleIds, true),
    ),
  );

$renderThreads = static function ($threads) use ($comments): void {
  foreach ($threads as $comment) {
    snippet('komments/response/comment', [
      'comments' => $comments,
      'comment' => $comment,
      'depth' => 0,
      'max_depth' => 1,
      'root_comment_id' => $comment->id(),
    ]);
  }
};
?>
<?php if ($comments->count() > 0): ?>
  <div id="comments">
    <ul class="thread">
      <?php $renderThreads($visibleThreads); ?>
    </ul>

    <?php if ($hiddenThreads->isNotEmpty()): ?>
      <details class="comments-more" data-comments-more>
        <summary>
          <div class="button">
          Weitere <?= $hiddenCommentCount ?> <?= $hiddenCommentCount === 1
   ? 'Kommentar'
   : 'Kommentare' ?> anzeigen
          </div>
        </summary>
        <ul class="thread">
          <?php $renderThreads($hiddenThreads); ?>
        </ul>
      </details>
    <?php endif; ?>
  </div>
<?php endif; ?>
