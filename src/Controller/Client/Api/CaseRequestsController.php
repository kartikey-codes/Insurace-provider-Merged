<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Controller\Client\Api\ApiController;
use Cake\I18n\FrozenTime;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Case Requests Controller
 *
 * This is for the index/queue type actions that are not
 * specific to a single entity, or for bulk actions.
 *
 * @property \App\Model\Table\CaseRequestsTable $CaseRequests
 */
class CaseRequestsController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'priority' => 'desc',
			'due_date' => 'asc',
			'name' => 'asc',
		],
		'sortableFields' => [
			'request_type',
			'name',
			'unable_to_complete',
			'due_date',
			'completed',
			'created',
			'modified',
			'deleted',
			'assigned_to',
			'assigned',
			'priority',
			'InsuranceProviders.name',
			'Agencies.name'
		],
		'contain' => [
			'Cases' => [
				'Patients'
			],
			'CompletedByUser',
			'InsuranceProviders',
			'Agencies',
			'AssignedToUser'
		]
	];

	/**
	 * Index method
	 *
	 * @return void
	 */
	public function index(): void
	{
		$this->crudIndex();
	}
}
