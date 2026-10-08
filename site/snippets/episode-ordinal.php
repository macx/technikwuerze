<?php
/**
 * @var Kirby\Cms\Page $page
 * @var string $episodeTotal
 * @var string|null $typeLabel
 */

$library = $page->parent()?->parent();
$parts = [
  [
    'label' => 'Phase',
    'value' => $page->podcasterseason()->or('-')->value(),
    'href' => $page->parent()?->url(),
    'hint' => 'alle Folgen der Phase',
  ],
  [
    'label' => 'Technikwürze',
    'value' => $episodeTotal,
  ],
];
?>
<div class="episode-ordinal">
  <?php if ($library): ?>
    <a class="library-link" href="<?= $library->url() ?>">
      <span class="msi-list" aria-hidden="true"></span>
      <span class="label"><?= $library->title()->html() ?></span>
    </a>
  <?php endif; ?>
  <dl>
    <?php foreach ($parts as $part): ?>
      <div>
        <dt><?= esc($part['label']) ?></dt>
        <dd>
          <?php if (!empty($part['href'])): ?>
            <a href="<?= $part['href'] ?>"><?= esc(
  $part['value'],
) ?><span class="sr-only"> – <?= esc($part['hint']) ?></span></a>
          <?php else: ?>
            <?= esc($part['value']) ?>
          <?php endif; ?>
        </dd>
      </div>
    <?php endforeach; ?>
  </dl>
  <?php if (!empty($typeLabel)): ?>
    <p class="episode-type text-xs text-light"><?= esc($typeLabel) ?></p>
  <?php endif; ?>
</div>
