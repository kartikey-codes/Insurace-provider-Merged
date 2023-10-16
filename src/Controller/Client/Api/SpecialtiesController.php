<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

/**
 * Specialties Controller
 *
 * @property \App\Model\Table\SpecialtiesTable $Specialties
 */
class SpecialtiesController extends ApiController
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
		$entity = $this->Specialties->get($id);

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
		$entities = $this->Specialties
			->find('all')
			->find('ordered')
			->find('limited')
			->cache('Specialties', 'all')
			->all();

		$this->set('data', $entities);
	}
}
