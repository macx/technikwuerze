<?php

declare(strict_types=1);

use Kirby\Cms\Page;
use Kirby\Data\Data;

if (!function_exists('twSortExternalProfilesLinkedinFirst')) {
  function twSortExternalProfilesLinkedinFirst(Page $page): void
  {
    if ($page->intendedTemplate()->name() !== 'participant') {
      return;
    }

    $profiles = $page->external_profiles()->yaml();
    $sorted = array_merge(
      array_filter($profiles, fn(array $profile) => ($profile['network'] ?? '') === 'linkedin'),
      array_filter($profiles, fn(array $profile) => ($profile['network'] ?? '') !== 'linkedin'),
    );

    if ($sorted === array_values($profiles)) {
      return;
    }

    $page->save(['external_profiles' => Data::encode($sorted, 'yaml')]);
  }
}
