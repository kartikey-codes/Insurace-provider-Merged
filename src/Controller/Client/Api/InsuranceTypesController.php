<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

/**
 * Insurance Types Controller
 *
 * @property \App\Model\Table\InsuranceTypesTable $InsuranceTypes
 */
class InsuranceTypesController extends ApiController
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
			'created',
			'modified',
			'deleted',
			// Associations
		],
		'contain' => [
			'InsuranceProviders',
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
		$this->set('data', $this->InsuranceTypes->getFull($id));
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
		$entities = $this->InsuranceTypes
			->find('all')
			->find('ordered')
			->find('limited')
			->cache('InsuranceTypes', 'all')
			->all();

		$this->set('data', $entities);
	}
}
