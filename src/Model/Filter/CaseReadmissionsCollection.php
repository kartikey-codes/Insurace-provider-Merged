<?php

declare(strict_types=1);

namespace App\Model\Filter;

use App\Model\Filter\Rule\AliasedFilters;
use Search\Model\Filter\FilterCollection;

class CaseReadmissionsCollection extends FilterCollection
{
	/**
	 * Initialize search filters
	 *
	 * @return void
	 */
	public function initialize(): void
	{
		// Map 'search' to multiple potential filters
		$this->add('search', AliasedFilters::class, [
			'filters' => [
				'name',
			],
		]);

		$this->add('visit_number', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'visit_number',
			'filterEmpty' => true,
		]);
	}
}
