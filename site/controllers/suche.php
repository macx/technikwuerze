<?php

declare(strict_types=1);

return function () {
  $query = trim((string) get('q'));
  $category = twSearchNormalizeCategory((string) get('category'));
  $currentPage = max(1, (int) get('p'));
  $settings = twSearchSettings();

  $results = twSearchSearch($query, $category, (int) $settings['results_limit'], $currentPage);

  return [
    'query' => $query,
    'category' => $category,
    'categories' => twSearchCategories(),
    'settings' => $settings,
    'results' => $results['hits'],
    'total' => $results['total'],
    'currentPage' => $results['page'],
    'totalPages' => $results['pages'],
    'perPage' => $results['limit'],
  ];
};
