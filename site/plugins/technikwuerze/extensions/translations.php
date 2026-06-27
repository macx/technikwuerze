<?php

// Expose German translations as English fallback for single-language setup
$kommentsTranslations = @include dirname(__DIR__, 2) . '/komments/plugin/translations.php';
$kommentsDe =
  $kommentsTranslations && isset($kommentsTranslations['de']) ? $kommentsTranslations['de'] : [];

// https://maurice-renck.de/de/kirby/komments/reference/translations
$customTranslations = [
  'mauricerenck.komments.form.title' => 'Kommentar hinterlassen',
  'mauricerenck.komments.form.submit' => 'Absenden',
  'mauricerenck.komments.action.reply.text' => 'Antworten',
  'mauricerenck.komments.form.privacy' =>
    'Mit dem Absenden stimmst du zu, dass deine eingegebenen Daten gespeichert und als Kommentar veröffentlicht werden dürfen (weitere Infos in der <a href="/datenschutz">Datenschutzerklärung</a>). Dein Beitrag spiegelt deine persönliche Meinung wider – bitte behandle andere respektvoll und halte dich an geltendes Recht. Rechtswidrige Inhalte behalten wir uns vor zu entfernen.',
];

return [
  'en' => array_merge($kommentsDe, $customTranslations),
];
