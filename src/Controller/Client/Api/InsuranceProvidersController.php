<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Insurance Providers Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\InsuranceProvidersTable $InsuranceProviders
 */
class InsuranceProvidersController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_HIGH_LIMIT,
		'order' => [
			'name' => 'asc',
		],
		'sortableFields' => [
			'name',
			'active',
			'phone',
			'fax',
			'city',
			'state',
			'zip',
			'created',
			'modified',
			'deleted',
			// Associations
			'AppealLevels.name',
			'InsuranceTypes.name',
			'DefaultInsuranceType.name',
		],
		'contain' => [
			'AppealLevels' => [
				'finder' => 'ordered',
			],
			'InsuranceTypes' => [
				'finder' => 'ordered',
			],
			'DefaultInsuranceType',
		],
	];

	/**
	 * Add method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function add(): void
	{
		$entity = $this->InsuranceProviders->newEntity($this->getRequest()->getData(), [
			'associated' => [
				'AppealLevels._joinData',
				'InsuranceTypes'
			]
		]);

		try {
			$this->InsuranceProviders->saveOrFail($entity);
			$entity = $this->InsuranceProviders->getFull($entity->id);
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
		$this->set('data', $this->InsuranceProviders->getFull($id));
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
	 * Active method
	 *
	 * @return void
	 */
	public function active(): void
	{
		$entities = $this->InsuranceProviders
			->find('active')
			->find('ordered')
			->find('full')
			->all();

		$this->set('data', $entities);
	}

	/**
	 * All method
	 *
	 * @return void
	 */
	public function all(): void
	{
		$entities = $this->InsuranceProviders
			->find('all')
			->find('ordered')
			->find('full')
			->all();

		$this->set('data', $entities);
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
		$entity = $this->InsuranceProviders->get($id);

		try {
			$entity = $this->InsuranceProviders->patchEntity($entity, $this->getRequest()->getData(), [
				'associated' => [
					'AppealLevels._joinData',
					'InsuranceTypes',
				],
			]);

			$this->InsuranceProviders->saveOrFail($entity, []);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
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
		$entity = $this->InsuranceProviders->getFull($id);

		try {
			$this->InsuranceProviders->deleteOrFail($entity);
			$this->set('data', $entity);
			$this->set('success', true);
		} catch (PersistenceFailedException $e) {
			$this->Log->deleteFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}
}
