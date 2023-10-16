<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Model\Table\CasesTable;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Patients Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\CasesTable $Cases
 * @property \App\Model\Table\PatientsTable $Patients
 */
class PatientsController extends ApiController
{
	use LocatorAwareTrait;

	/**
	 * @var \App\Model\Table\CasesTable
	 */
	public CasesTable $Cases;

	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'id' => 'desc',
		],
		'fields' => [
			'id',
			'created',
			'modified',
			'first_name',
			'middle_name',
			'last_name',
			'date_of_birth',
			'sex',
			'marital_status',
			'secured',
			'medical_record_number',
		],
		'sortableFields' => [
			'id',
			'created',
			'modified',
			'deleted',
			'first_name',
			'middle_name',
			'last_name',
			'date_of_birth',
			'sex',
			'marital_status',
			'phone',
			'fax',
			'street_address_1',
			'street_address_2',
			'city',
			'state',
			'zip',
			'email',
			'secured',
			'medical_record_number',
			'ssn_last_four',
			// Virtual
			'full_name',
			'list_name',
			// Associations
		],
	];

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

		$this->Cases = $this->fetchTable('Cases');
	}

	/**
	 * Add method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function add(): void
	{
		$entity = $this->Patients->newEntity($this->getRequest()->getData(), [
			'associated' => [],
		]);

		try {
			$this->Patients->saveOrFail($entity);
			$entity = $this->Patients->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * View method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function view($id): void
	{
		$this->set('data', $this->Patients->getFull($id));
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
	 * Similar method
	 *
	 * @param string $id
	 * @return void
	 */
	public function similar($id)
	{
		// Build Query
		$entities = $this->Patients->find(
			'search',
			['search' => $this->getRequest()->getQuery()]
		)
			->find('similar', [
				'PatientId' => $id,
			]);

		$this->set('data', $this->paginate($entities));
		$this->set('pagination', $this->getDefaultTablePagination());
	}

	/**
	 * Edit method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function edit($id): void
	{
		$entity = $this->Patients->get($id);
		$entity = $this->Patients->patchEntity($entity, $this->getRequest()->getData(), [
			'associated' => [],
		]);

		try {
			$this->Patients->saveOrFail($entity);
			$entity = $this->Patients->getFull($id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Merge method
	 *
	 * Moves all associations (cases/appeals/etc) from multiple patients into one.
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function merge($id)
	{
		if (empty($this->getRequest()->getData('_ids'))) {
			throw new BadRequestException(__('The IDs of similar patients must be provided in order to merge'));
		}

		if (!$this->Patients->exists(['id' => $id])) {
			throw new NotFoundException(__('The patient with the ID provided could not be located'));
		}

		$patient_ids = $this->getRequest()->getData('_ids');

		$modified_cases = $this->Cases->updateAll([
			'patient_id' => $id,
		], [
			'patient_id IN' => $patient_ids,
		]);

		$deleted_patients = $this->Patients->deleteAll([
			'id IN' => $patient_ids,
		]);

		$this->set(compact(
			'patient_ids',
			'modified_cases',
			'deleted_patients'
		));
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
		$entity = $this->Patients->get($id);

		try {
			$this->Patients->deleteOrFail($entity);
			$this->Log->deleteSuccess($entity);
			$this->set('data', $entity);
			$this->set('success', true);
		} catch (PersistenceFailedException $e) {
			$this->Log->deleteFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}
}
