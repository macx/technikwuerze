<?php

$mailNoreply = $_ENV['TW_MAIL_NOREPLY'] ?? getenv('TW_MAIL_NOREPLY') ?: null;
$contactRecipient = $_ENV['TW_CONTACT_RECIPIENT'] ?? getenv('TW_CONTACT_RECIPIENT') ?: null;
$smtpHost = $_ENV['TW_SMTP_HOST'] ?? getenv('TW_SMTP_HOST') ?: '';
$smtpPort = (int) ($_ENV['TW_SMTP_PORT'] ?? getenv('TW_SMTP_PORT') ?: 587);
$smtpSecurity = $_ENV['TW_SMTP_SECURITY'] ?? getenv('TW_SMTP_SECURITY') ?: 'tls';
$smtpUser = $_ENV['TW_SMTP_USER'] ?? getenv('TW_SMTP_USER') ?: '';
$smtpPass = $_ENV['TW_SMTP_PASS'] ?? getenv('TW_SMTP_PASS') ?: '';

$emailConfig = [];

if ($mailNoreply !== null) {
  $emailConfig['noreply'] = $mailNoreply;
}

if ($mailNoreply !== null && $contactRecipient !== null) {
  $emailConfig['presets'] = [
    'uniform-contact' => [
      'from' => $mailNoreply,
      'to' => $contactRecipient,
      'subject' => 'Kontaktformular - Technikwuerze',
    ],
  ];
}

if ($smtpHost !== '' && $smtpUser !== '' && $smtpPass !== '') {
  $emailConfig['transport'] = [
    'type' => 'smtp',
    'host' => $smtpHost,
    'port' => $smtpPort,
    'security' => $smtpSecurity,
    'auth' => true,
    'username' => $smtpUser,
    'password' => $smtpPass,
  ];
}

return [
  // Mail defaults for form plugins (Kirby Email Manager + Uniform)
  'email' => $emailConfig,
];
