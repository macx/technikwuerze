<?php
/** @var \Kirby\Cms\Block $block */

use Kirby\Toolkit\Str;

$level = $block->level()->or('h2');
$text = $block->text()->value();
?>
<<?= $level ?> id="<?= Str::slug($text) ?>"><?= $text ?></<?= $level ?>>
