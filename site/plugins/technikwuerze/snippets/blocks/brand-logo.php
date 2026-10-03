<?php
$byline = trim((string) $block->byline()->value());
if ($byline === '') {
  $byline = 'für Medienschaffende seit 2005 (mit kurzer Pause)';
}
?>
<div class="tw-brand content medium">
  <h1 class="animation">
    <span class="word">Technik</span><span class="word">würze</span>
  </h1>

  <p class="byline">
    <?= esc($byline) ?>
  </p>
</div>
