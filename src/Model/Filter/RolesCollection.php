<?php

declare(strict_types=1);

namespace App\Model\Filter;

use Search\Model\Filter\FilterCollection;

/**
 * Search Filters
 */
class RolesCollection extends FilterCollection
{
	public function initialize(): void
	{
		$this->add('search', 'Search.Callback', [
			'callback' => function ($query, $args, $manager) {
				if (empty($args['search'])) {
					return false;
				}

				// Reuse Search plugin to alias another filter
				$query->find('search', [
					'search' => [
						// Search 'name' filter with the contents of 'search'
						'name' => $args['search'],
					],
				]);

				return true;
			},
		]);

		$this->add('query', 'Search.Like', [
			'fields' => [
				'name',
			],
			'before' => false,
			'after' => true,
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
