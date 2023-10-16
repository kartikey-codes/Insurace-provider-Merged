<?php

declare(strict_types=1);

namespace App\Controller\Vendor\Api;

use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Vendors Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\VendorsTable $Vendors
 */
class VendorsController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'name' => 'asc',
		],
		'sortableFields' => [
			'name',
			'email',
			'active',
			'phone',
			'fax',
			'street_address_1',
			'street_address_2',
			'city',
			'state',
			'zip',
			'contact_first_name',
			'contact_last_name',
			'contact_title',
			'contact_email',
			'contact_phone',
			'created',
			'modified',
			'deleted',
			'status',
		],
	];

	/**
	 * View method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function view($id): void
	{
		$this->set('data', $this->Vendors->getFull($id));
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
		$entities = $this->Vendors
			->find('all')
			->find('ordered')
			->cache('Vendors', 'all')
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
		$entities = $this->Vendors
			->find('active')
			->find('ordered')
			->cache('Vendors', 'active')
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
		$entity = $this->Vendors->get($id);

		try {
			$requestData = $this->getRequest()->getData();
			if (isset($requestData['status'])) {
				$requestData['active'] = $requestData['status'] === 'Active';
			}
			$entity = $this->Vendors->patchEntity($entity, $requestData);
			$this->Vendors->saveOrFail($entity);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}
}
