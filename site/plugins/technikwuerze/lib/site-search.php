<?php

declare(strict_types=1);

use Kirby\Cms\Page;
use Loupe\Loupe\Configuration;
use Loupe\Loupe\Loupe;
use Loupe\Loupe\LoupeFactory;
use Loupe\Loupe\SearchParameters;

const TW_SEARCH_META_VERSION = 1;

function twSearchEnsureDirectory(string $path): string
{
  $path = trim($path);
  if ($path === '') {
    throw new RuntimeException('Search cache directory path is empty.');
  }

  if (!is_dir($path)) {
    $created = @mkdir($path, 0777, true);
    if ($created !== true && !is_dir($path)) {
      throw new RuntimeException('Could not create search directory: ' . $path);
    }
  }

  $resolvedPath = realpath($path);
  if ($resolvedPath === false) {
    throw new RuntimeException('Could not resolve search directory path: ' . $path);
  }

  return $resolvedPath;
}

function twSearchCacheRootPath(): string
{
  $cacheRoot = kirby()->root('cache');

  if (!is_string($cacheRoot) || trim($cacheRoot) === '') {
    $indexRoot = kirby()->root('index');
    if (!is_string($indexRoot) || trim($indexRoot) === '') {
      throw new RuntimeException('Kirby root(index) is not available for search cache.');
    }

    $cacheRoot = rtrim($indexRoot, '/') . '/site/cache';
  }

  return twSearchEnsureDirectory($cacheRoot);
}

function twSearchSettings(): array
{
  $defaults = [
    'results_limit' => 40,
    'comments_enabled' => true,
    'placeholder' => 'z. B. Typografie, Podcast, Kirb…',
    'dialog_title' => 'Suche',
  ];

  $searchPage = page('suche');
  if (!$searchPage) {
    return $defaults;
  }

  $resultsLimit = (int) twSearchReadPageFieldValue($searchPage, 'search_results_limit');
  if ($resultsLimit <= 0) {
    $resultsLimit = $defaults['results_limit'];
  }

  $commentsEnabledRaw = twSearchReadPageFieldValue($searchPage, 'search_comments_enabled');
  $commentsEnabled =
    $commentsEnabledRaw === ''
      ? $defaults['comments_enabled']
      : in_array(strtolower($commentsEnabledRaw), ['1', 'true', 'yes', 'on'], true);

  $placeholder = trim(twSearchReadPageFieldValue($searchPage, 'search_placeholder'));
  if ($placeholder === '') {
    $placeholder = $defaults['placeholder'];
  }

  $dialogTitle = trim(twSearchReadPageFieldValue($searchPage, 'search_dialog_title'));
  if ($dialogTitle === '') {
    $dialogTitle = $defaults['dialog_title'];
  }

  return [
    'results_limit' => $resultsLimit,
    'comments_enabled' => $commentsEnabled,
    'placeholder' => $placeholder,
    'dialog_title' => $dialogTitle,
  ];
}

function twSearchReadPageFieldValue(Page $page, string $key): string
{
  $method = str_replace('-', '_', strtolower($key));
  if (method_exists($page, $method)) {
    $value = trim((string) $page->$method()->value());
    if ($value !== '') {
      return $value;
    }
  }

  $variants = array_unique([
    $key,
    strtolower($key),
    str_replace('_', '-', $key),
    strtolower(str_replace('_', '-', $key)),
    ucfirst(str_replace('_', '-', strtolower($key))),
    str_replace('-', '_', $key),
    strtolower(str_replace('-', '_', $key)),
    ucfirst(str_replace('-', '_', strtolower($key))),
  ]);

  foreach ($variants as $variant) {
    $value = trim((string) $page->content()->get($variant)->value());
    if ($value !== '') {
      return $value;
    }
  }

  return '';
}

function twSearchCategories(): array
{
  $categories = [
    'all' => 'Alle',
    'content' => 'Inhalte',
    'episode' => 'Episoden',
    'participant' => 'Teilnehmende',
    'comment' => 'Kommentare',
  ];

  if (twSearchSettings()['comments_enabled'] !== true) {
    unset($categories['comment']);
  }

  return $categories;
}

function twSearchCategoryLabel(string $category): string
{
  return twSearchCategories()[$category] ?? twSearchCategories()['content'];
}

function twSearchNormalizeCategory(?string $category): string
{
  $value = strtolower(trim((string) $category));
  return array_key_exists($value, twSearchCategories()) ? $value : 'content';
}

function twSearchIndexPath(): string
{
  $cacheRoot = twSearchCacheRootPath();
  $indexPath = $cacheRoot . '/twz-search';

  return twSearchEnsureDirectory($indexPath);
}

