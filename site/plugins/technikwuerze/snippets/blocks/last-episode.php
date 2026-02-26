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
$latestEpisode = null;
$latestSeasonTs = -1;

if ($mediathek) {
  $seasons = $mediathek->children()->filterBy('intendedTemplate', 'season')->published();

  foreach ($seasons as $season) {
    $seasonLatestEpisode = $season
      ->children()
      ->filterBy('intendedTemplate', 'episode')
      ->published()
      ->sortBy('date', 'desc')
      ->first();

    if (!$seasonLatestEpisode) {
      continue;
    }

    $seasonLatestTs = (int) ($seasonLatestEpisode->date()->toDate('U') ?? 0);
    if ($seasonLatestTs > $latestSeasonTs) {
      $latestSeasonTs = $seasonLatestTs;
      $latestEpisode = $seasonLatestEpisode;
    }
  }
}
?>

<?php if ($latestEpisode): ?>
  <section class="tw-last-episode">
    <div class="tw-last-episode-layout">
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
          'debug' => true,
        ]); ?>
      </article>
    </div>
  </section>
<?php endif; ?>
