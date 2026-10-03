<?php
$site = site();
$useProvider = $block->source()->value() === 'site';
$isPackstation = $block->addresstype()->value() === 'packstation';

if ($useProvider) {
  $name = $site->providerName()->value();
  $zip = $isPackstation ? $site->providerPackstationZip()->value() : $site->providerZip()->value();
  $city = $isPackstation
    ? $site->providerPackstationCity()->value()
    : $site->providerCity()->value();
} else {
  $name = $block->name()->value();
  $zip = $block->zip()->value();
  $city = $block->city()->value();
}

if ($isPackstation) {
  $line2 = $useProvider ? $site->providerPostnummer()->value() : $block->postnummer()->value();
  $line3 =
    'Packstation ' .
    ($useProvider ? $site->providerPackstation()->value() : $block->packstation()->value());
  $country = 'GERMANY';
} else {
  $line2 = $useProvider ? '' : $block->company()->value();
  $line3 = $useProvider ? $site->providerStreet()->value() : $block->street()->value();
  $country = $useProvider ? 'GERMANY' : $block->country()->value();
}

$parts = array_filter([$name, $line2, $line3, $zip . ' ' . $city, $country]);
$addressText = implode("\n", $parts);
if (empty(trim($addressText))) {
  $addressText = 'Technikwürze Address';
}

$qrOptions = [
  'color' => '#000000',
  'back' => '#ffffff',
];
$qrSvgCode = qr($addressText, $qrOptions)->toSvg();
?>
<div class="tw-address-label-wrapper">
  <div class="tw-address-label" aria-hidden="true">
    <svg class="background" viewBox="0 0 600 400" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
        <rect width="100%" height="100%" fill="#ffffff" />
        <rect x="0" y="0" width="100%" height="60" fill="#ffffff" />
        <line x1="0" y1="60" x2="600" y2="60" stroke="#000000" stroke-width="2" />
        <text x="70" y="45" font-family="Arial, Helvetica, sans-serif" font-weight="900" font-size="32" letter-spacing="1.5" fill="#000000">PAKET</text>

        <text x="590" y="42" text-anchor="end" font-family="Arial, Helvetica, sans-serif" font-weight="900" font-size="22" fill="#000000">TECHNIKWÜRZE</text>
        <text x="10" y="77" font-family="Arial" font-weight="bold" font-size="12" fill="#000000">Von:</text>
        <text x="45" y="77" font-family="Arial" font-size="12" fill="#000000">Dir · Hoffentlich mit was Schönem drin</text>
        <line x1="0" y1="90" x2="600" y2="90" stroke="#000000" stroke-width="1" />
        <path d="M430,105 L445,105 L445,120" fill="none" stroke="#666" stroke-width="1" />
        <path d="M445,250 L445,265 L430,265" fill="none" stroke="#666" stroke-width="1" />
        <line x1="460" y1="90" x2="460" y2="280" stroke="#000000" stroke-width="1" />
        <foreignObject x="20" y="100" width="420" height="175">
            <div xmlns="http://www.w3.org/1999/xhtml" class="address-container">
                <strong>An:</strong>
                <span><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></span>
                <?php if ($line2): ?><span><?= htmlspecialchars(
  $line2,
  ENT_QUOTES,
  'UTF-8',
) ?></span><?php endif; ?>
                <?php if ($line3): ?><span><?= htmlspecialchars(
  $line3,
  ENT_QUOTES,
  'UTF-8',
) ?></span><?php endif; ?>
                <span><?= htmlspecialchars($zip, ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars(
   $city,
   ENT_QUOTES,
   'UTF-8',
 ) ?></span>
                <?php if ($country): ?><span><?= htmlspecialchars(
  $country,
  ENT_QUOTES,
  'UTF-8',
) ?></span><?php endif; ?>
            </div>
        </foreignObject>

        <g transform="translate(470, 130)">
            <text x="0" y="0" font-family="Arial" font-size="14" font-weight="bold" fill="#000000">Vorsicht</text>
            <text x="0" y="14" font-family="Arial" font-size="10" fill="#000000">Enthält Begeisterung</text>
            <text x="0" y="26" font-family="Arial" font-size="10" fill="#000000">für das Web</text>
        </g>
        <line x1="0" y1="280" x2="600" y2="280" stroke="#000000" stroke-width="2" />
        <text x="10" y="295" font-family="Arial" font-size="12" fill="#000000" font-weight="bold">Leitcode/Routingcode</text>
        <svg x="10" y="305" width="85" height="85">
            <?= $qrSvgCode ?>
        </svg>
        <svg x="110" y="305" width="480" height="85" viewBox="0 0 664 145" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path stroke="#000000" stroke-width="4" d="M2 145L2 0M24 145L24 0M50 145L50 0M82 145L82 0M96 145L96 0M112 145L112 0M144 145L144 0M156 145L156 0M244 145L244 0M260 145L260 0M270 145L270 0M288 145L288 0M294 145L294 0M302 145L302 0M320 145L320 0M376 145L376 0M382 145L382 0M390 145L390 0M424 145L424 0M496 145L496 0M556 145L556 0M584 145L584 0M596 145L596 0M618 145L618 0M626 145L626 0M632 145L632 0M640 145L640 0M662 145L662 0" />
            <path stroke="#000000" stroke-width="2" d="M7 145L7 0M13 145L13 0M41 145L41 0M45 145L45 0M57 145L57 0M67 145L67 0M77 145L77 0M89 145L89 0M107 145L107 0M123 145L123 0M127 145L127 0M133 145L133 0M149 145L149 0M167 145L167 0M173 145L173 0M189 145L189 0M193 145L193 0M199 145L199 0M217 145L217 0M221 145L221 0M227 145L227 0M265 145L265 0M277 145L277 0M309 145L309 0M325 145L325 0M331 145L331 0M347 145L347 0M353 145L353 0M369 145L369 0M397 145L397 0M401 145L401 0M419 145L419 0M431 145L431 0M441 145L441 0M447 145L447 0M481 145L481 0M485 145L485 0M491 145L491 0M507 145L507 0M523 145L523 0M529 145L529 0M545 145L545 0M551 145L551 0M563 145L563 0M573 145L573 0M589 145L589 0M603 145L603 0M607 145L607 0M657 145L657 0" />
            <path stroke="#000000" stroke-width="6" d="M31 145L31 0M475 145L475 0M651 145L651 0" />
            <path stroke="#000000" stroke-width="8" d="M180 145L180 0M208 145L208 0M236 145L236 0M252 145L252 0M338 145L338 0M362 145L362 0M410 145L410 0M456 145L456 0M466 145L466 0M514 145L514 0M538 145L538 0" />
        </svg>
    </svg>
  </div>
</div>

  <div class="tw-address-text">
    <address>
      <?php foreach (
        array_filter([$name, $line2, $line3, trim($zip . ' ' . $city)])
        as $addressLine
      ): ?>
        <?= htmlspecialchars($addressLine, ENT_QUOTES, 'UTF-8') ?><br>
      <?php endforeach; ?>
    </address>
    <button type="button" class="button" data-copy-text="<?= htmlspecialchars(
      implode("\n", array_filter([$name, $line2, $line3, trim($zip . ' ' . $city)])),
      ENT_QUOTES,
      'UTF-8',
    ) ?>" data-copied-label="Adresse kopiert">
      <i class="msi-content-copy" aria-hidden="true"></i>
      <span>Adresse kopieren</span>
    </button>
  </div>