function twSearchMetaPath(): string
{
  $cacheRoot = twSearchCacheRootPath();
  return $cacheRoot . '/twz-search-meta.json';
}

function twSearchLoupe(): Loupe
{
  static $loupe = null;

  if ($loupe instanceof Loupe) {
    return $loupe;
  }

  $config = Configuration::create()
    ->withPrimaryKey('id')
    ->withLanguages(['de', 'en'])
    ->withSearchableAttributes(['title_boost', 'title', 'subtitle', 'text', 'author'])
    ->withFilterableAttributes(['entity', 'pageUuid'])
    ->withSortableAttributes(['updatedTs']);

  $loupe = (new LoupeFactory())->create(twSearchIndexPath(), $config);

  return $loupe;
}

function twSearchIsIndexablePage(Page $page): bool
{
  if ($page->isDraft()) {
    return false;
  }

  if (!$page->isListed()) {
    return false;
  }

  $excludedTemplates = ['audio', 'avatars', 'covers', 'podcasterfeed'];

  return !in_array($page->intendedTemplate()->name(), $excludedTemplates, true);
}

function twSearchPageEntity(Page $page): string
{
  return match ($page->intendedTemplate()->name()) {
    'episode' => 'episode',
    'participant' => 'participant',
    default => 'content',
  };
}

function twSearchPlainText(string $value): string
{
  $value = strip_tags($value);
  $value = preg_replace('/(?:file|page|user):\/\/[^\s]+/u', ' ', $value) ?? $value;
  $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

  return trim($value);
}

function twSearchPageText(Page $page): string
{
  $chunks = [];

  foreach ($page->content()->toArray() as $key => $value) {
    if (strtolower((string) $key) === 'uuid') {
      continue;
    }

    if (is_scalar($value)) {
      $chunks[] = (string) $value;
      continue;
    }

    if (is_array($value)) {
      $chunks[] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
    }
  }

  return twSearchPlainText(implode("\n", $chunks));
}

function twSearchExcerpt(string $text, int $maxLength = 220): string
{
  $text = twSearchPlainText($text);

  if ($text === '') {
    return '';
  }

  if (mb_strlen($text) <= $maxLength) {
    return $text;
  }

  return rtrim(mb_substr($text, 0, $maxLength - 1)) . '…';
}

function twSearchTimestampFromPage(Page $page): int
{
  $dateField = $page->date();
  if ($dateField->isNotEmpty()) {
    return (int) $dateField->toDate();
  }

  return (int) $page->modified();
}

function twSearchPageDocument(Page $page): array
{
  $entity = twSearchPageEntity($page);
  $title = $page->title()->value();
  $subtitle = '';

  if ($entity === 'episode') {
    $subtitle = (string) $page->podcastersubtitle()->value();
  }

  if ($entity === 'participant') {
    $profession = trim((string) $page->profession()->value());
    $subtitle = $profession;
  }

  $text = twSearchPageText($page);

  return [
    'id' => twSearchPageDocumentId($page->uuid()->toString()),
    'pageUuid' => $page->uuid()->toString(),
    'entity' => $entity,
    'title' => $title,
    'title_boost' => trim($title . ' ' . $title),
    'subtitle' => $subtitle,
    'text' => $text,
    'author' => '',
    'url' => $page->url(),
    'pageTitle' => $title,
    'updatedTs' => twSearchTimestampFromPage($page),
  ];
}

function twSearchCommentDocuments(Page $page): array
{
  if (twSearchSettings()['comments_enabled'] !== true) {
    return [];
  }

  try {
    $comments = $page->comments();
  } catch (Throwable) {
    return [];
  }

  $documents = [];
  $comments = $comments->filterBy('type', 'comment')->filter(static function ($comment): bool {
    $status = strtoupper(trim((string) $comment->verification_status()->value()));

    return $status === 'PUBLISHED' || $comment->published()->isTrue();
  });

  foreach ($comments as $comment) {
    $content = twSearchPlainText((string) $comment->content()->value());

    if ($content === '') {
      continue;
    }

    $author = trim((string) $comment->authorName()->value());

    $timestamp = strtotime((string) $comment->createdAt()->value());
    if ($timestamp === false) {
      $timestamp = twSearchTimestampFromPage($page);
    }

    $documents[] = [
      'id' => twSearchCommentDocumentId($page->uuid()->toString(), (string) $comment->id()),
      'pageUuid' => $page->uuid()->toString(),
      'entity' => 'comment',
      'title' => $author !== '' ? 'Kommentar von ' . $author : 'Kommentar',
      'title_boost' => '',
      'subtitle' => 'Zu: ' . $page->title()->value(),
      'text' => $content,
      'author' => $author,
      'url' => $page->url() . '#c' . $comment->id(),
      'pageTitle' => $page->title()->value(),
      'updatedTs' => (int) $timestamp,
    ];
  }

  return $documents;
}

