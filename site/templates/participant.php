<?php
/**
 * @var Kirby\Cms\Page $page
 */

if ($page->isListed() !== true) {
  go(site()->find('teilnehmende')?->url() ?? site()->url(), 302);
}

$fullName = trim($page->first_name()->value() . ' ' . $page->last_name()->value());
$image = $page->profile_image()->toFile();
$profiles = $page->external_profiles()->toStructure();

$allEpisodes =
  site()->find('mediathek')?->index()->filterBy('intendedTemplate', 'episode')->published() ??
  new Kirby\Cms\Pages([]);
$recentParticipations = $allEpisodes
  ->filter(function ($episode) use ($page) {
    return $episode->podcasterhosts()->toPages()->has($page) ||
      $episode->podcasterguests()->toPages()->has($page);
  })
  ->sortBy('date', 'desc')
  ->limit(5);

$hostCount = 0;
$guestCount = 0;
$totalParticipationCount = 0;

foreach ($allEpisodes as $episode) {
  $isHost = $episode->podcasterhosts()->toPages()->has($page);
  $isGuest = $episode->podcasterguests()->toPages()->has($page);

  if ($isHost) {
    $hostCount++;
  }
  if ($isGuest) {
    $guestCount++;
  }
  if ($isHost || $isGuest) {
    $totalParticipationCount++;
  }
}

snippet('layout', slots: true);
?>
<?php slot(); ?>
  <article class="participant-detail">
    <header>
      <h1><?= esc($fullName !== '' ? $fullName : $page->title()->value()) ?></h1>
      <?php if ($page->profession()->isNotEmpty()): ?>
        <p class="participant-profession"><?= $page->profession()->html() ?></p>
      <?php endif; ?>
    </header>

    <?php if ($image): ?>
      <figure class="participant-image">
        <img src="<?= $image->url() ?>" alt="<?= esc($fullName) ?>" loading="lazy">
      </figure>
    <?php endif; ?>

    <?php if ($page->description()->isNotEmpty()): ?>
      <section class="participant-description">
        <?= $page->description()->kt() ?>
      </section>
    <?php endif; ?>

    <section class="participant-meta">
      <p>
        <strong>Teilnahmen:</strong>
        <?= $totalParticipationCount ?> mal dabei,
        davon <?= $hostCount ?> mal als Moderator
        und <?= $guestCount ?> mal als Gast
      </p>
      <?php if ($page->participant_role()->isNotEmpty()): ?>
        <p><strong>Rolle:</strong> <?= $page->participant_role()->html() ?></p>
      <?php endif; ?>
      <?php if ($page->gender_identities()->isNotEmpty()): ?>
        <p><strong>Geschlecht:</strong> <?= $page->gender_identities()->html() ?></p>
      <?php endif; ?>
      <?php if ($page->pronouns()->isNotEmpty()): ?>
        <p><strong>Pronomen:</strong> <?= $page->pronouns()->html() ?></p>
      <?php endif; ?>
    </section>

    <?php if ($profiles->isNotEmpty()): ?>
      <section class="participant-profiles">
        <h2>Externe Profile</h2>
        <ul>
          <?php foreach ($profiles as $profile): ?>
            <?php
            $url = trim((string) $profile->url()->value());
            if ($url === '') {
              continue;
            }
            $label = trim((string) $profile->profile_label()->value());
            $network = trim((string) $profile->network()->value());
            ?>
            <li>
              <a href="<?= esc($url) ?>" target="_blank" rel="noopener nofollow">
                <?= esc($label !== '' ? $label : ($network !== '' ? $network : $url)) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </section>
    <?php endif; ?>

    <?php if ($recentParticipations->isNotEmpty()): ?>
      <section class="participant-episodes">
        <h2>Letzte Folgen mit Beteiligung</h2>
        <ul>
          <?php foreach ($recentParticipations as $episode): ?>
            <li>
              <a href="<?= $episode->url() ?>"><?= $episode->title()->html() ?></a>
              <?php if ($episode->date()->isNotEmpty()): ?>
                (<?= $episode->date()->toDate('d.m.Y') ?>)
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </section>
    <?php endif; ?>
  </article>
<?php endslot(); ?>
<?php endsnippet(); ?>
