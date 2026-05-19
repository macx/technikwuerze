<?php
$commentList = $page->comments();
$comments = $commentList->filterBy('type', 'comment')->sortBy('createdAt', 'asc');
?>
<?php if ($comments->count() > 0): ?>
  <div id="comments">
    <ul class="thread">
      <?php foreach ($comments->filterBy('parentId', 'maxlength', 0) as $comment): ?>
        <?php snippet('komments/response/comment', [
          'comments' => $comments,
          'comment' => $comment,
          'depth' => 0,
          'max_depth' => 1,
          'root_comment_id' => $comment->id(),
        ]); ?>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
