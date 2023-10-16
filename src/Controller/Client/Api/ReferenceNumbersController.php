<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

/**
 * Reference Numbers Controller
 *
 * @property \App\Model\Table\ReferenceNumbersTable $ReferenceNumbers
 */
class ReferenceNumbersController extends ApiController
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
			'id',
			'name',
			'created',
			'modified',
			'deleted',
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
		$entity = $this->ReferenceNumbers->get($id);

		$this->set('data', $entity);
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
		$entities = $this->ReferenceNumbers
			->find('all')
			->find('ordered')
			->find('limited')
			->cache('ReferenceNumbers', 'all')
			->all();

		$this->set('data', $entities);
	}
}
