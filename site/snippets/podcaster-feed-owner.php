<?php

namespace mauricerenck\Podcaster;

$feed = new Feed(); ?>
<?php if ($page->podcasterOwnerEmail()->isEmpty()) {
  return;
} ?>
<?= $feed->xmlTag(
  'managingEditor',
  $page->podcasterOwnerEmail()->value() . ' (' . $page->podcasterOwnerName()->value() . ')',
) ?>

<itunes:owner>
    <?= $feed->xmlTag('itunes:name', $page->podcasterOwnerName()->value()) ?>
    <?= $feed->xmlTag('itunes:email', $page->podcasterOwnerEmail()->value()) ?>
</itunes:owner>
