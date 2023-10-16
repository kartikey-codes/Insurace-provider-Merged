<?php

declare(strict_types=1);

namespace App\Model\Filter;

use App\Model\Filter\Rule\AliasedFilters;
use App\Model\Filter\Rule\NotMatchingAssociation;
use Search\Model\Filter\FilterCollection;

class UsersCollection extends FilterCollection
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
				'full_name',
			],
		]);

		$this->add('active', 'App.Boolean', [
			'fields' => 'active',
			'filterEmpty' => true,
			'aliasField' => true,
		]);

		$this->add('admin', 'App.Boolean', [
			'fields' => 'admin',
			'filterEmpty' => true,
			'aliasField' => true,
		]);

		$this->add('first_name', 'Search.Like', [
			'before' => false,
			'after' => true,
			'fields' => 'first_name',
			'filterEmpty' => true,
			'aliasField' => true,
		]);

		$this->add('last_name', 'Search.Like', [
			'before' => false,
			'after' => true,
			'fields' => 'last_name',
			'filterEmpty' => true,
			'aliasField' => true,
		]);

		$this->add('email', 'Search.Like', [
			'before' => false,
			'after' => true,
			'fields' => 'email',
			'filterEmpty' => true,
			'aliasField' => true,
		]);

		$this->add('date_of_birth', 'Search.Value', [
			'fields' => 'date_of_birth',
			'filterEmpty' => true,
			'aliasField' => true,
		]);

		$this->add('last_login_ip', 'Search.Value', [
			'fields' => 'last_login_ip',
			'filterEmpty' => true,
			'aliasField' => true,
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

		$this->add('not_in_role', NotMatchingAssociation::class, [
			'association' => 'Roles',
		]);
	}
}
