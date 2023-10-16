<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Lib\NpiUtility\NpiOrganizationResult;
use App\Service\NpiServiceInterface;
use Cake\Cache\Cache;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\Utility\Text;

/**
 * Facilities Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\FacilitiesTable $Facilities
 */
class FacilitiesController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'name' => 'asc',
		],
		'sortableFields' => [
			'created',
			'modified',
			'deleted',
			'name',
			'phone',
			'fax',
			'street_address_1',
			'street_address_2',
			'city',
			'state',
			'zip',
			'active',
			'npi_number',
			'npi_manual',
			'primary_taxonomy',
			'client_owned',
			'chain_name',
			'area_name',
			'ou_number',
			'territory',
			'rvp_name',
			'has_contract',
			'contract_start_date',
			'contract_end_date',
			'indemnification_days',
			'max_return_work_days',
			// Associations
			'FacilityTypes.name',

		],
		'contain' => [
			'FacilityTypes' => [
				'finder' => 'limited'
			],
			'ClientEmployees' => [
				'finder' => 'limited'
			],
			'Services' => [
				'finder' => 'limited'
			]
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
	}

	/**
	 * Add method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function add(): void
	{
		$entity = $this->Facilities->newEntity($this->getRequest()->getData(), [
			'associated' => [
				'Services'
			],
		]);

		try {
			$this->Facilities->saveOrFail($entity);
			$entity = $this->Facilities->getFull($entity->id);
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
		$this->set('data', $this->Facilities->getFull($id));
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
		$entities = $this->Facilities
			->find('active')
			->find('ordered')
			->contain([
				'FacilityTypes',
			])
			->cache('Facilities', 'active')
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
		$entities = $this->Facilities
			->find('all')
			->find('ordered')
			->contain([
				'FacilityTypes',
			])
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
		$entity = $this->Facilities->get($id);

		try {
			$entity = $this->Facilities->patchEntity($entity, $this->getRequest()->getData(), [
				'associated' => [
					'Services'
				],
			]);

			$this->Facilities->saveOrFail($entity);
			$entity = $this->Facilities->getFull($entity->id);
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
		$entity = $this->Facilities->getFull($id);

		try {
			$this->Facilities->deleteOrFail($entity);
			$this->set('data', $entity);
			$this->set('success', true);
		} catch (PersistenceFailedException $e) {
			$this->Log->deleteFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Get the organization details by NPI number provided
	 * @param NpiServiceInterface $npiService
	 * @return void
	 */
	public function npiLookup(NpiServiceInterface $npiService): void
	{
		$this->request->allowMethod(['post']);

		$name = (string) $this->request->getData('name');
		$state = (string) $this->request->getData('state');

		$cacheState = Text::slug(strtoupper($state), '_');
		$cacheName = Text::slug(strtolower($name));
		$cacheKey = 'org_' . $cacheState . '__' . $cacheName;

		/** @var \App\Lib\NpiUtility\NpiOrganizationResult[] */
		$results = Cache::remember(
			$cacheKey,
			function () use ($npiService, $name, $state) {
				return $npiService->searchOrganizationByNameAndState($name, $state);
			},
			'npi'
		);

		$this->set('data', $results);
	}
}
