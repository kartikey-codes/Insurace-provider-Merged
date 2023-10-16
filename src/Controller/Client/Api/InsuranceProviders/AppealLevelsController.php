<?php

declare(strict_types=1);

namespace App\Controller\Client\Api\InsuranceProviders;

use App\Controller\Client\Api\ApiController;

/**
 * Insurance Providers Appeal Levels Controller
 *
 * @property \App\Model\Table\AppealLevelsTable $AppealLevels
 */
class AppealLevelsController extends ApiController
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
			'created',
			'modified',
			'deleted',
		],
	];

	/**
	 * Index method
	 *
	 * @return void
	 */
	public function index(): void
	{
		$entities = $this->AppealLevels
			->find('byInsuranceProvider', [
				'insurance_provider_id' => $this->getRequest()->getParam('insurance_provider_id'),
			])
			->find('ordered')
			->all();

		$this->set('data', $entities);
	}
}
