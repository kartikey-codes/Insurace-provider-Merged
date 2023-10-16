<?php

declare(strict_types=1);

namespace App\Model\Filter;

use App\Model\Filter\Rule\AliasedFilters;
use Search\Model\Filter\FilterCollection;

class FacilitiesCollection extends FilterCollection
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

		$this->add('query', 'Search.Like', [
			'fields' => [
				'name',
				'chain_name',
			],
			'before' => false,
			'after' => true,
			'filterEmpty' => true,
		]);

		$this->add('facility_type_id', 'App.NullableId', [
			'fields' => 'facility_type_id',
			'filterEmpty' => true,
		]);

		$this->add('email', 'Search.Value', [
			'fields' => 'email',
			'filterEmpty' => true,
		]);

		$this->add('state', 'Search.Value', [
			'fields' => 'state',
			'filterEmpty' => true,
		]);

		$this->add('name', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => [
				'name',
				'chain_name',
			],
			'filterEmpty' => true,
		]);

		$this->add('active', 'App.Boolean', [
			'fields' => 'active',
			'filterEmpty' => true,
		]);

		$this->add('address', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => [
				'street_address_1',
				'street_address_2',
				'city',
				'state',
				'zip',
			],
			'filterEmpty' => true,
		]);

		$this->add('client_owned', 'App.Boolean', [
			'fields' => 'client_owned',
			'filterEmpty' => true,
		]);

		$this->add('chain_name', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'chain_name',
			'filterEmpty' => true,
		]);

		$this->add('area_name', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'area_name',
			'filterEmpty' => true,
		]);

		$this->add('ou_number', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'ou_number',
			'filterEmpty' => true,
		]);

		$this->add('territory', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'territory',
			'filterEmpty' => true,
		]);

		$this->add('rvp_name', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'rvp_name',
			'filterEmpty' => true,
		]);

		$this->add('has_contract', 'App.Boolean', [
			'fields' => 'has_contract',
			'filterEmpty' => true,
		]);

		$this->add('contract_start_date', 'Search.Compare', [
			'operator' => '>=',
			'fields' => 'contract_start_date',
			'filterEmpty' => true,
		]);

		$this->add('contract_end_date', 'App.EndDate', [
			'fields' => 'contract_end_date',
			'filterEmpty' => true,
		]);
	}
}
