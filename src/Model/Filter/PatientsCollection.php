<?php

declare(strict_types=1);

namespace App\Model\Filter;

use App\Model\Filter\Rule\AliasedFilters;
use Search\Model\Filter\FilterCollection;

class PatientsCollection extends FilterCollection
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
			'firstName' => 'Patients.first_name',
			'middleName' => 'Patients.middle_name',
			'lastName' => 'Patients.last_name',
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

		$this->add('date_of_birth', 'Search.Value', [
			'fields' => 'date_of_birth',
			'filterEmpty' => true,
		]);

		$this->add('date_of_birth_start', 'Search.Compare', [
			'operator' => '>=',
			'fields' => 'date_of_birth',
			'filterEmpty' => true,
		]);

		$this->add('date_of_birth_end', 'App.EndDate', [
			'fields' => 'date_of_birth',
			'filterEmpty' => true,
		]);

		$this->add('sex', 'Search.Value', [
			'fields' => 'sex',
			'filterEmpty' => false,
		]);

		$this->add('marital_status', 'Search.Value', [
			'fields' => 'marital_status',
			'filterEmpty' => true,
		]);

		$this->add('phone', 'Search.Like', [
			'before' => false,
			'after' => true,
			'fields' => 'phone',
			'filterEmpty' => true,
		]);

		$this->add('fax', 'Search.Like', [
			'before' => false,
			'after' => true,
			'fields' => 'fax',
			'filterEmpty' => true,
		]);

		$this->add('city', 'Search.Like', [
			'before' => false,
			'after' => true,
			'fields' => 'city',
			'filterEmpty' => true,
		]);

		$this->add('state', 'Search.Like', [
			'before' => false,
			'after' => true,
			'fields' => 'state',
			'filterEmpty' => true,
		]);

		$this->add('zip', 'Search.Value', [
			'fields' => 'zip',
			'filterEmpty' => true,
		]);

		$this->add('secured', 'App.Boolean', [
			'fields' => 'secured',
			'filterEmpty' => true,
		]);

		$this->add('medical_record_number', 'Search.Like', [
			'before' => false,
			'after' => true,
			'fields' => 'medical_record_number',
			'filterEmpty' => true,
		]);

		$this->add('ssn_last_four', 'Search.Value', [
			'fields' => 'ssn_last_four',
			'filterEmpty' => true,
		]);
	}
}
