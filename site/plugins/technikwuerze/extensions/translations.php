<?php

// Expose German translations as English fallback for single-language setup
$kommentsTranslations = @include dirname(__DIR__, 2) . '/komments/plugin/translations.php';
$kommentsDe =
  $kommentsTranslations && isset($kommentsTranslations['de']) ? $kommentsTranslations['de'] : [];

$customTranslations = [
  'mauricerenck.komments.form.title' => 'Kommentar hinterlassen',
  'mauricerenck.komments.form.submit' => 'Abschicken',
];

return [
  'en' => array_merge($kommentsDe, $customTranslations),
];
