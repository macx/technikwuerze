<?php

namespace mauricerenck\Podcaster;

$feed = new Feed(); ?>
<?php if ($page->podcasterAuthorNames()->isEmpty()) {
  return;
} ?>
<?= $feed->xmlTag('itunes:author', $page->podcasterAuthorNames()->value()) ?>
<?= $feed->xmlTag('googleplay:author', $page->podcasterAuthorNames()->value()) ?>
<?php if ($page->podcasterAuthorEmail()->isNotEmpty()): ?>
<?= $feed->xmlTag('googleplay:email', $page->podcasterAuthorEmail()->value()) ?>
<?php endif; ?>
