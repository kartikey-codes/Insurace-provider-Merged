<?php

declare(strict_types=1);

namespace App\Model\Filter;

use App\Model\Filter\Rule\AliasedFilters;
use Search\Model\Filter\FilterCollection;

class OutgoingDocumentsCollection extends FilterCollection
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
			'fields' => 'filename',
			'filterEmpty' => true,
		]);

		$this->add('status', 'Search.Value', [
			'fields' => 'status_message',
			'filterEmpty' => true,
		]);

		$this->add('delivery_method', 'Search.Value', [
			'fields' => 'delivery_method',
			'filterEmpty' => true,
		]);

		$this->add('agency_id', 'App.NullableId', [
			'fields' => 'agency_id',
			'filterEmpty' => true,
		]);
	}
}
