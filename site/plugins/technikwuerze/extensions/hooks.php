<?php

declare(strict_types=1);

return [
  'file.create:after' => function ($file) {
    twGenerateParticipantProfileVariants($file);
  },
  'file.replace:after' => function ($newFile, $oldFile) {
    twGenerateParticipantProfileVariants($newFile);
  },
];
