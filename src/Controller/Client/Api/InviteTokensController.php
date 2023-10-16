<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Invite Tokens Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\InviteTokensTable $InviteTokens
 */
class InviteTokensController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'limit' => PAGINATION_HIGH_LIMIT,
		'order' => [
			'id' => 'asc',
		],
		'sortableFields' => [
			'id',
			'active',
			'created',
			'modified',
		],
		'contain' => [],
	];

	/**
	 * Add method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function add(): void
	{
		$entity = $this->InviteTokens->newEntity($this->getRequest()->getData());

		try {
			$this->InviteTokens->saveOrFail($entity, []);

			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}
}
