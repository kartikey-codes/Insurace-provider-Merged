<?php

declare(strict_types=1);

namespace App\Model\Filter;

use App\Model\Filter\Rule\AliasedFilters;
use Search\Model\Filter\FilterCollection;

/**
 * Search Filters
 */
class PermissionsCollection extends FilterCollection
{
	public function initialize(): void
	{
		// Map 'search' to multiple potential filters
		$this->add('search', AliasedFilters::class, [
			'filters' => [
				'name',
			],
		]);

		$this->add('controller', 'Search.Value', [
			'fields' => 'controller',
			'filterEmpty' => true,
		]);

		$this->add('action', 'Search.Value', [
			'fields' => 'action',
			'filterEmpty' => true,
		]);

		$this->add('name', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'name',
			'filterEmpty' => true,
		]);
	}
}
