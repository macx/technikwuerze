<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $page->title() ?> | <?= $site->title() ?></title>
    <?= vite()->css('src/index.ts') ?>
    <?= vite()->js('src/index.ts') ?>
  </head>
  <body>
    <header>
      <h1><?= $page->title() ?></h1>
    </header>

    <main>
        <?= $page->text()->kt() ?>
    </main>

    <footer>
      <p>&copy; <?= date('Y') ?> <?= $site->title() ?></p>
    </footer>
  </body>
</html>
