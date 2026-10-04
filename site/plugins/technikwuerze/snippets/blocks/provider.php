<?php
/** @var \Kirby\Cms\Block $block */

$site = site();
$lines = array_filter([
  $site->providerName()->escape(),
  $site->providerStreet()->escape(),
  trim($site->providerZip()->escape() . ' ' . $site->providerCity()->escape()),
]);
$email = $site->providerEmail();
?>
<p>
  <?= implode('<br>', $lines) ?>
  <?php if ($block->showEmail()->toBool() && $email->isNotEmpty()): ?>
    <br><br>E-Mail: <?= Html::email($email->value()) ?>
  <?php endif; ?>
</p>
