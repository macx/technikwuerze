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

  $iconNetwork = is_file(kirby()->root('base') . '/src/assets/social/' . $network . '.svg')
    ? $network
    : 'website';

  $profileLinks[] = [
    'label' => in_array($label, ['', 'Website'], true) ? Kirby\Http\Url::short($url) : $label,
    'network' => $iconNetwork,
    'url' => $url,
  ];
}

$roleValue = trim((string) $page->participant_role()->value());
$genderValue = trim((string) $page->gender_identities()->value());
$pronouns = trim((string) $page->pronouns()->value());

$roleLabels = [
  'host' => 'Team',
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
$isGuestHost =
  $roleValue === 'guest' && in_array('guest_moderation', $page->guest_roles()->split(), true);
if ($isGuestHost) {
  $roleLabel .= ' · Gastmoderation';
}
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
  ->limit(8);

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

$teamRoleLabels = [
  'publisher' => [
    'female' => 'Herausgeberin',
    'male' => 'Herausgeber',
    'other' => 'Herausgeber:in',
  ],
  'moderation' => ['female' => 'Moderatorin', 'male' => 'Moderator', 'other' => 'Moderator:in'],
  'editorial' => ['female' => 'Redakteurin', 'male' => 'Redakteur', 'other' => 'Redakteur:in'],
];
$genderKey = in_array($genderValue, ['female', 'male'], true) ? $genderValue : 'other';
$teamRoles = [];
if ($page->participant_role()->value() === 'host') {
  foreach ($page->additional_roles()->split() as $role) {
    if (isset($teamRoleLabels[$role])) {
      $teamRoles[] = $teamRoleLabels[$role][$genderKey];
    }
  }
}
$teamRolesSentence =
  count($teamRoles) > 1
    ? implode(', ', array_slice($teamRoles, 0, -1)) . ' und ' . end($teamRoles)
    : $teamRoles[0] ?? '';

snippet('layout', slots: true);
?>
<?php slot(); ?>
  <article class="participant-detail content medium">
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
    </header>

    <div class="participant-stage">
      <aside>
        <div class="participant-meta">
          <section class="card participant-meta-card">
            <?php if ($image): ?>
              <?php $blurredBackground = $image->thumb([
                'width' => 64,
                'height' => 40,
                'crop' => true,
                'blur' => 16,
                'quality' => 60,
              ]); ?>
              <div class="participant-card-header" style="--participant-blur: url('<?= esc(
                $blurredBackground->url(),
                'attr',
              ) ?>')">
                <figure class="participant-image">
                  <img src="<?= $image->crop(304, 304)->url() ?>" srcset="<?= $image->srcset([
  '240w' => ['width' => 240, 'height' => 240, 'crop' => true],
  '360w' => ['width' => 360, 'height' => 360, 'crop' => true],
  '480w' => ['width' => 480, 'height' => 480, 'crop' => true],
]) ?>" sizes="9.5rem" alt="<?= esc(
  $fullName,
) ?>" class="participant-image" data-vt-group="participant-image" data-vt-name="<?= esc(
  $transitionImageName,
) ?>" loading="lazy">
                </figure>
              </div>
            <?php endif; ?>

            <section class="participant-panel participant-stats" aria-labelledby="participant-stats-heading">
              <h2 id="participant-stats-heading">Statistik</h2>
              <dl class="participant-data-list">
                <div>
                  <dt>Teilnahmen</dt>
                  <dd><?= $totalParticipationCount ?></dd>
                </div>
                <?php if ($roleValue === 'host' || $hostCount > 0): ?>
                  <div>
                    <dt><?= $roleValue === 'host' ? 'im Team' : 'moderiert' ?></dt>
                    <dd><?= $hostCount ?></dd>
                  </div>
                <?php endif; ?>
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

            <?php if ($profileLinks !== []): ?>
              <section class="participant-panel participant-profiles" aria-labelledby="participant-profiles-heading">
                <h2 id="participant-profiles-heading">Im Netz</h2>
                <ul class="participant-profile-list">
                  <?php foreach ($profileLinks as $profileLink): ?>
                    <li>
                      <a href="<?= esc($profileLink['url']) ?>" target="_blank" rel="noopener">
                        <span class="participant-profile-icon" aria-hidden="true">
                          <?= tw_sprite_icon(
                            kirby()->root('base') .
                              '/src/assets/social/' .
                              $profileLink['network'] .
                              '.svg',
                            '/dist/assets/social.svg',
                            $profileLink['network'],
                          ) ?>
                        </span>
                        <span><?= str_replace('@', '<wbr>@', esc($profileLink['label'])) ?></span>
                        <span class="sr-only">(externer Link, öffnet in neuem Tab)</span>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </section>
            <?php endif; ?>
          </section>
        </div>
      </aside>

      <div class="participant-content content-text">
        <?php if ($page->description()->isNotEmpty()): ?>
          <?= $page->description()->kt() ?>
        <?php endif; ?>

        <?php if ($teamRoles !== []): ?>
          <p><?= esc(
            $fullName !== '' ? $fullName : $page->title()->value(),
          ) ?> ist bei Technikwürze <?= esc($teamRolesSentence) ?>.</p>
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
                  <a href="<?= $episode->url() ?>" class="episode-title">
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

    <?php snippet('participant-pagination', ['page' => $page]); ?>
  </article>
<?php endslot(); ?>
<?php endsnippet(); ?>
