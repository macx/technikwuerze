<?php
$lang = $lang ?? 'de';
$mode = $mode ?? 'system';
$title = $title ?? $page->title()->value();
$fullTitle = $title . ' | ' . $site->title();
?>
<!DOCTYPE html>
<html lang="<?= esc($lang, 'attr') ?>" mode="<?= esc($mode, 'attr') ?>">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= esc($fullTitle) ?></title>
    <?= vite()->css('src/index.ts', [], true) ?>
    <?= vite()->js('src/index.ts', [], true) ?>
    <?php if ($head = $slots->head()): ?>
      <?= $head ?>
    <?php endif ?>
  </head>
  <body>
    <?php if ($header = $slots->header()): ?>
      <?= $header ?>
    <?php endif ?>

    <main>
      <?= $slot ?>
    </main>

    <?php if ($footer = $slots->footer()): ?>
      <?= $footer ?>
    <?php endif ?>
  </body>
</html>
