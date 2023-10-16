<?php

declare(strict_types=1);

namespace App\Model\Filter;

use App\Model\Entity\CaseRequest;
use App\Model\Filter\Rule\AliasedFilters;
use Search\Model\Filter\FilterCollection;

class CaseRequestsCollection extends FilterCollection
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

		$this->add('status', 'Search.Callback', [
			'callback' => function ($query, $args, $manager) {
				if (isset($args['status'])) {
					switch ($args['status']) {
						case CaseRequest::STATUS_OPEN:
							$query->find('open', $args);
							break;
						case CaseRequest::STATUS_UTC:
							$query->find('utc', $args);
							break;
						case CaseRequest::STATUS_COMPLETED:
							$query->find('completed', $args);
							break;
					}
				}

				return true;
			},
			'filterEmpty' => true,
		]);

		$this->add('request_type', 'Search.Value', [
			'fields' => 'request_type',
			'filterEmpty' => true,
		]);

		$this->add('agency_id', 'App.NullableId', [
			'fields' => 'agency_id',
			'filterEmpty' => true,
		]);

		$this->add('insurance_provider_id', 'App.NullableId', [
			'fields' => 'insurance_provider_id',
			'filterEmpty' => true,
		]);

		$this->add('assigned_to', 'App.NullableId', [
			'fields' => 'assigned_to',
			'filterEmpty' => true,
		]);

		$this->add('priority', 'App.Boolean', [
			'fields' => 'priority',
			'filterEmpty' => true,
		]);
	}
}
