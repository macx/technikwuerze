<?php
$byline = trim((string) $block->byline()->value());
if ($byline === '') {
  $byline = 'für Medienschaffende seit 2005 (mit kurzer Pause)';
}
?>
<div class="tw-brand" style="--brand-logo-size-mobile: 3.2rem;">
  <div class="animation">
    <span class="word">Technik</span><span class="word">würze</span>
  </div>

  <div class="byline">
    <?= esc($byline) ?>
  </div>
</div>
