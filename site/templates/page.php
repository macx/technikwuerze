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

      <?php if ($page->revisedAt()->isNotEmpty()): ?>
        <p class="page-revised text-s text-light">
          Stand: <?= [
            1 => 'Januar',
            'Februar',
            'März',
            'April',
            'Mai',
            'Juni',
            'Juli',
            'August',
            'September',
            'Oktober',
            'November',
            'Dezember',
          ][(int) $page->revisedAt()->toDate('n')] ?> <?= $page->revisedAt()->toDate('Y') ?>
        </p>
      <?php endif; ?>
    </div>
  <?php endslot(); ?>

<?php endsnippet(); ?>
