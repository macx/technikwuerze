<?php

declare(strict_types=1);

$targets = [
  __DIR__ . '/../site/plugins/podcaster/lib/AudioTools.php',
  __DIR__ . '/../vendor/mauricerenck/podcaster/lib/AudioTools.php',
];
$hookTargets = [
  __DIR__ . '/../site/plugins/podcaster/plugin/hooks.php',
  __DIR__ . '/../vendor/mauricerenck/podcaster/plugin/hooks.php',
];

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
        if ($playTime <= 0) {
            $ffprobeBin = 'ffprobe';
            if (function_exists('kirby')) {
                $ffprobeBin = (string) \kirby()->option('tw.audioDuration.ffprobeBin', 'ffprobe');
            }
            $root = $audioFile->root();
            if (is_string($root) && $root !== '' && is_file($root)) {
                $cmd = sprintf(
                    '%s -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s 2>/dev/null',
                    escapeshellarg($ffprobeBin),
                    escapeshellarg($root),
                );
                $raw = shell_exec($cmd);
                if (is_string($raw)) {
                    $seconds = (float) trim($raw);
                    if ($seconds > 0) {
                        $playTime = (int) round($seconds);
                    }
                }
            }
        }
        if ($playTime <= 0) {
            // keep upload flow running on systems without ffprobe
        }
PHP;

$titleNeedle = '$title = $this->getId3Tag(\'title\', $id3Data);';
$titleReplacement = <<<'PHP'
$title = $this->getId3Tag('title', $id3Data);
        if (!is_string($title) || trim($title) === '') {
            $title = $this->getAudioTitleFromFfprobe($audioFile);
        }
PHP;

$titleMethodNeedle =
  "    public function getId3Tag(\$tag, \$id3)\n    {\n        return (isset(\$id3['tags']['id3v2'][\$tag]) && isset(\$id3['tags']['id3v2'][\$tag][0])) ? \$id3['tags']['id3v2'][\$tag][0] : null;\n    }\n";
$titleMethodAppend = <<<'PHP'
    public function getAudioTitleFromFfprobe($audioFile): ?string
    {
        $ffprobeBin = 'ffprobe';
        if (function_exists('kirby')) {
            $ffprobeBin = (string) \kirby()->option('tw.audioDuration.ffprobeBin', 'ffprobe');
        }
        $root = $audioFile->root();
        if (!is_string($root) || $root === '' || !is_file($root)) {
            return null;
        }
        $cmd = sprintf(
            '%s -v error -show_entries format_tags=title -of default=noprint_wrappers=1:nokey=1 %s 2>/dev/null',
            escapeshellarg($ffprobeBin),
            escapeshellarg($root),
        );
        $raw = shell_exec($cmd);
        if (!is_string($raw)) {
            return null;
        }
        $title = trim($raw);
        if ($title === '') {
            return null;
        }
        return $title;
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

  if (
    str_contains(
      $content,
      '$duration = $playTime > 0 ? $this->convertAudioDuration($playTime) : null;',
    ) &&
    str_contains($content, "if (\$duration !== null)")
  ) {
    echo "Already patched: {$file}\n";
    continue;
  }

  $pattern = '/\$playTime\s*=.*?\n\s*\$duration = \$this->convertAudioDuration\(\$playTime\);/s';
  if (!preg_match($pattern, $content)) {
    echo "Skip (pattern not found): {$file}\n";
    continue;
  }

  $updated = preg_replace(
    $pattern,
    $replacement . "\n\n        \$duration = \$this->convertAudioDuration(\$playTime);",
    $content,
    1,
  );
  if (!is_string($updated)) {
    echo "Skip (replace failed): {$file}\n";
    continue;
  }

  if ($updated === $content) {
    echo "Skip (no changes): {$file}\n";
    continue;
  }

  $updated = str_replace(
    "throw new \\Kirby\\Exception\\Exception(['details' => 'audio duration could not be determined']);",
    '// keep upload flow running on systems without ffprobe',
    $updated,
  );

  $updated = str_replace(
    '$duration = $this->convertAudioDuration($playTime);',
    '$duration = $playTime > 0 ? $this->convertAudioDuration($playTime) : null;',
    $updated,
  );

  $updated = str_replace(
    "\$audioFile->update([\n            'episodeTitle' => \$title,\n            'duration' => \$duration,\n            'guid' => md5(time()),\n        ]);",
    "\$updates = [\n            'episodeTitle' => \$title,\n            'guid' => md5(time()),\n        ];\n        if (\$duration !== null) {\n            \$updates['duration'] = \$duration;\n        }\n\n        \$audioFile->update(\$updates);",
    $updated,
  );

  if (str_contains($updated, "getAudioTitleFromFfprobe(\$audioFile)") !== true) {
    if (str_contains($updated, $titleNeedle)) {
      $updated = str_replace($titleNeedle, $titleReplacement, $updated);
    }
  }

  if (str_contains($updated, 'function getAudioTitleFromFfprobe(') !== true) {
    if (str_contains($updated, $titleMethodNeedle)) {
      $updated = str_replace(
        $titleMethodNeedle,
        $titleMethodNeedle . "\n" . $titleMethodAppend,
        $updated,
      );
    }
  }

  file_put_contents($file, $updated);
  echo "Patched: {$file}\n";
}

$hookNeedleCreate = "'file.create:after' => function (\$file) {\n";
$hookReplacementCreate = <<<'PHP'
'file.create:after' => function ($file) {
        if (!$file instanceof File) {
            return;
        }

PHP;

$hookNeedleReplace = "'file.replace:after' => function (\$file) {\n";
$hookReplacementReplace = <<<'PHP'
'file.replace:after' => function ($newFile, $oldFile = null) {
        if (!$newFile instanceof File) {
            return;
        }

PHP;

$hookCatchOld = "throw new Exception(['details' => 'the audio id3 data could not be read']);";
$hookCatchNew = <<<'PHP'
throw new Exception([
                'fallback' => 'the audio id3 data could not be read',
                'details' => [],
            ]);
PHP;

foreach ($hookTargets as $file) {
  if (!is_file($file)) {
    continue;
  }

  $content = file_get_contents($file);
  if (!is_string($content)) {
    continue;
  }

  if (
    str_contains($content, 'if (!$file instanceof File)') &&
    str_contains($content, 'if (!$newFile instanceof File)') &&
    str_contains($content, "'details' => []")
  ) {
    echo "Already patched: {$file}\n";
    continue;
  }

  if (str_contains($content, 'use Kirby\Cms\File;') !== true) {
    $content = str_replace(
      "namespace mauricerenck\\Podcaster;\n\n",
      "namespace mauricerenck\\Podcaster;\n\nuse Kirby\\Cms\\File;\n",
      $content,
    );
  }

  $content = str_replace($hookNeedleCreate, $hookReplacementCreate, $content);
  $content = str_replace($hookNeedleReplace, $hookReplacementReplace, $content);
  $content = str_replace("parseAndWriteId3(\$file,", "parseAndWriteId3(\$newFile,", $content);
  $content = str_replace('catch (Exception $e)', 'catch (\Throwable $e)', $content);
  $content = str_replace($hookCatchOld, $hookCatchNew, $content);

  file_put_contents($file, $content);
  echo "Patched: {$file}\n";
}
