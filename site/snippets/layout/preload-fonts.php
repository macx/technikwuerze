<?php

$fonts = ['McQueenVAR.woff2', 'CaseVAR.woff2'];

foreach ($fonts as $file) {
  $url = $kirby->url('assets') . '/fonts/' . $file;
  echo '  <link rel="preload" href="' . $url . '" as="font" type="font/woff2" crossorigin>' . "\n";
}
