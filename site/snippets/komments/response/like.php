<?php snippet('komments/response/base', ['comment' => $comment], slots: true); ?>
  <?php slot('header'); ?>
    <div class="comment-header facepile-item">
      <?php if ($comment->authorUrl()->isNotEmpty()): ?>
        <a class="u-author has-tooltip" href="<?= $comment->authorUrl() ?>" rel="nofollow" target="_blank">
          <?= $comment->authorAvatar() ?>
          <span class="tooltip" role="tooltip"><?= $comment->authorName() ?></span>
        </a>
      <?php else: ?>
        <span class="p-author has-tooltip">
          <?= $comment->authorAvatar() ?>
          <span class="tooltip" role="tooltip"><?= $comment->authorName() ?></span>
        </span>
      <?php endif; ?>
    </div>
  <?php endslot(); ?>
  <?php
  slot('body');
  endslot();
  ?>
  <?php
  slot('footer');
  endslot();
  ?>
<?php endsnippet(); ?>