function twSearchPageDocumentId(string $pageUuid): string
{
  return 'page:' . $pageUuid;
}

function twSearchCommentDocumentId(string $pageUuid, string $commentId): string
{
  return 'comment:' . $pageUuid . ':' . $commentId;
}

function twSearchDeleteDocumentsByPageUuid(string $pageUuid): void
{
  $loupe = twSearchLoupe();

  $searchParams = SearchParameters::create()
    ->withFilter('pageUuid = ' . SearchParameters::escapeFilterValue($pageUuid))
    ->withHitsPerPage(1000)
    ->withPage(1)
    ->withAttributesToRetrieve(['id']);

  $searchResult = $loupe->search($searchParams);
  $ids = [];

  foreach ($searchResult->getHits() as $hit) {
    $id = (string) ($hit['id'] ?? '');
    if ($id !== '') {
      $ids[] = $id;
    }
  }

  if ($ids !== []) {
    $loupe->deleteDocuments($ids);
  }
}

function twSearchIndexPageAndComments(Page $page): void
{
  $pageUuid = $page->uuid()->toString();
  twSearchDeleteDocumentsByPageUuid($pageUuid);

  if (!twSearchIsIndexablePage($page)) {
    return;
  }

  $documents = [twSearchPageDocument($page), ...twSearchCommentDocuments($page)];
  twSearchLoupe()->addDocuments($documents);
}

function twSearchHandleCommentChange(mixed $comment): void
{
  $pageUuid = '';

  if (is_object($comment) && method_exists($comment, 'pageuuid')) {
    $pageUuid = trim((string) $comment->pageuuid()->value());
  } elseif (is_array($comment) && isset($comment['pageUuid'])) {
    $pageUuid = trim((string) $comment['pageUuid']);
  }

  if ($pageUuid === '') {
    twSearchReindexAll();
    return;
  }

  $page = page($pageUuid);
  if (!$page) {
    twSearchDeleteDocumentsByPageUuid($pageUuid);
    return;
  }

  twSearchIndexPageAndComments($page);
}

function twSearchWriteMeta(int $documents): void
{
  $meta = [
    'version' => TW_SEARCH_META_VERSION,
    'documents' => $documents,
    'indexedAt' => date('c'),
  ];

  file_put_contents(
    twSearchMetaPath(),
    json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
  );
}

function twSearchRefreshMetaFromIndex(): void
{
  twSearchWriteMeta(twSearchLoupe()->countDocuments());
}

function twSearchNeedsReindex(): bool
{
  $metaPath = twSearchMetaPath();

  if (!is_file($metaPath)) {
    return true;
  }

  $meta = json_decode((string) file_get_contents($metaPath), true);

  if (!is_array($meta)) {
    return true;
  }

  if ((int) ($meta['version'] ?? 0) !== TW_SEARCH_META_VERSION) {
    return true;
  }

  return twSearchLoupe()->needsReindex();
}

function twSearchReindexAll(): int
{
  $loupe = twSearchLoupe();
  $loupe->deleteAllDocuments();

  $documents = [];

  foreach (site()->index() as $page) {
    if (!twSearchIsIndexablePage($page)) {
      continue;
    }

    $documents[] = twSearchPageDocument($page);
    $documents = [...$documents, ...twSearchCommentDocuments($page)];
  }

  if ($documents !== []) {
    $loupe->addDocuments($documents);
  }

  twSearchWriteMeta(count($documents));

  return count($documents);
}

function twSearchDeleteDocumentsByEntity(array $entities): void
{
  $expressions = [];
  foreach ($entities as $entity) {
    $expressions[] = 'entity = ' . SearchParameters::escapeFilterValue($entity);
  }

  if ($expressions === []) {
    return;
  }

  $searchParams = SearchParameters::create()
    ->withFilter('(' . implode(' OR ', $expressions) . ')')
    ->withHitsPerPage(2000)
    ->withPage(1)
    ->withAttributesToRetrieve(['id']);

  $hits = twSearchLoupe()->search($searchParams)->getHits();
  $ids = [];
  foreach ($hits as $hit) {
    $id = (string) ($hit['id'] ?? '');
    if ($id !== '') {
      $ids[] = $id;
    }
  }

  if ($ids !== []) {
    twSearchLoupe()->deleteDocuments($ids);
  }
}

