<?php
/**
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Site $site
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Pages $pages
 */

snippet('layout', slots: true); ?>
  <?php slot(); ?>
    <?php if ($page->header()->isNotEmpty()): ?>
      <div class="page-header">
        <h1 class="title">
          <?= $page->header()->html() ?>
        </h1>

        <?php if ($page->lead()->isNotEmpty()): ?>
          <p class="lead">
            <?= $page->lead()->kti() ?>
          </p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?= $page->blocks()->toBlocks() ?>

    <?php if ($page->text()->isNotEmpty()): ?>
      <div class="teilnehmende-intro">
        <?= $textBlocks->isNotEmpty() ? $textBlocks : $page->text()->kt() ?>
      </div>
    <?php endif; ?>
  <?php endslot(); ?>
<?php endsnippet(); ?>
