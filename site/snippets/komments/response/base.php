<?php
$classNames = ['u-comment', 'h-cite'];
$classNames[] = 'comment-type_' . $comment->type();

if ($comment->verified()->isTrue()) {
  $classNames[] = 'verified';
}

$replyTargetId =
  isset($reply_target_id) && trim((string) $reply_target_id) !== ''
    ? (string) $reply_target_id
    : (string) $comment->id();
?>
<li class="<?= implode(' ', $classNames) ?>" id="c<?= $comment->id() ?>">
  <div class="<?php e($comment->parentId()->isNotEmpty(), 'reply', 'comment'); ?>">
    <?php if ($header = $slots->header()): ?>
        <?= $header ?>
    <?php else: ?>
      <div class="comment-header h-card">
        <?= $comment->authorAvatar() ?>
        <div class="comment-author-meta">
          <?php if ($comment->authorUrl()->isNotEmpty()): ?>
            <a class="u-author" href="<?= $comment->authorUrl() ?>" rel="nofollow" target="_blank">
              <?= $comment->authorName() ?>
            </a>
          <?php else: ?>
            <span class="p-author"><?= $comment->authorName() ?></span>
          <?php endif; ?>

          <a class="u-url" href="<?= $comment->permalink() ?>">
            <?php
            $type = (string) $comment->type();
            $isComment = $type === 'comment' || $type === 'reply';
            ?>
            <time class="dt-published" datetime="<?= $comment->createdAt()->toDate('c') ?>">
              <?php if ($isComment): ?>
                <?= $comment->createdAt()->toDate('d.m.Y') ?> &middot; <?= $comment
   ->createdAt()
   ->toDate('H:i') ?> Uhr
              <?php else: ?>
                <?= $comment->createdAt()->toDate('d.m.Y') ?>
              <?php endif; ?>
            </time>
          </a>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($body = $slots->body()): ?>
      <?= $body ?>
    <?php else: ?>
      <div class="comment-text content-text p-content p-name"><?= $comment
        ->content()
        ->kirbytext()
        ->emoticons() ?></div>
    <?php endif; ?>

    <?php if ($footer = $slots->footer()): ?>
      <?= $footer ?>
    <?php else: ?>
      <div class="comment-footer">
        <a href="#kommentform" class="button button-compact kommentReply" data-id="<?= $replyTargetId ?>" data-handle="<?= $comment->authorName() ?>">
          <?= t('mauricerenck.komments.action.reply.text') ?>
        </a>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($replies = $slots->replies()): ?>
    <?= $replies ?>
  <?php endif; ?>
</li>
