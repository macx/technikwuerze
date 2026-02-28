<?php
/**
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Site $site
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Pages $pages
 */

snippet('layout', slots: true); ?>
  <?php slot(); ?>
    <?php $textBlocks = $page->text()->toBlocks(); ?>

    <?php if ($page->text()->isNotEmpty()): ?>
      <div class="teilnehmende-intro">
        <?= $textBlocks->isNotEmpty() ? $textBlocks : $page->text()->kt() ?>
      </div>
    <?php endif; ?>
  <?php endslot(); ?>
<?php endsnippet(); ?>
