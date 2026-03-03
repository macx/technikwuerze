<?php

declare(strict_types=1);

$targets = [
  __DIR__ . '/../site/plugins/podcaster/lib/AudioTools.php',
  __DIR__ . '/../vendor/mauricerenck/podcaster/lib/AudioTools.php',
];

$needle = "\$playTime = round(\$id3Data['playtime_seconds']);";
$replacement = <<<'PHP'
$playTime = 0;
        if (isset($id3Data['playtime_seconds']) && is_numeric($id3Data['playtime_seconds'])) {
            $playTime = (int) round((float) $id3Data['playtime_seconds']);
        } elseif (isset($id3Data['playtime_string']) && is_string($id3Data['playtime_string'])) {
            $parts = array_map('intval', explode(':', $id3Data['playtime_string']));
            if (count($parts) === 3) {
                $playTime = ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
            } elseif (count($parts) === 2) {
                $playTime = ($parts[0] * 60) + $parts[1];
            }
        }
PHP;

foreach ($targets as $file) {
  if (!is_file($file)) {
    continue;
  }

  $content = file_get_contents($file);
  if (!is_string($content)) {
    continue;
  }

  if (str_contains($content, $replacement)) {
    echo "Already patched: {$file}\n";
    continue;
  }

  if (!str_contains($content, $needle)) {
    echo "Skip (pattern not found): {$file}\n";
    continue;
  }

  $updated = str_replace($needle, $replacement, $content);
  if ($updated === $content) {
    echo "Skip (no changes): {$file}\n";
    continue;
  }

  file_put_contents($file, $updated);
  echo "Patched: {$file}\n";
}
