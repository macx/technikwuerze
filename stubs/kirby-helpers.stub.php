<?php

declare(strict_types=1);

use Kirby\Cms\Page;

// IDE stubs for Kirby global helper functions used in templates.
// These are not loaded at runtime by Kirby; they only help static analysis.
if (function_exists('page') === false) {
	function page(string|null $id = null): Page|null
	{
		return null;
	}
}

if (function_exists('snippet') === false) {
	function snippet(
		string $name,
		array $data = [],
		bool $slots = false
	): string|null {
		return null;
	}
}
