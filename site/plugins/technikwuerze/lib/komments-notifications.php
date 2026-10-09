<?php

use mauricerenck\Komments\KommentNotifications;

function twSendKommentNotificationsAfterResponse(): void
{
  register_shutdown_function(static function (): void {
    if (function_exists('fastcgi_finish_request')) {
      fastcgi_finish_request();
    }

    try {
      (new KommentNotifications())->sendNotifications();
    } catch (Throwable $exception) {
      error_log('Komments notification failed: ' . $exception->getMessage());
    }
  });
}
