<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Lib\TenantUtility\TenantUtility;
use App\Mailer\UserInviteMailer;
use App\Model\Table\InviteTokensTable;
use App\Model\Table\UsersTable;

/**
 * User Invites Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\InviteTokensTable $InviteTokens
 * @property \App\Model\Table\UsersTable $Users
 */
class UserInvitesController extends ApiController
{
	/** @var \App\Model\Table\InviteTokensTable */
	private InviteTokensTable $InviteTokens;

	/** @var \App\Model\Table\UsersTable */
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
		$this->InviteTokens = $this->fetchTable('InviteTokens');
		$this->Users = $this->fetchTable('Users');

		parent::initialize();
	}

	/**
	 * Add method
	 *
	 * Creates a new invite token and emails it to the recipient
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function add(): void
	{
		$this->request->allowMethod(['post']);

		/**
		 * @var string $email
		 */
		$email = $this->getRequest()->getData("email");

		/**
		 * @var int $clientId
		 */
		$clientId = (int)TenantUtility::getTenantIdFromRequest();

		/**
		 * @var \App\Model\Entity\InviteToken $inviteToken
		 */
		$inviteToken = $this->InviteTokens->generateForClient($clientId);

		$mailer = new UserInviteMailer();
		$mailer->sendToEmail($inviteToken, $email)->send();

		$this->set([
			'success' => true,
			'email' => $email,
			'data' => $inviteToken,
		]);
	}
}
