<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Lib\TenantUtility\TenantUtility;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Queue Controller
 *
 * Used for returning the current user's assigned work items
 * ordered by priority and any other business logic.
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\DownloadComponent $Download
 * @property \App\Controller\Component\LogComponent $Log
 */
class QueueController extends ApiController
{
	use LocatorAwareTrait;

	/**
	 * @var int
	 */
	public int $clientId;

	/**
	 * Initialization hook method.
	 *
	 * Use this method to add common initialization code like loading components.
	 *
	 * @return void
	 */
	public function initialize(): void
	{
		parent::initialize();

		$this->clientId = TenantUtility::getTenantIdFromRequest();
	}

	/**
	 * Appeal queue method
	 *
	 * @return void
	 */
	public function appeals(): void
	{
		// Search Plugin
		$entities = $this->fetchTable('Appeals')
			->find('queue')
			->find('assignedTo', [
				'UserId' => $this->currentUser->id,
			])
			->where([
				'Appeals.client_id' => $this->clientId
			]);

		$data = $this->paginate($entities, [
			'limit' => PAGINATION_DEFAULT_LIMIT,
			'contain' => [
				'AppealTypes' => [
					'finder' => 'limited'
				],
				'AppealLevels' => [
					'finder' => 'limited'
				],
				'Cases' => [
					'CaseTypes' => [
						'finder' => 'limited'
					],
					'Patients' => [
						'finder' => 'limited'
					],
					'Facilities' => [
						'finder' => 'limited'
					],
					'DenialTypes' => [
						'finder' => 'limited'
					],
					'CaseOutcomes' => [
						'finder' => 'limited'
					],
					'InsuranceProviders' => [
						'finder' => 'limited'
					],
					'InsuranceTypes' => [
						'finder' => 'limited'
					],
				],
				'CreatedByUser' => [
					'finder' => 'limited'
				],
				'ModifiedByUser' => [
					'finder' => 'limited'
				],
			]
		]);

		$this->set('data', $data);
		$this->set('pagination', $this->getRequest()->getAttribute('paging')['Appeals']);
	}

	/**
	 * Case request queue method
	 *
	 * @return void
	 */
	public function caseRequests(): void
	{
		// Search Plugin
		$entities = $this->fetchTable('CaseRequests')
			->find('queue')
			->find('assignedTo', [
				'UserId' => $this->currentUser->id,
			])
			->where([
				'CaseRequests.client_id' => $this->clientId
			]);

		$data = $this->paginate($entities, [
			'limit' => PAGINATION_DEFAULT_LIMIT,
			'contain' => [
				'Cases' => [
					'Patients' => [
						'finder' => 'limited'
					]
				],
				'InsuranceProviders' => [
					'finder' => 'limited'
				],
				'Agencies' => [
					'finder' => 'limited'
				]
			]
		]);

		$this->set('data', $data);
		$this->set('pagination', $this->getRequest()->getAttribute('paging')['CaseRequests']);
	}
}
