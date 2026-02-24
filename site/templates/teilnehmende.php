<?php
/**
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Site $site
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Pages $pages
 */

snippet('layout', slots: true);
?>
<?php slot('header') ?>
  <?php snippet('header'); ?>
<?php endslot() ?>

<?php slot() ?>
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
<?php endslot() ?>
<?php endsnippet(); ?>
