<?php
$footerPages = [
  ['id' => 'impressum', 'label' => 'Impressum'],
  ['id' => 'datenschutz', 'label' => 'Datenschutz'],
  ['id' => 'kontakt', 'label' => 'Kontakt'],
];

$footerPageLinks = [];
foreach ($footerPages as $item) {
  $footerPage = site()->find($item['id']);
  if ($footerPage !== null) {
    $footerPageLinks[] = [
      'label' => $item['label'],
      'url' => $footerPage->url(),
    ];
  }
}

if (kirby()->user() !== null && isset($page) && $page !== null) {
  $footerPageLinks[] = [
    'label' => 'Seite bearbeiten',
    'url' => $page->panel()->url(),
  ];
}

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

  $socialLinks[] = [
    'network' => $network,
    'url' => $url,
  ];
}
?>

<footer class="site-footer">
  <?php snippet('layout/sponsors'); ?>

  <div class="about-us">
    <div class="content inner">
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
            <span class="handwriting">
              <?= site()->content()->get('footerContactLabel')->kti() ?>
            </span>
            <?= site()->content()->get('footerContactHint')->kti() ?>
          </p>
        </div>

        <?php snippet('social-links', [
          'links' => $socialLinks,
          'label' => 'Social Media',
          'class' => 'footer-social',
        ]); ?>
      </div>
    </div>
  </div>
</footer>
