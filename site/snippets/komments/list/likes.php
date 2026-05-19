<?php
$likesMock = [
  new \Kirby\Cms\StructureObject([
    'id' => 'mock-like-10',
    'content' => [
      'id' => 'mock-like-10',
      'type' => 'like-of',
      'verified' => 'false',
      'parentid' => '',
      'authoravatar' =>
        '<img src="https://avatars.githubusercontent.com/u/10001?v=4" alt="Maria" />',
      'authorurl' => 'https://example.org',
      'authorname' => 'Maria Musterfrau',
      'permalink' => 'https://example.org/like',
      'createdat' => date('d.m.Y', strtotime('-1 day')) . ', 14:20 Uhr',
      'content' => '',
    ],
  ]),
  new \Kirby\Cms\StructureObject([
    'id' => 'mock-like-20',
    'content' => [
      'id' => 'mock-like-20',
      'type' => 'like-of',
      'verified' => 'true',
      'parentid' => '',
      'authoravatar' =>
        '<img src="https://avatars.githubusercontent.com/u/10002?v=4" alt="Lukas" />',
      'authorurl' => 'https://example.test',
      'authorname' => 'Lukas Weber',
      'permalink' => 'https://example.test/post',
      'createdat' => date('d.m.Y', strtotime('-3 days')) . ', 09:12 Uhr',
      'content' => '',
    ],
  ]),
  new \Kirby\Cms\StructureObject([
    'id' => 'mock-like-30',
    'content' => [
      'id' => 'mock-like-30',
      'type' => 'like-of',
      'verified' => 'false',
      'parentid' => '',
      'authoravatar' =>
        '<img src="https://avatars.githubusercontent.com/u/10003?v=4" alt="Julia" />',
      'authorurl' => 'https://example.net',
      'authorname' => 'Julia Schmitz',
      'permalink' => 'https://example.net/status/12345',
      'createdat' => date('d.m.Y', strtotime('-5 days')) . ', 18:45 Uhr',
      'content' => '',
    ],
  ]),
];

$actualLikes = $page->comments()->filterBy('type', 'like-of');
$displayLikes = $actualLikes->count() > 0 ? $actualLikes : $likesMock;
?>

<?php if (count($displayLikes) > 0): ?>
  <div class="mention-category category-likes" id="likes">
    <h3>Likes</h3>

    <ul class="list-likes">
        <?php foreach ($displayLikes as $comment):
          snippet('komments/response/like', ['comment' => $comment]);
        endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
