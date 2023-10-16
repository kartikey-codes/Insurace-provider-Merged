<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Lib\TenantUtility\TenantUtility;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;

/**
 * Cases Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\CasesTable $Cases
 */
class CasesController extends ApiController
{
	use LocatorAwareTrait;

	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'created' => 'asc',
		],
		'sortableFields' => [
			'id',
			'insurance_plan',
			'total_claim_amount',
			'disputed_amount',
			'settled_amount',
			'reimbursement_amount',
			'insurance_number',
			'visit_number',
			'admit_date',
			'discharge_date',
			'closed',
			'assigned',
			'created',
			'modified',
			'unable_to_complete',
			'assigned',
			'assigned_to',
			// Associations
			'CaseTypes.name',
			'Clients.name',
			'ClientEmployees.last_name',
			'ClientEmployees.first_name',
			'Patients.first_name',
			'Patients.middle_name',
			'Patients.last_name',
			'Patients.date_of_birth',
			'Patients.phone',
			'Patients.fax',
			'Patients.sex',
			'Patients.city',
			'Patients.state',
			'Patients.zip',
			'Patients.marital_status',
			'Facilities.name',
			'DenialTypes.name',
			'CaseOutcomes.name',
			'Disciplines.name',
			'InsuranceProviders.name',
			'InsuranceTypes.name',
			'ClosedByUser.first_name',
			'ClosedByUser.last_name',
			'AssignedToUser.first_name',
			'AssignedToUser.last_name',
			'CreatedByUser.first_name',
			'CreatedByUser.last_name',
			'ModifiedByUser.first_name',
			'ModifiedByUser.last_name',
		],
		'contain' => [
			'Appeals' => [
				'finder' => 'ordered',
				'AppealLevels',
				'AppealTypes',
				'DaysToRespondFroms',
			],
			'CaseRequests' => [
				'finder' => 'ordered'
			],
			'CaseTypes',
			'ClientEmployees',
			'Patients',
			'Facilities',
			'DenialTypes',
			'DenialReasons' => [
				'finder' => 'ordered',
			],
			'Disciplines',
			'CaseOutcomes',
			'CaseReadmissions',
			'InsuranceProviders',
			'InsuranceTypes',
			'ClosedByUser',
			'AssignedToUser',
			'CreatedByUser',
			'ModifiedByUser',
		],
	];

	/**
	 * Add method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function add(): void
	{
		$entity = $this->Cases->newEntity($this->getRequest()->getData());

		// Always ensure client ID is set
		$entity->client_id = TenantUtility::getTenantIdFromRequest();

		// Assign default case type
		if (empty($entity->case_type_id)) {
			$caseType = $this->Cases->CaseTypes->find('active')->first();
			$entity->case_type_id = $caseType->id;
		}

		try {
			$this->Cases->saveOrFail($entity);

			if (!empty($this->getRequest()->getData('attach_document_id'))) {
				// $this->Cases->IncomingDocuments->attachToCase(
				// 	$this->getRequest()->getData('attach_document_id'),
				// 	$entity->id
				// );
			}

			$entity = $this->Cases->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * View method
	 *
	 * @param int $id Primary Key.
	 * @return void
	 * @throws \Cake\Core\Exception\CakeException When record not found.
	 */
	public function view($id): void
	{
		$this->set('data', $this->Cases->getFull($id));
	}

	/**
	 * Index method
	 *
	 * @return void
	 */
	public function index(): void
	{
		$this->crudIndex();
	}

	/**
	 * Empty Cases
	 *
	 * @return void
	 */
	public function empty(): void
	{
		// Search Plugin
		$entities = $this->Cases
			->find('withoutAppeals')
			->find(
				'search',
				['search' => $this->getRequest()->getQuery()]
			);

		$this->set('data', $this->paginate($entities));
		$this->set('pagination', $this->getDefaultTablePagination());
	}

	/**
	 * Edit method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function edit($id): void
	{
		$entity = $this->Cases->get($id);
		$entity = $this->Cases->patchEntity($entity, $this->getRequest()->getData());

		try {
			$this->Cases->saveOrFail($entity);

			if (!empty($this->getRequest()->getData('attach_fax_id'))) {
				$this->Cases->IncomingDocuments->update([
					'case_id' => $entity->id,
				], [
					'id' => $this->getRequest()->getData('attach_fax_id'),
				]);
			}

			$entity = $this->Cases->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Assign method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When rules check fails.
	 */
	public function assign($id): void
	{
		/** @var \App\Model\Entity\CaseEntity */
		$entity = $this->Cases->get($id);

		$userId = $this->getRequest()->getData('user_id');

		if (!empty($userId)) {
			$users = $this->fetchTable('Users');
			$user = $users->get($userId);
			$entity->setAssignedTo($user);
		} else {
			$entity->clearAssignedTo();
		}

		try {
			$this->Cases->saveOrFail($entity);
			$entity = $this->Cases->getFull($id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Close method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function close($id): void
	{
		/** @var \App\Model\Entity\CaseEntity */
		$entity = $this->Cases->get($id);
		$entity->setClosedBy($this->currentUser);
		$entity = $this->Cases->patchEntity($entity, $this->getRequest()->getData());

		//$this->Cases->closeAllAppeals($id, $this->currentUser);

		try {
			$this->Cases->saveOrFail($entity);
			$entity = $this->Cases->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Open method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function open($id): void
	{
		/** @var \App\Model\Entity\CaseEntity */
		$entity = $this->Cases->get($id);
		$entity->setOpenedBy($this->currentUser);
		$entity->set('case_outcome_id', null);

		try {
			$this->Cases->saveOrFail($entity);
			$entity = $this->Cases->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Activity method
	 *
	 * Returns users currently viewing a case
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function activity($id): void
	{
		/**
		 * Update current users activity
		 *
		 * @var \App\Model\Table\CaseActivityTable
		 */
		$activityTable = $this->fetchTable('CaseActivity');

		$activityTable->touch(
			intval($id),
			intval($this->currentUser->id)
		);

		// Return currently active users
		$entities = $this->Cases
			->ActiveUsers
			->find('all')
			->select([
				'ActiveUsers.id',
				'ActiveUsers.first_name',
				'ActiveUsers.last_name',
			])
			->select($this->Cases->CaseActivity)
			->matching('CaseActivity', function (Query $q) use ($id) {
				return $q->find('current')->where([
					'CaseActivity.case_id' => $id,
				]);
			});

		$this->set('data', $entities->all());
	}

	/**
	 * Delete method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When rules check fails.
	 */
	public function delete($id): void
	{
		$entity = $this->Cases->getFull($id);

		try {
			$this->Cases->deleteOrFail($entity);
			$this->Log->deleteSuccess($entity);
			$this->set('data', $entity);
			$this->set('success', true);
		} catch (PersistenceFailedException $e) {
			$this->Log->deleteFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}
}
