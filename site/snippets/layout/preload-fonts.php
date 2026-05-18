<?php

$fonts = [
  'bricolage' => [
    'manifest_key' => 'bricolage-grotesque-latin-standard-normal.woff2',
    'dev_path' =>
      '/node_modules/@fontsource-variable/bricolage-grotesque/files/bricolage-grotesque-latin-standard-normal.woff2',
  ],
  'manrope' => [
    'manifest_key' => 'manrope-latin-wght-normal.woff2',
    'dev_path' =>
      '/node_modules/@fontsource-variable/manrope/files/manrope-latin-wght-normal.woff2',
  ],
];

$isDev = F::exists(kirby()->root('base') . '/.dev');
$manifest = null;

if (!$isDev) {
  $manifestPath = kirby()->root('base') . '/public/dist/manifest.json';
  if (F::exists($manifestPath)) {
    $manifest = Data::read($manifestPath);
  }
}

foreach ($fonts as $key => $config) {
  $url = null;

  if ($isDev) {
    // In Dev-Mode it is not necessary to preload fonts since they load from localhost almost instantly.
    // Also, guessing the correct local Vite path (@fs vs relative) often triggers "preload but not used" warnings.
    $url = null;
  } elseif ($manifest) {
    foreach ($manifest as $manifestKey => $info) {
      if (str_contains($manifestKey, $config['manifest_key'])) {
        $url = site()->url() . '/dist/' . $info['file'];
        break;
      }
    }
  }

  if ($url) {
    echo '  <link rel="preload" href="' .
      $url .
      '" as="font" type="font/woff2" crossorigin>' .
      "\n";
  }
}
