<?php

declare(strict_types=1);

return [
  'routes' => [
    [
      'pattern' => 'tw-search/reindex/(:any)',
      'method' => 'POST',
      'action' => function (string $scope) {
        if (!kirby()->user()) {
          return [
            'status' => 401,
            'message' => 'Unauthorized',
          ];
        }

        $count = twSearchReindexScope($scope);

        return [
          'status' => 200,
          'scope' => $scope,
          'count' => $count,
          'indexedTotal' => twSearchLoupe()->countDocuments(),
        ];
      },
    ],
  ],
];
