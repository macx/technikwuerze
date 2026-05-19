<?php
$repostsMock = [
  new \Kirby\Cms\StructureObject([
    'id' => 'mock-repost-10',
    'content' => [
      'id' => 'mock-repost-10',
      'type' => 'repost-of',
      'verified' => 'false',
      'parentid' => '',
      'authoravatar' => '<img src="https://avatars.githubusercontent.com/u/10004?v=4" alt="Tim" />',
      'authorurl' => 'https://example.com/tim',
      'authorname' => 'Tim Taylor',
      'permalink' => 'https://example.com/tim/repost',
      'createdat' => date('d.m.Y', strtotime('-2 days')) . ', 11:30 Uhr',
      'content' => '',
    ],
  ]),
  new \Kirby\Cms\StructureObject([
    'id' => 'mock-repost-20',
    'content' => [
      'id' => 'mock-repost-20',
      'type' => 'repost-of',
      'verified' => 'true',
      'parentid' => '',
      'authoravatar' =>
        '<img src="https://avatars.githubusercontent.com/u/10005?v=4" alt="Sarah" />',
      'authorurl' => 'https://example.org/sarah',
      'authorname' => 'Sarah Connor',
      'permalink' => 'https://example.org/sarah/status/987',
      'createdat' => date('d.m.Y', strtotime('-4 days')) . ', 16:55 Uhr',
      'content' => '',
    ],
  ]),
];

$actualReposts = $page->comments()->filterBy('type', 'repost-of');
$displayReposts = $actualReposts->count() > 0 ? $actualReposts : $repostsMock;
?>

<?php if (count($displayReposts) > 0): ?>
  <div class="mention-category category-reposts" id="reposts">
    <h3>Reposts</h3>

    <ul class="list-reposts">
        <?php foreach ($displayReposts as $comment):
          snippet('komments/response/repost', ['comment' => $comment]);
        endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
