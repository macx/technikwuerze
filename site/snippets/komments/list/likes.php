<?php $likes = $page->comments()->filterBy('type', 'like-of'); ?>

<?php if ($likes->count() > 0): ?>
  <div class="mention-category category-likes" id="likes">
    <h3>Likes</h3>

    <ul class="list-likes">
        <?php foreach ($likes as $comment):
          snippet('komments/response/like', ['comment' => $comment]);
        endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
