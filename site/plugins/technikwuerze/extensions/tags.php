<?php

declare(strict_types=1);

use Kirby\Toolkit\Html;

$renderText = static fn(string $value): string => Html::encode(trim($value));

$renderBadgeTone = static function (mixed $tone): string {
  $value = strtolower(trim((string) $tone));

  return match ($value) {
    'ok', 'success', 'green' => 'ok',
    'warn', 'warning', 'yellow' => 'warn',
    'danger', 'error', 'red' => 'danger',
    'info', 'blue' => 'info',
    default => 'default',
  };
};

return [
  'small' => [
    'attr' => ['tone'],
    'html' => static function ($tag) use ($renderText): string {
      $text = trim((string) $tag->value);
      if ($text === '') {
        return '';
      }

      $classes = ['text-xs'];
      if (strtolower(trim((string) $tag->tone)) === 'light') {
        $classes[] = 'text-light';
      }

      return Html::tag('small', $renderText($text), ['class' => implode(' ', $classes)]);
    },
  ],
  'kbd' => [
    'html' => static function ($tag) use ($renderText): string {
      $text = trim((string) $tag->value);
      if ($text === '') {
        return '';
      }

      return Html::tag('kbd', $renderText($text), ['class' => 'tw-kbd']);
    },
  ],
  'badge' => [
    'attr' => ['tone'],
    'html' => static function ($tag) use ($renderText, $renderBadgeTone): string {
      $text = trim((string) $tag->value);
      if ($text === '') {
        return '';
      }

      $tone = $renderBadgeTone($tag->tone);
      $classes = ['tw-badge'];

      if ($tone !== 'default') {
        $classes[] = $tone;
      }

      return Html::tag('span', $renderText($text), [
        'class' => implode(' ', $classes),
      ]);
    },
  ],
  'abbr' => [
    'attr' => ['title'],
    'html' => static function ($tag) use ($renderText): string {
      $text = trim((string) $tag->value);
      if ($text === '') {
        return '';
      }

      $title = trim((string) $tag->title);

      return Html::tag('abbr', $renderText($text), [
        'class' => 'tw-abbr',
        'title' => $title !== '' ? $title : $text,
      ]);
    },
  ],
  'hinweis' => [
    'attr' => ['title'],
    'html' => static function ($tag) use ($renderText): string {
      $text = trim((string) $tag->value);
      if ($text === '') {
        return '';
      }

      $title = trim((string) $tag->title);
      if ($title === '') {
        $title = 'Hinweis';
      }

      return '<aside class="tw-aside tw-aside-note"><p><strong>' .
        $renderText($title) .
        ':</strong> ' .
        $renderText($text) .
        '</p></aside>';
    },
  ],
  'note' => [
    'attr' => ['title'],
    'html' => static function ($tag) use ($renderText): string {
      $text = trim((string) $tag->value);
      if ($text === '') {
        return '';
      }

      $title = trim((string) $tag->title);
      if ($title === '') {
        $title = 'Hinweis';
      }

      return '<aside class="tw-aside tw-aside-note"><p><strong>' .
        $renderText($title) .
        ':</strong> ' .
        $renderText($text) .
        '</p></aside>';
    },
  ],
];
