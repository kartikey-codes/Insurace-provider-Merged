<?php

declare(strict_types=1);

namespace App\Model\Filter;

use App\Model\Filter\Rule\AliasedFilters;
use Search\Model\Filter\FilterCollection;

class InsuranceProvidersCollection extends FilterCollection
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

		$this->add('default_insurance_type_id', 'App.NullableId', [
			'fields' => 'default_insurance_type_id',
			'filterEmpty' => true,
		]);

		$this->add('appeal_level_id', 'Search.Finder', [
			'finder' => 'appealLevel',
			'filterEmpty' => true,
		]);

		$this->add('insurance_type_id', 'Search.Finder', [
			'finder' => 'insuranceType',
			'filterEmpty' => true,
		]);

		$this->add('active', 'App.Boolean', [
			'fields' => 'active',
			'filterEmpty' => true,
		]);
	}
}
