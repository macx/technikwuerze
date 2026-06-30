<?php $reposts = $page->comments()->filterBy('type', 'repost-of'); ?>

<?php if ($reposts->count() > 0): ?>
  <div class="mention-category category-reposts" id="reposts">
    <h3>Reposts</h3>

    <ul class="list-reposts">
        <?php foreach ($reposts as $comment):
          snippet('komments/response/repost', ['comment' => $comment]);
        endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
