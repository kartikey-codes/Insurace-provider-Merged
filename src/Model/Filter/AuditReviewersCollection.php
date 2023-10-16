<?php

declare(strict_types=1);

namespace App\Model\Filter;

use App\Model\Filter\Rule\AliasedFilters;
use Search\Model\Filter\FilterCollection;

class AuditReviewersCollection extends FilterCollection
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

		$this->add('first_name', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'first_name',
			'filterEmpty' => true,
		]);

		$this->add('middle_name', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'middle_name',
			'filterEmpty' => true,
		]);

		$this->add('last_name', 'Search.Like', [
			'before' => true,
			'after' => true,
			'fields' => 'last_name',
			'filterEmpty' => true,
		]);

		$this->add('name', 'App.PersonFullNameFilter', [
			'filterEmpty' => true,
			'before' => true,
			'after' => true,
			'firstName' => 'AuditReviewers.first_name',
			'middleName' => 'AuditReviewers.middle_name',
			'lastName' => 'AuditReviewers.last_name',
			'searchFirstLast' => true,
			'searchLastFirst' => true,
			'searchFirstMiddleLast' => true,
			'searchLastFirstMiddle' => true,
		]);

		$this->add('full_name', 'App.PersonFullNameFilter', [
			'filterEmpty' => true,
			'before' => true,
			'after' => true,
			'firstName' => 'first_name',
			'middleName' => 'middle_name',
			'lastName' => 'last_name',
			'searchFirstLast' => true,
			'searchLastFirst' => true,
			'searchFirstMiddleLast' => true,
			'searchLastFirstMiddle' => true,
		]);

		$this->add('list_name', 'App.PersonFullNameFilter', [
			'filterEmpty' => true,
			'before' => true,
			'after' => true,
			'firstName' => 'first_name',
			'middleName' => 'middle_name',
			'lastName' => 'last_name',
			'searchFirstLast' => false,
			'searchLastFirst' => true,
			'searchFirstMiddleLast' => false,
			'searchLastFirstMiddle' => true,
		]);

		$this->add('agency_id', 'Search.Value', [
			'fields' => 'agency_id',
			'filterEmpty' => true,
		]);

		$this->add('active', 'App.Boolean', [
			'fields' => 'active',
			'filterEmpty' => true,
		]);
	}
}
