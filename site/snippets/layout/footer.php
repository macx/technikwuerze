<?php
$footerPages = [
  ['id' => 'impressum', 'label' => 'Impressum'],
  ['id' => 'datenschutz', 'label' => 'Datenschutz'],
  ['id' => 'kontakt', 'label' => 'Kontakt'],
];

$footerPageLinks = [];
foreach ($footerPages as $item) {
  $page = site()->find($item['id']);
  if ($page !== null) {
    $footerPageLinks[] = [
      'label' => $item['label'],
      'url' => $page->url(),
    ];
  }
}

$socialLabels = [
  'bluesky' => 'Bluesky',
  'mastodon' => 'Mastodon',
  'threads' => 'Threads',
  'instagram' => 'Instagram',
  'tiktok' => 'TikTok',
  'youtube' => 'YouTube',
  'discord' => 'Discord',
  'linkedin' => 'LinkedIn',
  'twitch' => 'Twitch',
  'x' => 'X',
];

$socialLinks = [];
foreach (site()->content()->get('socialLinks')->toStructure() as $social) {
  $network = trim((string) $social->network()->value());
  $url = trim((string) $social->url()->value());

  if ($network === '' || $url === '') {
    continue;
  }

  if (filter_var($url, FILTER_VALIDATE_URL) === false) {
    continue;
  }

  $iconPath = 'assets/social/' . $network . '.svg';
  $iconRoot = kirby()->root('index') . '/' . $iconPath;

  if (!is_file($iconRoot)) {
    $iconPath = 'assets/social/placeholder.svg';
  }

  $socialLinks[] = [
    'label' => $socialLabels[$network] ?? ucfirst($network),
    'url' => $url,
    'iconPath' => $iconPath,
  ];
}
?>

<footer class="site-footer">
  <section class="gold-sponsors">
    <header class="hand-writing">
      <?= site()->content()->get('footerSponsorsHint')->esc() ?>
    </header>

    <div class="sponsors">
      <a href="https://www.mittwald.de/" class="sponsor" target="_blank" rel="noopener" data-sponsor="mittwald">
        <?= asset('assets/logos/mittwald.svg')->read() ?>
      </a>

      <a href="https://getkirby.com/" class="sponsor" target="_blank" rel="noopener" data-sponsor="kirby">
        <?= asset('assets/logos/kirby.svg')->read() ?>
      </a>
    </div>
  </section>

  <div class="about-us">
    <div class="inner">
      <p class="about-us">
        <?= site()->content()->get('footerAboutUs')->kti() ?>
      </p>

      <div class="footer-bottom">
        <div class="footer-meta">
          <nav class="footer-meta-links" aria-label="Rechtliches und Kontakt">
            <?php foreach ($footerPageLinks as $link): ?>
              <a href="<?= $link['url'] ?>">
                <?= esc($link['label']) ?>
              </a>
            <?php endforeach; ?>
          </nav>

          <p class="footer-contact-hint">
            <?= site()->content()->get('footerContactHint')->kti() ?>
          </p>
        </div>

        <nav class="footer-social" aria-label="Social Media">
          <?php foreach ($socialLinks as $social): ?>
            <a href="<?= $social[
              'url'
            ] ?>" class="footer-social-link" target="_blank" rel="noopener" aria-label="<?= esc(
  $social['label'],
) ?>">
              <?= asset($social['iconPath'])->read() ?>
            </a>
          <?php endforeach; ?>
        </nav>
      </div>
    </div>
  </div>
</footer>
