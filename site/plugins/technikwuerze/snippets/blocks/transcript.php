<?php
/**
 * @var Kirby\Cms\Block $block
 */

$headline = trim((string) $block->headline()->value());
$intro = trim((string) $block->intro()->value());
$segments = $block->segments()->toStructure();

if ($segments->isEmpty()) {
  return;
}

$timestampToDuration = static function (string $value): string {
  $value = trim($value);
  if ($value === '' || preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $value, $matches) !== 1) {
    return '';
  }

  if (isset($matches[3]) && $matches[3] !== '') {
    return 'PT' . (int) $matches[1] . 'H' . (int) $matches[2] . 'M' . (int) $matches[3] . 'S';
  }

  return 'PT' . (int) $matches[1] . 'M' . (int) $matches[2] . 'S';
};

$timestampToMs = static function (string $value): int {
  $value = trim($value);
  if ($value === '' || preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $value, $matches) !== 1) {
    return 0;
  }

  if (isset($matches[3]) && $matches[3] !== '') {
    return ((int) $matches[1] * 3600 + (int) $matches[2] * 60 + (int) $matches[3]) * 1000;
  }

  return ((int) $matches[1] * 60 + (int) $matches[2]) * 1000;
};
?>
<section class="tw-transcript" aria-labelledby="<?= $headline !== ''
  ? esc($block->id(), 'attr') . '-headline'
  : esc($block->id(), 'attr') . '-segments' ?>">
  <?php if ($headline !== ''): ?>
    <h2 id="<?= esc($block->id(), 'attr') ?>-headline"><?= esc($headline) ?></h2>
  <?php endif; ?>

  <?php if ($intro !== ''): ?>
    <div class="tw-transcript-intro"><?= $block->intro()->kt() ?></div>
  <?php endif; ?>

  <ol class="tw-transcript-segments" id="<?= esc($block->id(), 'attr') ?>-segments">
    <?php foreach ($segments as $segment): ?>
      <?php
      $speaker = trim((string) $segment->speaker()->value());
      $timestamp = trim((string) $segment->timestamp()->value());
      $timestampMs = $timestampToMs($timestamp);
      $text = trim((string) $segment->text()->value());

      if ($speaker === '' && $timestamp === '' && $text === '') {
        continue;
      }
      ?>
      <li class="tw-transcript-segment">
        <article>
          <?php if ($speaker !== '' || $timestamp !== ''): ?>
            <header class="tw-transcript-segment-meta">
              <?php if ($speaker !== ''): ?>
                <span class="tw-transcript-speaker"><?= esc($speaker) ?></span>
              <?php endif; ?>

              <?php if ($timestamp !== ''): ?>
                <button
                  type="button"
                  class="tw-transcript-timestamp"
                  data-timestamp="<?= $timestampMs ?>"
                  aria-label="Springe zu <?= $speaker !== ''
                    ? esc($speaker . ' bei ' . $timestamp, 'attr')
                    : esc($timestamp, 'attr') ?>"
                >
                  <span class="tw-transcript-timestamp-icon" aria-hidden="true"></span>
                  <span class="tw-transcript-timestamp-time"><?= esc($timestamp) ?></span>
                </button>
              <?php endif; ?>
            </header>
          <?php endif; ?>

          <?php if ($text !== ''): ?>
            <div class="tw-transcript-content"><?= $segment->text()->kt() ?></div>
          <?php endif; ?>
        </article>
      </li>
    <?php endforeach; ?>
  </ol>
</section>
