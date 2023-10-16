<?php

declare(strict_types=1);

namespace App\Model\Filter;

use App\Model\Filter\Rule\AliasedFilters;
use Search\Model\Filter\FilterCollection;

class AgenciesCollection extends FilterCollection
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

		$this->add('name', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'name',
			'filterEmpty' => true,
		]);

		$this->add('active', 'App.Boolean', [
			'fields' => 'active',
			'filterEmpty' => true,
		]);

		$this->add('third_party_contractor', 'App.Boolean', [
			'fields' => 'third_party_contractor',
			'filterEmpty' => true,
		]);

		$this->add('outgoing_primary_method', 'Search.Value', [
			'fields' => 'outgoing_primary_method',
			'filterEmpty' => true,
		]);
	}
}
