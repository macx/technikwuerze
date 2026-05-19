<?php

// Expose German translations as English fallback for single-language setup
$kommentsTranslations = @include dirname(__DIR__, 2) . '/komments/plugin/translations.php';
$kommentsDe =
  $kommentsTranslations && isset($kommentsTranslations['de']) ? $kommentsTranslations['de'] : [];

// https://maurice-renck.de/de/kirby/komments/reference/translations
$customTranslations = [
  'mauricerenck.komments.form.title' => 'Kommentar hinterlassen',
  'mauricerenck.komments.form.submit' => 'Abschicken',
  'mauricerenck.komments.action.reply.text' => 'Antworten',
];

return [
  'en' => array_merge($kommentsDe, $customTranslations),
];
