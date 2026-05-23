<?php snippet('layout', slots: true); ?>

  <?php slot(); ?>
    <div class="page-header content narrow">
      <h1 class="title"><?= $page->header()->or($page->title())->html() ?></h1>

      <?php if ($page->lead()->isNotEmpty()): ?>
        <p class="lead"><?= $page->lead()->kti() ?></p>
      <?php endif; ?>
    </div>

    <div class="page-content content-text content narrow">
      <?= $page->blocks()->toBlocks() ?>
    </div>
  <?php endslot(); ?>

<?php endsnippet(); ?>
