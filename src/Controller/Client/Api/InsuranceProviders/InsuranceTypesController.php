<?php

declare(strict_types=1);

namespace App\Controller\Client\Api\InsuranceProviders;

use App\Controller\Client\Api\ApiController;

/**
 * Insurance Providers Insurance Types Controller
 *
 * @property \App\Model\Table\InsuranceTypesTable $InsuranceTypes
 */
class InsuranceTypesController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
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
	 * Index method
	 *
	 * @return void
	 */
	public function index(): void
	{
		$entities = $this->InsuranceTypes
			->find('byInsuranceProvider', [
				'insurance_provider_id' => $this->getRequest()->getParam('insurance_provider_id'),
			])
			->find('ordered')
			->all();

		$this->set('data', $entities);
	}
}
