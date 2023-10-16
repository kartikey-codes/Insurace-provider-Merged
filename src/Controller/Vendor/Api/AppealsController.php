<?php

declare(strict_types=1);

namespace App\Controller\Vendor\Api;

use Cake\Event\Event;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Appeals Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\AppealsTable $Appeals
 */
class AppealsController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'id' => 'desc',
		],
		'sortableFields' => [
			'id',
			'case_id',
			'defendable',
			'letter_date',
			'received_date',
			'due_date',
			'hearing_date',
			'priority',
			'assigned',
			'completed',
			'created',
			'modified',
			// Associations
			'AppealTypes.name',
			'Cases.id',
			'Cases.visit_number',
			'Cases.total_claim_amount',
			'Cases.disputed_amount',
			'Cases.settled_amount',
			'Cases.reimbursement_amount',
			'Cases.insurance_number',
			'Cases.admit_date',
			'Cases.discharge_date',
			'Cases.closed',
			'CaseTypes.name',
			'Clients.name',
			'DenialTypes.name',
			'Facilities.name',
			'Patients.first_name',
			'Patients.last_name',
			'CaseOutcomes.name',
			'AppealLevels.name',
			'InsuranceProviders.name',
			'InsuranceTypes.name',
			'AssignedToUser.first_name',
			'AssignedToUser.last_name',
			'CompletedByUser.first_name',
			'CompletedByUser.last_name',
			'CreatedByUser.first_name',
			'CreatedByUser.last_name',
			'ModifiedByUser.first_name',
			'ModifiedByUser.last_name',
		],
		'contain' => [
			'AppealTypes',
			'AppealLevels',
			'Cases' => [
				'CaseTypes',
				'Clients',
				'Patients',
				'Facilities',
				'DenialTypes',
				'CaseOutcomes',
				'InsuranceProviders',
				'InsuranceTypes',
			],
			'CompletedByUser',
			'AssignedToUser',
			'CreatedByUser',
			'ModifiedByUser',
		],
	];

	/**
	 * View method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Core\Exception\CakeException When record not found.
	 */
	public function view($id): void
	{
		$this->set('data', $this->Appeals->getFull($id));
	}

	/**
	 * Index method
	 *
	 * @return void
	 */
	public function index(): void
	{
		// Search Plugin
		$query = $this->Appeals
			->find('assignedToVendorById', [
				'id' => $this->vendorId,
			])
			->find('assigned');

		$this->crudIndex($query);
	}

	/**
	 * Edit method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function edit($id): void
	{
		/** @var \App\Model\Entity\Appeal $entity */
		$entity = $this->Appeals->getFull($id);
		$entity = $this->Appeals->patchEntity($entity, $this->getRequest()->getData());

		try {
			$this->Appeals->saveOrFail($entity, [
				'associated' => [
					'AppealReferenceNumbers',
				],
			]);
			$entity = $this->Appeals->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Defendable method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function defendable($id): void
	{
		/** @var \App\Model\Entity\Appeal $entity */
		$entity = $this->Appeals->getFull($id);
		$entity = $this->Appeals->patchEntity($entity, $this->getRequest()->getData());

		try {
			$this->Appeals->saveOrFail($entity, [
				'associated' => [
					'NotDefendableReasons',
				],
			]);
			$entity = $this->Appeals->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Complete method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function complete($id): void
	{
		/** @var \App\Model\Entity\Appeal $entity */
		$entity = $this->Appeals->getFull($id);
		/** @var \App\Model\Entity\Appeal $entity */
		$entity = $this->Appeals->patchEntity($entity, $this->getRequest()->getData());

		try {
			$entity->setCompletedBy($this->currentUser);
			$this->Appeals->saveOrFail($entity);

			$entity = $this->Appeals->getFull($entity->id);

			$event = new Event('Model.Appeal.completedByVendor', $this, [$entity]);
			$this->Appeals->getEventManager()->dispatch($event);

			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Return method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function return($id): void
	{
		/** @var \App\Model\Entity\Appeal $entity */
		$entity = $this->Appeals->getFull($id);
		/** @var \App\Model\Entity\Appeal $entity */
		$entity = $this->Appeals->patchEntity($entity, $this->getRequest()->getData());

		try {
			$entity->setReturnedBy($this->currentUser);

			$this->Appeals->saveOrFail($entity);
			$entity = $this->Appeals->getFull($entity->id);

			$event = new Event('Model.Appeal.returnedByVendor', $this, [$entity]);
			$this->Appeals->getEventManager()->dispatch($event);

			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}
}
