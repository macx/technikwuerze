<?php
/**
 * @var Kirby\Cms\Block $block
 */

$sourcePage = null;

if (page()->intendedTemplate()->name() === 'teilnehmende') {
  $sourcePage = page();
}

if ($sourcePage === null) {
  $sourcePage = site()->find('teilnehmende');
}

if ($sourcePage === null) {
  return;
}

$participants = $sourcePage->children()->listed();
if ($participants->isEmpty()) {
  return;
}

$headline = trim((string) $block->headline()->value());
$sortBy = trim((string) $block->sort_by()->value());
$displayLayout = trim((string) $block->display_layout()->value());
$selectedScopes = $block->participant_scope()->toData();

if (!is_array($selectedScopes)) {
  $selectedScopes = [];
}

$allowedScopes = ['publishers', 'hosts_without_publishers', 'guests'];
$selectedScopes = array_values(array_intersect($selectedScopes, $allowedScopes));

if ($selectedScopes === []) {
  $selectedScopes = $allowedScopes;
}

$participants = $participants->filter(function ($participant) use ($selectedScopes) {
  $roles = $participant->additional_roles()->toData();
  $isPublisher = is_array($roles) && in_array('publisher', $roles, true);
  $role = (string) $participant->participant_role()->value();

  if (in_array('publishers', $selectedScopes, true) && $isPublisher) {
    return true;
  }

  if (
    in_array('hosts_without_publishers', $selectedScopes, true) &&
    $role === 'host' &&
    !$isPublisher
  ) {
    return true;
  }

  if (in_array('guests', $selectedScopes, true) && $role === 'guest') {
    return true;
  }

  return false;
});

if ($sortBy === 'first_name') {
  $participants = $participants->sortBy('first_name', 'asc', 'last_name', 'asc');
} else {
  $participants = $participants->sortBy('last_name', 'asc', 'first_name', 'asc');
}

if ($participants->isEmpty()) {
  return;
}

if (!in_array($displayLayout, ['cards', 'list'], true)) {
  $displayLayout = 'list';
}
?>
<section class="tw-participants">
  <?php if ($headline !== ''): ?>
    <h2 class="section-title"><?= esc($headline) ?></h2>
  <?php endif; ?>

  <?php if ($displayLayout === 'list'): ?>
    <ul class="tw-participants-list">
      <?php foreach ($participants as $participant): ?>
        <?php $fullName = trim(
          $participant->first_name()->value() . ' ' . $participant->last_name()->value(),
        ); ?>
        <li>
          <a href="<?= $participant->url() ?>">
            <span class="tw-participants-name"><?= esc($fullName) ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <ul class="tw-participants-cards">
      <?php foreach ($participants as $participant): ?>
        <?php
        $fullName = trim(
          $participant->first_name()->value() . ' ' . $participant->last_name()->value(),
        );
        $image = $participant->profile_image()->toFile();
        ?>
        <li>
          <a class="tw-participants-card" href="<?= $participant->url() ?>">
            <?php if ($image): ?>
              <img src="<?= $image->crop(320, 320)->url() ?>" alt="<?= esc(
  $fullName,
) ?>" loading="lazy">
            <?php endif; ?>
            <span class="tw-participants-name"><?= esc($fullName) ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>
