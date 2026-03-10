<?php

declare(strict_types=1);

return [
  'technikwuerze' => function () {
    $buttons = static function (string $current): array {
      return [
        [
          'text' => 'Formatierung',
          'icon' => 'text',
          'link' => 'technikwuerze/formatierung',
          'current' => $current === 'formatierung',
        ],
      ];
    };

    return [
      'label' => 'Technikwürze',
      'icon' => 'bolt',
      'menu' => true,
      'link' => 'technikwuerze/formatierung',
      'views' => [
        [
          'pattern' => 'technikwuerze/formatierung',
          'action' => function () use ($buttons) {
            return [
              'component' => 'k-tw-formatting-help-view',
              'breadcrumb' => [
                [
                  'label' => 'Technikwürze',
                  'link' => 'technikwuerze/formatierung',
                ],
              ],
              'buttons' => $buttons('formatierung'),
              'title' => 'Formatierungshilfe',
            ];
          },
        ],
      ],
    ];
  },
];
