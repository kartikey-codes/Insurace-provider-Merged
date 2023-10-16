<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Service\NpiServiceInterface;
use Cake\Cache\Cache;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\Utility\Text;

/**
 * Client Employee Types Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\ClientEmployeesTable $ClientEmployees
 */
class ClientEmployeesController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'last_name' => 'asc',
			'first_name' => 'asc'
		],
		'sortableFields' => [
			'first_name',
			'last_name',
			'title',
			'work_phone',
			'mobile_phone',
			'email',
			'state',
			'created',
			'modified',
			'deleted',
			'active',
			'npi_number',
			// Associations
			'Facilities.name',
		],
		'contain' => [
			'Facilities',
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
		$entity = $this->ClientEmployees->newEntity($this->getRequest()->getData(), [
			'fields' => [
				'active',
				'first_name',
				'last_name',
				'facility_id',
				'npi_number',
				'state',
				'title',
				'email',
				'mobile_phone',
				'work_phone'
			],
			'associated' => [],
		]);

		try {
			$this->ClientEmployees->saveOrFail($entity);
			$entity = $this->ClientEmployees->getFull($entity->id);
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
		$this->set('data', $this->ClientEmployees->getFull($id));
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
	 * All method
	 *
	 * @return void
	 */
	public function all(): void
	{
		$entities = $this->ClientEmployees
			->find('all')
			->find('ordered')
			->all();

		$this->set('data', $entities);
	}

	/**
	 * Active method
	 *
	 * @return void
	 */
	public function active(): void
	{
		$entities = $this->ClientEmployees
			->find('active')
			->find('ordered')
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
		$entity = $this->ClientEmployees->get($id);

		try {
			$entity = $this->ClientEmployees->patchEntity($entity, $this->getRequest()->getData(), [
				'associated' => [],
			]);
			$this->ClientEmployees->saveOrFail($entity);
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
		$entity = $this->ClientEmployees->getFull($id);

		try {
			$this->ClientEmployees->deleteOrFail($entity);
			$this->set('data', $entity);
			$this->set('success', true);
		} catch (PersistenceFailedException $e) {
			$this->Log->deleteFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Lookup from NPI Service method
	 *
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When rules check fails.
	 */
	public function lookup(NpiServiceInterface $npiService): void
	{
		$this->getRequest()->allowMethod('post');

		$firstName = $this->getRequest()->getData('first_name', '');
		$lastName = $this->getRequest()->getData('last_name', '');
		$state = $this->getRequest()->getData('state', '');
		$exact = $this->getRequest()->getData('exact', false);

		$cacheState = Text::slug(strtoupper($state));
		$cacheName = Text::slug(strtolower($lastName)) . '__' . Text::slug(strtolower($firstName));
		$cacheKey = 'ind_' . $cacheState . '__' . $cacheName;

		/** @var \App\Lib\NpiUtility\NpiOrganizationResult[] */
		$results = Cache::remember(
			$cacheKey,
			function () use ($npiService, $firstName, $lastName, $state, $exact) {
				return $npiService->searchIndividualByNameAndState($firstName, $lastName, $state, $exact);
			},
			'npi'
		);

		$this->set('data', $results);
	}
}
