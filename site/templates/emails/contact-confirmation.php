<?php
/** @var Kirby\Cms\Page $page */
?>
<?php
/** @var string $name */
?>
Hallo <?= $name !== '' ? $name : 'du' ?>,

vielen Dank für deine Nachricht an Technikwürze.
Wir haben sie erhalten und melden uns so schnell wie möglich zurück.

Direktlink:
<?= $page->url() . PHP_EOL ?>
