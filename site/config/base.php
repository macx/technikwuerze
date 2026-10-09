<?php

$projectRoot = dirname(__DIR__, 2);
$dbPath = $projectRoot . '/content/.db/';
$emailOptions = require __DIR__ . '/email.php';

return [
  'ready' => static function ($kirby) {
    $cacheRoot = $kirby->root('cache');
    if (!is_string($cacheRoot) || trim($cacheRoot) === '') {
      return [];
    }

    $loupePath = rtrim($cacheRoot, '/') . '/kirby-loupe';
    if (!is_dir($loupePath)) {
      @mkdir($loupePath, 0777, true);
    }

    return [];
  },

  'panel' => [
    'css' => 'assets/panel.css',
  ],

  // Enable tables, definition lists, footnotes syntax etc. in Markdown/Kirbytext
  'routes' => [
    [
      'pattern' => 'teilnehmende/daniel-jagzent',
      'action' => fn() => go('teilnehmende/daniel-jagszent', 301),
    ],
    [
      'pattern' => 'teilnehmende/ansger-hein',
      'action' => fn() => go('teilnehmende/ansgar-hein', 301),
    ],
    [
      'pattern' => 'teilnehmende/nadja-mueller-schade',
      'action' => fn() => go('teilnehmende/nadja-katzer', 301),
    ],
    [
      'pattern' => 'feed',
      'action' => fn() => go('mediathek/feed', 301),
    ],
  ],

  'markdown' => [
    'extra' => true,
  ],

  'arnoson.kirby-form-builder' => [
    'clientValidation' => true,
    'gridColumns' => 6,
    'autoComplete' => true,
    'addEmptyPlaceholder' => true,
    'defaultEntryStatus' => 'draft',
    'fromEmails' => array_values(array_filter([$emailOptions['email']['noreply'] ?? null])),
  ],

  // Podcaster setup (analytics + player metadata)
  'mauricerenck.podcaster' => [
    'statsInternal' => true,
    'statsType' => 'sqlite',
    'sqlitePath' => $dbPath,
    'doNotTrackBots' => true,
    'useApi' => false,
    'setId3Data' => true,
    'feed' => [
      'uuid' => true,
    ],
  ],

  // Audio metadata extraction
  'tw.audioDuration.ffprobeBin' => 'ffprobe',
  'tw.audioCover.ffmpegBin' => 'ffmpeg',

  // Komments setup
  'mauricerenck.indieConnector.sqlitePath' => $dbPath,
  'mauricerenck.indieConnector.stats.enabled' => true,
  // Webmentions senden: nur live (siehe config.technikwuerze.de.php) und nur beim
  // Statuswechsel (Veröffentlichen) einer Folge, nicht bei jeder Änderung.
  'mauricerenck.indieConnector.send.enabled' => false,
  'mauricerenck.indieConnector.send.automatically' => false,
  'mauricerenck.indieConnector.send.allowedTemplates' => ['episode'],
  'mauricerenck.indieConnector.send.url-fields' => ['blocks:block', 'podcasterdescription:text'],

  'mauricerenck.komments.storage.type' => 'sqlite',
  'mauricerenck.komments.storage.sqlitePath' => $dbPath,
  'mauricerenck.komments.panel.enabled' => true,
  'mauricerenck.komments.panel.webmentions' => true,
  'mauricerenck.komments.panel.showPublished' => true,
  'mauricerenck.komments.privacy.storeEmail' => true,
  'mauricerenck.komments.avatar.service' => 'initials',
  'mauricerenck.komments.avatar.webmentionAvatars' => false,
  'mauricerenck.komments.autoDisable.datefield' => 'date',
  'mauricerenck.komments.form.submit.classNames' => 'button',
  'mauricerenck.komments.notifications.email.enable' => true,
  'mauricerenck.komments.notifications.email.sender' => $_ENV['TW_MAIL_NOREPLY'] ?? null,
  'mauricerenck.komments.notifications.email.emailReceiverList' => array_filter([
    $_ENV['TW_CONTACT_RECIPIENT'] ?? null,
  ]),
  'mauricerenck.komments.notifications.email.notificationMode' => 'deferred',
  'mauricerenck.komments.notifications.skipSpam' => true,
];
