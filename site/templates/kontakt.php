<?php snippet('layout', slots: true); ?>

  <?php slot(); ?>
    <div class="page-header content narrow">
      <h1 class="title">
        <?= $page->header()->html() ?>
      </h1>

      <p class="lead">
        <?= $page->lead()->kti() ?>
      </p>
    </div>

    <div class="content narrow">
      <?php if ($page->form_fields()->isNotEmpty()): ?>
        <div class="card form">
          <h2 id="form-heading">Schreib uns</h2>

          <?php snippet('form', ['formPage' => $page]); ?>
        </div>
      <?php endif; ?>

      <?= $page->blocks()->toBlocks() ?>
    </div>
  <?php endslot(); ?>

<?php endsnippet(); ?>
