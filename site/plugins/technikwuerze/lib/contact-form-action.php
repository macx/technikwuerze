<?php

declare(strict_types=1);

use Kirby\Exception\Exception as KirbyException;
use Uniform\Actions\Action;

class TwContactFormAction extends Action
{
  public function perform()
  {
    $config = option('tw.contactForm', []);
    $recipient = $config['recipient'] ?? null;
    $noreply = $config['noreply'] ?? null;

    if (!is_string($recipient) || trim($recipient) === '') {
      $this->fail('Das Kontaktformular hat keinen Empfänger.');
    }

    if (!is_string($noreply) || trim($noreply) === '') {
      $this->fail('Das Kontaktformular hat keinen Absender.');
    }

    $page = $this->option('page');
    $data = $this->form->data();
    $senderEmail = $data['email'] ?? null;

    try {
      kirby()->email([
        'to' => $recipient,
        'from' => $noreply,
        'replyTo' => $senderEmail ?: null,
        'subject' => $config['notificationSubject'] ?? 'Neue Nachricht über das Kontaktformular',
        'template' => 'contact-notification',
        'data' => [
          'page' => $page,
          'name' => $data['name'] ?? '',
          'email' => $senderEmail ?? '',
          'message' => $data['message'] ?? '',
        ],
      ]);

      if (is_string($senderEmail) && trim($senderEmail) !== '') {
        kirby()->email([
          'to' => $senderEmail,
          'from' => $noreply,
          'replyTo' => $recipient,
          'subject' => $config['confirmationSubject'] ?? 'Danke für deine Nachricht',
          'template' => 'contact-confirmation',
          'data' => [
            'page' => $page,
            'name' => $data['name'] ?? '',
          ],
        ]);
      }
    } catch (Throwable $error) {
      $message = 'Die Nachricht konnte nicht versendet werden.';

      if (option('debug') === true) {
        $message .= ' ' . $error->getMessage();
      }

      if ($error instanceof KirbyException && $error->getMessage() !== '') {
        $message = $error->getMessage();
      }

      $this->fail($message);
    }
  }
}
