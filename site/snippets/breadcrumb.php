<?php
/**
 * @var Kirby\Cms\Page $page
 * @var bool $visible
 */

$visible = $visible ?? true;

$trail = $page->parents()->flip()->filter(static fn($parent) => !$parent->isHomePage());

if ($trail->isEmpty()) {
  return;
}

$items = [];
$position = 1;
foreach ($trail as $parent) {
  $items[] = [
    '@type' => 'ListItem',
    'position' => $position++,
    'name' => $parent->title()->value(),
    'item' => $parent->url(),
  ];
}
$items[] = [
  '@type' => 'ListItem',
  'position' => $position,
  'name' => $page->title()->value(),
  'item' => $page->url(),
];
?>
<?php if ($visible): ?>
<nav class="breadcrumb" aria-label="Brotkrumennavigation">
  <ol>
    <?php foreach ($trail as $parent): ?>
      <li><a href="<?= $parent->url() ?>"><?= $parent->title()->html() ?></a></li>
    <?php endforeach; ?>
    <li aria-current="page"><span><?= $page->title()->html() ?></span></li>
  </ol>
</nav>
<?php endif; ?>
<script type="application/ld+json"><?= json_encode(
  ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items],
  JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG,
) ?></script>
