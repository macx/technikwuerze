<?php snippet('layout', slots: true); ?>

  <?php slot(); ?>
    <div class="page-header content narrow">
      <h1 class="title"><?= $page->title()->html() ?></h1>
    </div>

    <div class="page-content content-text content narrow">
      <h2>Angaben gemäß § 5 DDG</h2>
      <p>
        <?= $site->impressumName()->html() ?><br>
        <?= $site->impressumStreet()->html() ?><br>
        <?= $site->impressumCity()->html() ?>
        <?php if ($site->impressumPhone()->isNotEmpty()): ?><br>
          Tel.: <?= $site->impressumPhone()->html() ?>
        <?php endif; ?>
        <?php if ($site->impressumEmail()->isNotEmpty()): ?><br>
          E-Mail: <a href="mailto:<?= $site->impressumEmail() ?>"><?= $site
  ->impressumEmail()
  ->html() ?></a>
        <?php endif; ?>
      </p>

      <h2>Inhaltlich Verantwortlicher gemäß § 18 Abs. 2 MStV</h2>
      <p>
        <?= $site->impressumName()->html() ?><br>
        <?= $site->impressumStreet()->html() ?><br>
        <?= $site->impressumCity()->html() ?>
      </p>

      <?= $page->text()->toBlocks() ?>
    </div>
  <?php endslot(); ?>

<?php endsnippet(); ?>
