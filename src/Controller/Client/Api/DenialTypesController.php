<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

/**
 * Denial Types Controller
 *
 * @property \App\Model\Table\DenialTypesTable $DenialTypes
 */
class DenialTypesController extends ApiController
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
			'name',
			'multiple_service_dates',
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
		$this->set('data', $this->DenialTypes->get($id));
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
		$entities = $this->DenialTypes
			->find('all')
			->find('ordered')
			->find('limited')
			->cache('DenialTypes', 'all')
			->all();

		$this->set('data', $entities);
	}
}
