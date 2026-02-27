<?php
/**
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Site $site
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Pages $pages
 */

snippet('layout', slots: true); ?>
<?php slot(); ?>
  <?php $participants = $page
    ->children()
    ->listed()
    ->sortBy('last_name', 'asc', 'first_name', 'asc'); ?>

  <?php if ($page->text()->isNotEmpty()): ?>
    <div class="teilnehmende-intro">
      <?= $page->text()->kt() ?>
    </div>
  <?php endif; ?>

  <ul class="participant-list-columns">
    <?php foreach ($participants as $participant): ?>
      <li>
        <a href="<?= $participant->url() ?>">
          <?= esc(
            trim($participant->first_name()->value() . ' ' . $participant->last_name()->value()),
          ) ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endslot(); ?>
<?php endsnippet(); ?>