function twSearchReindexScope(string $scope): int
{
  $scope = strtolower(trim($scope));

  if ($scope === '' || $scope === 'all') {
    return twSearchReindexAll();
  }

  if (twSearchNeedsReindex()) {
    twSearchReindexAll();
  }

  $entityMap = [
    'content' => ['content'],
    'episode' => ['episode'],
    'participant' => ['participant'],
    'comment' => ['comment'],
  ];

  if (!isset($entityMap[$scope])) {
    throw new InvalidArgumentException('Unknown reindex scope: ' . $scope);
  }

  $entities = $entityMap[$scope];
  twSearchDeleteDocumentsByEntity($entities);

  $documents = [];

  foreach (site()->index() as $page) {
    if (!twSearchIsIndexablePage($page)) {
      continue;
    }

    if (in_array('comment', $entities, true)) {
      $documents = [...$documents, ...twSearchCommentDocuments($page)];
      continue;
    }

    $pageEntity = twSearchPageEntity($page);
    if (in_array($pageEntity, $entities, true)) {
      $documents[] = twSearchPageDocument($page);
    }
  }

  if ($documents !== []) {
    twSearchLoupe()->addDocuments($documents);
  }

  twSearchRefreshMetaFromIndex();

  return count($documents);
}

function twSearchEnsureIndex(): void
{
  if (twSearchNeedsReindex()) {
    twSearchReindexAll();
  }
}

function twSearchFilterExpression(string $category): ?string
{
  $commentsEnabled = twSearchSettings()['comments_enabled'] === true;

  return match ($category) {
    'all' => $commentsEnabled
      ? "(entity = 'content' OR entity = 'episode' OR entity = 'participant' OR entity = 'comment')"
      : "(entity = 'content' OR entity = 'episode' OR entity = 'participant')",
    'episode' => "entity = 'episode'",
    'participant' => "entity = 'participant'",
    'comment' => "entity = 'comment'",
    'content' => "(entity = 'content' OR entity = 'episode' OR entity = 'participant')",
    default => null,
  };
}

function twSearchHitWeight(string $entity): float
{
  return match ($entity) {
    'episode' => 1.05,
    'participant' => 1.0,
    'content' => 1.0,
    'comment' => 0.62,
    default => 1.0,
  };
}

function twSearchSearch(
  string $query,
  string $category = 'content',
  int $limit = 30,
  int $page = 1,
): array {
  $query = trim($query);
  $category = twSearchNormalizeCategory($category);
  $page = max(1, $page);

  if ($query === '') {
    return [
      'total' => 0,
      'hits' => [],
      'query' => $query,
      'category' => $category,
      'page' => 1,
      'pages' => 0,
      'limit' => $limit,
    ];
  }

  twSearchEnsureIndex();

  $limit = $limit > 0 ? $limit : (int) twSearchSettings()['results_limit'];

  $params = SearchParameters::create()
    ->withQuery($query)
    ->withHitsPerPage($limit)
    ->withPage($page)
    ->withShowRankingScore(true)
    ->withAttributesToRetrieve([
      'id',
      'entity',
      'title',
      'subtitle',
      'text',
      'author',
      'url',
      'pageTitle',
      'updatedTs',
    ]);

  $filter = twSearchFilterExpression($category);
  if ($filter !== null) {
    $params = $params->withFilter($filter);
  }

  try {
    $searchResult = twSearchLoupe()->search($params);
  } catch (Throwable) {
    twSearchReindexAll();
    $searchResult = twSearchLoupe()->search($params);
  }

  $hits = [];

  foreach ($searchResult->getHits() as $hit) {
    $entity = (string) ($hit['entity'] ?? 'content');
    $score = (float) ($hit['_rankingScore'] ?? 0.0);

    $hits[] = [
      'id' => (string) ($hit['id'] ?? ''),
      'entity' => $entity,
      'entityLabel' => twSearchCategoryLabel($entity),
      'title' => (string) ($hit['title'] ?? ''),
      'subtitle' => (string) ($hit['subtitle'] ?? ''),
      'text' => twSearchExcerpt((string) ($hit['text'] ?? '')),
      'author' => (string) ($hit['author'] ?? ''),
      'url' => (string) ($hit['url'] ?? ''),
      'pageTitle' => (string) ($hit['pageTitle'] ?? ''),
      'updatedTs' => (int) ($hit['updatedTs'] ?? 0),
      'score' => $score * twSearchHitWeight($entity),
    ];
  }

  usort($hits, static fn(array $left, array $right): int => $right['score'] <=> $left['score']);

  return [
    'total' => (int) $searchResult->getTotalHits(),
    'hits' => $hits,
    'query' => $query,
    'category' => $category,
    'page' => max(1, (int) $searchResult->getPage()),
    'pages' => max(0, (int) $searchResult->getTotalPages()),
    'limit' => max(1, (int) $searchResult->getHitsPerPage()),
  ];
}
