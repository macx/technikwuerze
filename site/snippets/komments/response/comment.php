<?php
$depth = isset($depth) ? (int)$depth : 0;
$maxDepth = isset($max_depth) ? (int)$max_depth : 1;
$rootCommentId = isset($root_comment_id) ? (string)$root_comment_id : (string)$comment->id();
?>
<?php snippet('komments/response/base', [
    'comments' => $comments,
    'comment' => $comment,
    'reply_target_id' => $rootCommentId,
], slots: true); ?>
    <?php $replies = $comments->filterBy('parentId', '==', $comment->id())->sortBy('createdAt', 'asc'); ?>
    <?php if ($replies->count() > 0 && $depth < $maxDepth): ?>
    <?php slot('replies'); ?>
        <ul class="comment-replies comment-depth-<?= $depth + 1 ?>">
            <?php foreach ($replies as $reply): ?>
            <?php snippet('komments/response/comment', [
                'comments' => $comments,
                'comment' => $reply,
                'depth' => $depth + 1,
                'max_depth' => $maxDepth,
                'root_comment_id' => $rootCommentId,
            ]); ?>
            <?php endforeach; ?>
        </ul>
    <?php endslot(); ?>
    <?php endif; ?>
<?php endsnippet(); ?>
