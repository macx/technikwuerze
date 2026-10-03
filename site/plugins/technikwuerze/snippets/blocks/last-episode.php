<?php
/**
 * @var Kirby\Cms\Block $block
 */

$headline = trim((string) $block->header()->value());
if ($headline === '') {
  $headline = 'Aktuelle Folge';
}
$podloveTemplate = asset('assets/podlove/tw-player-template.html')->url();

$mediathek = site()->find('mediathek');
$episodeCandidates = $mediathek
  ?->index()
  ->filterBy('intendedTemplate', 'episode')
  ->listed()
  ->sortBy('date', 'desc', 'podcasterseason', 'desc', 'podcasterepisode', 'desc');

$latestEpisode = null;
if ($episodeCandidates && $episodeCandidates->isNotEmpty()) {
  $podcast = new \mauricerenck\Podcaster\Podcast();
  foreach ($episodeCandidates as $candidate) {
    if ($podcast->getAudioFile($candidate) !== null) {
      $latestEpisode = $candidate;
      break;
    }
  }
}
?>

<?php
$isStale = false;
if ($latestEpisode) {
  twShownEpisodes($latestEpisode);

  $staleAfterDays = $block->staleAfterDays()->or(60)->toInt();
  $episodeTimestamp = $latestEpisode->date()->toTimestamp();
  $isStale =
    $episodeTimestamp !== false && $episodeTimestamp < strtotime('-' . $staleAfterDays . ' days');

  if ($isStale) {
    $headline = trim((string) $block->staleHeader()->or('Zuletzt erschienen')->value());
  }
}
?>

<?php if ($latestEpisode): ?>
  <section class="tw-last-episode content narrow">
    <?php snippet(
      'podcast-player',
      [
        'page' => $latestEpisode,
        'template' => $podloveTemplate,
        'transparent' => true,
        'containerClass' => '',
      ],
      slots: true,
    ); ?>
      <?php slot(); ?>
        <h2><?= esc($headline) ?></h2>

        <?php if ($isStale): ?>
          <p class="podcast-player-date text-s">
            Erschienen am <?= $latestEpisode->date()->toDate('d.m.Y') ?>
          </p>

          <?php if ($block->staleNotice()->isNotEmpty()): ?>
            <p class="podcast-player-notice">
              <?= $block->staleNotice()->kti() ?>
            </p>
          <?php endif; ?>
        <?php endif; ?>

        <p class="podcast-player-text">
          <?= $latestEpisode->podcasterdescription()->kti()->short(150) ?>
        </p>

        <div class="podcast-player-actions">
          <a href="<?= $latestEpisode->url() ?>" class="button-primary" data-icon-position="right" style="--color-scheme: var(--clr-secondary)">
            <i class="msi-arrow-forward" aria-hidden="true"></i>
            <span>Zur Folge</span>
          </a>
        </div>
      <?php endslot(); ?>
    <?php endsnippet(); ?>
  </section>
<?php endif; ?>
