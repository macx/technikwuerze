<?php
/**
 * @var Kirby\Cms\Page $page  The current participant page
 */

$participants = $page->siblings()->listed()->sortBy('first_name', 'asc', 'last_name', 'asc');
$position = $participants->indexOf($page);

if ($position === false || $participants->count() < 2) {
  return;
}

$prevParticipant = $position > 0 ? $participants->nth($position - 1) : null;
$nextParticipant = $participants->nth($position + 1);

$participantName = static function (Kirby\Cms\Page $participant): string {
  $name = trim($participant->first_name()->value() . ' ' . $participant->last_name()->value());
  return esc($name !== '' ? $name : (string) $participant->title()->value());
};

$currentLabel =
  "\u{00A0}" .
  ($position + 1) .
  '. von ' .
  $participants->count() .
  '<br /><a href="/teilnehmende">Teilnehmenden</a>';
?>
<nav class="pagination-nav" aria-label="Navigation zwischen Teilnehmenden">
  <div class="pagination-nav-slot pagination-nav-prev">
    <?php if ($prevParticipant): ?>
      <a href="<?= $prevParticipant->url() ?>" class="button">
        <i class="msi-arrow-back" aria-hidden="true"></i>
        <span><span class="sr-only">Vorige Person: </span><?= $participantName(
          $prevParticipant,
        ) ?></span>
      </a>
    <?php endif; ?>
  </div>

  <div class="pagination-nav-current">
    <span><?= $currentLabel ?></span>
  </div>

  <div class="pagination-nav-slot pagination-nav-next">
    <?php if ($nextParticipant): ?>
      <a href="<?= $nextParticipant->url() ?>" class="button button-primary" data-icon-position="right">
        <i class="msi-arrow-forward" aria-hidden="true"></i>
        <span><span class="sr-only">Nächste Person: </span><?= $participantName(
          $nextParticipant,
        ) ?></span>
      </a>
    <?php endif; ?>
  </div>
</nav>
