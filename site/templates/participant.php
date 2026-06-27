<?php
/**
 * @var Kirby\Cms\Page $page
 */

if ($page->isListed() !== true) {
  go(site()->find('teilnehmende')?->url() ?? site()->url(), 302);
}

$fullName = trim($page->first_name()->value() . ' ' . $page->last_name()->value());
$transitionName = 'participant-name-' . $page->slug();
$transitionImageName = 'participant-image-' . $page->slug();
$image = $page->profile_image()->toFile();
$profiles = $page->external_profiles()->toStructure();
$profileLinks = [];

foreach ($profiles as $profile) {
  $url = trim((string) $profile->url()->value());

  if ($url === '') {
    continue;
  }

  $label = trim((string) $profile->profile_label()->value());
  $network = trim((string) $profile->network()->value());

  $profileLinks[] = [
    'label' => $label,
    'network' => $network,
    'rel' => 'noopener nofollow',
    'url' => $url,
  ];
}

$roleValue = trim((string) $page->participant_role()->value());
$genderValue = trim((string) $page->gender_identities()->value());
$pronouns = trim((string) $page->pronouns()->value());

$roleLabels = [
  'host' => 'Host',
  'guest' => 'Gast',
];

$genderLabels = [
  'female' => 'Weiblich',
  'male' => 'Männlich',
  'non_binary' => 'Nicht-binär',
  'agender' => 'Agender',
  'genderfluid' => 'Genderfluid',
  'self_described' => 'Selbstbezeichnet',
  'prefer_not_to_say' => 'Keine Angabe',
];

$roleLabel = $roleLabels[$roleValue] ?? $roleValue;
$genderLabel = $genderLabels[$genderValue] ?? $genderValue;
$hasParticipantFacts = $roleLabel !== '' || $genderLabel !== '' || $pronouns !== '';

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
  <article class="participant-detail content narrow">
    <header class="page-header">
      <h1 class="title">
        <span class="participant-name" data-vt-group="participant-name" data-vt-name="<?= esc(
          $transitionName,
        ) ?>">
          <?= esc($fullName !== '' ? $fullName : $page->title()->value()) ?>
        </span>
        <?php if ($page->profession()->isNotEmpty()): ?>
          <span class="subtitle">
            <?= $page->profession()->value() ?>
          </span>
        <?php endif; ?>
      </h1>

      <?php if ($page->lead()->isNotEmpty()): ?>
        <p class="lead">
          <?= $page->lead()->kti() ?>
        </p>
      <?php endif; ?>
    </header>

    <div class="participant-stage">
      <aside>
        <div class="participant-meta">
          <section class="card participant-meta-card">
            <?php if ($image): ?>
              <figure class="participant-image">
                <img src="<?= $image->url() ?>" alt="<?= esc(
  $fullName,
) ?>" class="participant-image" data-vt-group="participant-image" data-vt-name="<?= esc(
  $transitionImageName,
) ?>" loading="lazy">
              </figure>
            <?php endif; ?>

            <section class="participant-panel participant-stats" aria-labelledby="participant-stats-heading">
              <h2 id="participant-stats-heading">Statistik</h2>
              <dl class="participant-data-list">
                <div>
                  <dt>Teilnahmen</dt>
                  <dd><?= $totalParticipationCount ?></dd>
                </div>
                <div>
                  <dt>als Moderator</dt>
                  <dd><?= $hostCount ?></dd>
                </div>
                <div>
                  <dt>als Gast</dt>
                  <dd><?= $guestCount ?></dd>
                </div>
              </dl>
            </section>

            <?php if ($hasParticipantFacts): ?>
              <section class="participant-panel participant-facts" aria-labelledby="participant-facts-heading">
                <h2 id="participant-facts-heading">Profil</h2>
                <dl class="participant-data-list">
                  <?php if ($roleLabel !== ''): ?>
                    <div>
                      <dt>Rolle</dt>
                      <dd><?= esc($roleLabel) ?></dd>
                    </div>
                  <?php endif; ?>
                  <?php if ($genderLabel !== ''): ?>
                    <div>
                      <dt>Geschlecht</dt>
                      <dd><?= esc($genderLabel) ?></dd>
                    </div>
                  <?php endif; ?>
                  <?php if ($pronouns !== ''): ?>
                    <div>
                      <dt>Pronomen</dt>
                      <dd><?= esc($pronouns) ?></dd>
                    </div>
                  <?php endif; ?>
                </dl>
              </section>
            <?php endif; ?>
          </section>
        </div>
      </aside>

      <div class="participant-content content-text">
        <?php if ($page->description()->isNotEmpty()): ?>
          <?= $page->description()->kt() ?>
        <?php endif; ?>

        <?php if ($profileLinks !== []): ?>
          <section class="participant-profiles">
            <h2>Externe Profile</h2>
            <?php snippet('social-links', [
              'links' => $profileLinks,
              'label' =>
                'Externe Profile von ' . ($fullName !== '' ? $fullName : $page->title()->value()),
              'class' => 'participant-social',
            ]); ?>
          </section>
        <?php endif; ?>

        <?php if ($recentParticipations->isNotEmpty()): ?>
          <section>
            <h2>Letzte Folgen mit Beteiligung</h2>

            <ul class="episodes-list">
              <?php foreach ($recentParticipations as $episode): ?>
                <?php $episodeNumber = trim((string) $episode->podcasterepisodetotal()->value()); ?>
                <li<?php e(
                  $episodeNumber !== '',
                  ' data-episode-number="' . esc($episodeNumber) . '"',
                ); ?>>
                  <a href="<?= $episode->url() ?>">
                    <?= $episode->title()->value() ?><br />
                    <span class="text-s">
                      <?php if ($episode->date()->isNotEmpty()): ?>
                        <span><?= $episode->date()->toDate('d.m.Y') ?></span>
                      <?php endif; ?>
                    </span>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </section>
        <?php endif; ?>
      </div>
    </div>




  </article>
<?php endslot(); ?>
<?php endsnippet(); ?>
