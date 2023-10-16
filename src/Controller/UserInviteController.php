<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\InviteToken;
use App\Model\Entity\User;
use App\Model\Table\InviteTokensTable;
use App\Model\Table\UsersTable;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;

/**
 * User Invite Controller
 *
 * Allows users sent an Invite Token to register or join an
 * existing client or vendor group.
 */
class UserInviteController extends AppController
{
	/**
	 * @var \App\Model\Table\InviteTokensTable
	 */
	private InviteTokensTable $InviteTokens;

	/**
	 * @var \App\Model\Table\UsersTable
	 */
	private UsersTable $Users;

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

		$this->Authentication->allowUnauthenticated([
			'redeem',
		]);

		$this->InviteTokens = $this->fetchTable('InviteTokens');
		$this->Users = $this->fetchTable('Users');
	}

	/**
	 * Before render callback.
	 * Executes before the view is rendered
	 *
	 * @param \Cake\Event\Event $event The beforeRender event.
	 * @return void
	 */
	public function beforeRender(EventInterface $event): void
	{
		parent::beforeRender($event);

		$this->viewBuilder()
			->setLayout('login');
	}

	/**
	 * Redeem Token & Register/Join
	 *
	 * @return void
	 */
	public function redeem(): void
	{
		$this->request->allowMethod(['get', 'post']);

		$tokenValue = $this->getRequest()->getQuery('token');
		$token = $this->InviteTokens->getByTokenValue($tokenValue);

		$identity = $this->Authentication->getIdentity();

		/**
		 * @var ?\App\Model\Entity\User
		 */
		$user = $identity ? $identity->getOriginalData() : null;

		$this->set(compact(
			'token',
			'user',
		));

		if (!$token->active) {
			$this->Flash->error(__('The invite token supplied was already used.'));
			$this->redirect(['_name' => 'login']);

			return;
		}

		if ($this->request->is('post')) {
			if (!empty($user->id)) {
				$this->joinUserByToken($token, $user->id);
				$this->redirect('/');

				return;
			} else {
				$this->redirect([
					'_name' => 'userRegister',
					'?' => [
						'inviteToken' => $tokenValue,
					],
				]);

				return;
			}
		}
	}

	/**
	 * Apply invite token to existing user to join organization
	 *
	 * @param \App\Model\Entity\InviteToken $token
	 * @param int $userId
	 * @return \Cake\Datasource\EntityInterface
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException
	 * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	private function joinUserByToken(InviteToken $token, int $userId): EntityInterface
	{
		$userEntity = $this->Users->get($userId);

		if (!empty($token->client_id)) {
			$userEntity->set('client_id', $token->client_id);
		}
		if (!empty($token->vendor_id)) {
			$userEntity->set('vendor_id', $token->vendor_id);
		}

		$this->Users->saveOrFail($userEntity);

		$token->set('active', false);
		$this->InviteTokens->saveOrFail($token);

		return $userEntity;
	}
}
