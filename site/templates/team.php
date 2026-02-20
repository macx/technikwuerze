<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $page->title() ?> | <?= $site->title() ?></title>
    <?= vite()->css('src/index.ts', [], true) ?>
    <?= vite()->js('src/index.ts', [], true) ?>
  </head>
  <body>
    <header>
      <nav>
        <a href="<?= $site->url() ?>">Start</a> /
        <a href="<?= page('mediathek')?->url() ?>">Mediathek</a> /
        <a href="<?= page('team')?->url() ?>">Team</a>
      </nav>
      <h1><?= $page->title() ?></h1>
      <?= $page->text()->kt() ?>
    </header>

    <main>
      <ul>
        <?php foreach ($page->members()->toStructure() as $member): ?>
          <li>
            <strong><?= $member->name()->html() ?></strong>
            <?php if ($member->role()->isNotEmpty()): ?>
              - <?= $member->role()->html() ?>
            <?php endif ?>
            <?php if ($member->bio()->isNotEmpty()): ?>
              <p><?= $member->bio()->kt() ?></p>
            <?php endif ?>
          </li>
        <?php endforeach ?>
      </ul>
    </main>
  </body>
</html>
