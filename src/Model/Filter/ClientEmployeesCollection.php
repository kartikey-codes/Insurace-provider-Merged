<?php

declare(strict_types=1);

namespace App\Model\Filter;

use App\Model\Filter\Rule\AliasedFilters;
use Search\Model\Filter\FilterCollection;

class ClientEmployeesCollection extends FilterCollection
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

		$this->add('active', 'App.Boolean', [
			'fields' => 'active',
			'filterEmpty' => true,
		]);

		$this->add('facility_id', 'Search.Value', [
			'fields' => 'facility_id',
			'filterEmpty' => true,
		]);

		$this->add('first_name', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'first_name',
			'filterEmpty' => true,
		]);

		$this->add('last_name', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'last_name',
			'filterEmpty' => true,
		]);

		$this->add('list_name', 'App.PersonFullNameFilter', [
			'filterEmpty' => true,
			'before' => true,
			'after' => true,
			'firstName' => 'first_name',
			'middleName' => null,
			'lastName' => 'last_name',
			'searchFirstLast' => false,
			'searchLastFirst' => true,
			'searchFirstMiddleLast' => false,
			'searchLastFirstMiddle' => false,
		]);

		$this->add('full_name', 'App.PersonFullNameFilter', [
			'filterEmpty' => true,
			'before' => true,
			'after' => true,
			'firstName' => 'first_name',
			'middleName' => null,
			'lastName' => 'last_name',
			'searchFirstLast' => true,
			'searchLastFirst' => true,
			'searchFirstMiddleLast' => false,
			'searchLastFirstMiddle' => false,
		]);

		$this->add('name', 'App.PersonFullNameFilter', [
			'filterEmpty' => true,
			'before' => true,
			'after' => true,
			'firstName' => 'first_name',
			'middleName' => null,
			'lastName' => 'last_name',
			'searchFirstLast' => true,
			'searchLastFirst' => true,
			'searchFirstMiddleLast' => false,
			'searchLastFirstMiddle' => false,
		]);

		$this->add('title', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'title',
			'filterEmpty' => true,
		]);

		$this->add('npi_number', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'npi_number',
			'filterEmpty' => true,
			'colType' => [
				'npi_number' => 'string',
			],
		]);
	}
}
