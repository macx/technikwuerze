<?php
/**
 * @var Kirby\Cms\Block $block
 */

$sourcePage =
  page()->intendedTemplate()->name() === 'testimonials' ? page() : site()->find('testimonials');

if ($sourcePage === null) {
  return;
}

$testimonials = $sourcePage->children()->published();

if ($testimonials->isEmpty()) {
  return;
}

$headline = trim((string) $block->headline()->value());
$amount = max(1, (int) $block->amount()->or('2')->value());
$order = trim((string) $block->order()->value());

if ($order === 'newest') {
  $testimonials = $testimonials->sortBy('modified', 'desc');
} else {
  $testimonials = $testimonials->shuffle();
}

$testimonials = $testimonials->limit($amount);
?>
<section class="tw-testimonials">
  <div class="content narrow">
    <?php if ($headline !== ''): ?>
      <h2 class="section-title"><?= esc($headline) ?></h2>
    <?php endif; ?>

    <ul class="tw-testimonials-list">
      <?php foreach ($testimonials as $testimonial): ?>
        <?php
        $firstName = trim((string) $testimonial->first_name()->value());
        $lastName = trim((string) $testimonial->last_name()->value());
        $fullName = trim($firstName . ' ' . $lastName);
        $profession = trim((string) $testimonial->profession()->value());
        $text = trim((string) $testimonial->testimonial_text()->value());
        $photo = $testimonial->photo()->toFile() ?? $testimonial->images()->first();
        ?>
        <li>
          <figure class="tw-testimonial-item">
            <?php if ($photo): ?>
              <div class="tw-testimonial-avatar">
                <img
                  src="<?= $photo->crop(480, 480)->url() ?>"
                  alt="<?= esc($fullName !== '' ? $fullName : $photo->filename()) ?>"
                  loading="lazy"
                >
              </div>
            <?php endif; ?>

            <?php if ($text !== ''): ?>
              <blockquote class="tw-testimonial-quote">
                <p><?= nl2br(esc($text)) ?></p>
              </blockquote>
            <?php endif; ?>

            <?php if ($fullName !== '' || $profession !== ''): ?>
              <figcaption class="tw-testimonial-person">
                <?php if ($fullName !== ''): ?>
                  <span class="tw-testimonial-name"><?= esc($fullName) ?></span>
                <?php endif; ?>

                <?php if ($profession !== ''): ?>
                  <span class="tw-testimonial-profession"><?= esc($profession) ?></span>
                <?php endif; ?>
              </figcaption>
            <?php endif; ?>
          </figure>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
