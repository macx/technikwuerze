<?php snippet('layout', slots: true); ?>

  <?php slot(); ?>
    <?php if ($page->text()->isNotEmpty()): ?>
      <?= $page->text()->toBlocks() ?>
    <?php endif; ?>

    <?php if ($page->content()->get('email_templates')->isNotEmpty()): ?>
      <?php snippet('email-manager/form-wrapper'); ?>
    <?php endif; ?>
  <?php endslot(); ?>

<?php endsnippet(); ?>
