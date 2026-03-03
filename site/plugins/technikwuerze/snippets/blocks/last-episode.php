<?php
/**
 * @var Kirby\Cms\Block $block
 */

$headline = trim((string) $block->header()->value());
if ($headline === '') {
  $headline = 'Aktuelle Folge';
}
$podloveTemplate = asset('assets/podlove/last-episode-template.html')->url();

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

<?php if ($latestEpisode): ?>
  <section class="tw-last-episode">
    <div class="tw-last-episode-intro">
      <h2><?= esc($headline) ?></h2>

      <p class="tw-last-episode-text">
        <?= esc($latestEpisode->podcasterdescription()->short(120)) ?>
      </p>

      <div class="tw-last-episode-actions">
        <a class="c-button primary" data-icon-position="right" href="<?= $latestEpisode->url() ?>">
          <span class="c-button__icon msi-arrow-forward" aria-hidden="true"></span>
          <span>Zur Folge</span>
        </a>
      </div>
    </div>

    <article class="tw-last-episode-player">
      <?php snippet('podcaster-player', [
        'page' => $latestEpisode,
        'template' => $podloveTemplate,
        'transparent' => true,
      ]); ?>
    </article>
  </section>
<?php endif; ?>
