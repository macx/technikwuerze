<?php
/**
 * Renders the gold sponsors section as a single inline SVG sprite
 * (one <symbol> per logo, referenced via <use>) instead of three
 * separate inline SVG documents, to cut down on duplicated markup.
 */

$sponsors = [
  [
    'key' => 'mittwald',
    'file' => 'mittwald-wortmarke.svg',
    'url' => 'https://www.mittwald.de/',
    'label' => 'Mittwald (externer Link, öffnet in neuem Fenster)',
  ],
  [
    'key' => 'fontwerk',
    'file' => 'fontwerk.svg',
    'url' => 'https://fontwerk.com/de/',
    'label' => 'Fontwerk (externer Link, öffnet in neuem Fenster)',
  ],
  [
    'key' => 'kirby',
    'file' => 'kirby.svg',
    'url' => 'https://getkirby.com/',
    'label' => 'Kirby CMS (externer Link, öffnet in neuem Fenster)',
  ],
];

$svgNamespace = 'http://www.w3.org/2000/svg';
$skipAttributes = [
  'xmlns',
  'xmlns:xlink',
  'viewbox',
  'role',
  'aria-labelledby',
  'width',
  'height',
  'id',
];
$symbolMarkup = '';
$viewBoxes = [];

foreach ($sponsors as $sponsor) {
  $source = asset('assets/logos/' . $sponsor['file'])->read();

  if ($source === false) {
    continue;
  }

  $dom = new DOMDocument();
  $dom->loadXML($source);
  $svg = $dom->documentElement;
  $viewBox = $svg->getAttribute('viewBox');
  $viewBoxes[$sponsor['key']] = $viewBox;

  // Use createElementNS/setAttributeNS (not the namespace-unaware
  // createElement/setAttribute) so prefixed nodes/attributes (e.g. xml:space)
  // keep their original namespace instead of being serialized with a
  // generated "default:" prefix.
  $symbol = $dom->createElementNS($svgNamespace, 'symbol');
  $symbol->setAttribute('id', 'sponsor-' . $sponsor['key']);
  $symbol->setAttribute('viewBox', $viewBox);

  foreach ($svg->attributes as $attribute) {
    if (in_array(strtolower($attribute->name), $skipAttributes, true)) {
      continue;
    }

    if ($attribute->namespaceURI !== null) {
      $symbol->setAttributeNS($attribute->namespaceURI, $attribute->nodeName, $attribute->value);
    } else {
      $symbol->setAttribute($attribute->name, $attribute->value);
    }
  }

  foreach (iterator_to_array($svg->childNodes) as $child) {
    if ($child->nodeName === 'title') {
      continue;
    }

    $symbol->appendChild($child);
  }

  $symbolMarkup .= $dom->saveXML($symbol);
}
?>

<section class="gold-sponsors">
  <header class="handwriting">
    <?= site()->content()->get('footerSponsorsHint')->esc() ?>
  </header>

  <svg class="sponsor-sprite" aria-hidden="true"><?= $symbolMarkup ?></svg>

  <div class="sponsors">
    <?php foreach ($sponsors as $index => $sponsor): ?>
      <a href="<?= esc(
        $sponsor['url'],
      ) ?>" class="sponsor" target="_blank" rel="noopener" data-sponsor="<?= esc(
  $sponsor['key'],
) ?>" aria-label="<?= esc($sponsor['label']) ?>">
        <svg class="sponsor-icon" viewBox="<?= esc(
          $viewBoxes[$sponsor['key']] ?? '',
        ) ?>" aria-hidden="true"><use href="#sponsor-<?= esc($sponsor['key']) ?>"></use></svg>
      </a>

      <?php if ($index < count($sponsors) - 1): ?>
        <span class="handwriting">&amp;</span>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</section>
