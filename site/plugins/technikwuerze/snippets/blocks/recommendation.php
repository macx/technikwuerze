<?php
/**
 * @var Kirby\Cms\Block $block
 */

$title = trim((string) $block->title()->value());
$subtitle = trim((string) $block->subtitle()->value());
$description = trim((string) $block->description()->value());
$category = trim((string) $block->category()->value());
$link = trim((string) $block->link()->value());
$image = $block->image()->toFile();
$details = $block->details()->toStructure();

if ($title === '') {
  return;
}

$categoryLabels = [
  'book' => 'Buch',
  'movie' => 'Film',
  'tv-show' => 'TV-Serie',
  'music' => 'Musik',
];

$linkLabels = [
  'book' => 'Zum Buch',
  'movie' => 'Zum Film',
  'tv-show' => 'Zur Serie',
  'music' => 'Zur Musik',
];

$categoryLabel = $categoryLabels[$category] ?? ucfirst($category);
$linkLabel = $linkLabels[$category] ?? 'Zur Empfehlung';

$thumb = $image ? $image->resize(400) : null;
?>
<article class="tw-recommendation">
  <header class="tw-recommendation-header">
    <?php snippet('tag', ['text' => $categoryLabel]); ?>

    <h2 class="tw-recommendation-title"><?= esc($title) ?></h2>

    <?php if ($subtitle !== ''): ?>
      <p class="tw-recommendation-subtitle"><?= esc($subtitle) ?></p>
    <?php endif; ?>
  </header>

  <aside class="tw-recommendation-media">
    <?php if ($thumb): ?>
      <div class="tw-recommendation-cover">
        <img
          src="<?= $thumb->url() ?>"
          alt="<?= esc($title) ?>"
          width="200"
          height="<?= round($thumb->height() / 2) ?>"
          loading="lazy"
        >
      </div>
    <?php endif; ?>

    <?php if ($link !== ''): ?>
      <a
        class="button button-primary"
        href="<?= esc($link) ?>"
        target="_blank"
        rel="noopener noreferrer"
      ><?= esc($linkLabel) ?></a>
    <?php endif; ?>
  </aside>

  <div class="tw-recommendation-content">
    <?php if ($description !== ''): ?>
      <div class="tw-recommendation-description"><?= $block->description()->kt() ?></div>
    <?php endif; ?>

    <?php if ($details->isNotEmpty()): ?>
      <table class="tw-recommendation-details">
        <tbody>
          <?php foreach ($details as $detail): ?>
            <?php
            $detailKey = trim((string) $detail->key()->value());
            $detailText = trim((string) $detail->text()->value());
            $detailSuffix = trim((string) $detail->suffix()->value());
            ?>
            <?php if ($detailKey !== '' || $detailText !== ''): ?>
              <tr>
                <th scope="row"><?= esc($detailKey) ?></th>
                <td>
                  <?= esc($detailText) ?>
                  <?php if ($detailSuffix !== ''): ?>
                    <span class="tw-recommendation-suffix"><?= esc($detailSuffix) ?></span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endif; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</article>
