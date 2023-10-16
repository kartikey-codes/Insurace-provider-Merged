<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Model\Table\UsersTable;
use Cake\Core\Exception\CakeException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Auth Controller
 */
class AuthController extends ApiController
{
	use LocatorAwareTrait;

	/**
     * @var \App\Model\Table\UsersTable
     */
	public UsersTable $Users;

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

		$this->Users = $this->fetchTable('Users');
	}

	/**
	 * Me
	 *
	 * Returns the current user based on token authentication
	 *
	 * @return void
	 */
	public function me(): void
	{
		if (empty($this->currentUser->id)) {
			throw new UnauthorizedException(__(
				'Unable to validate user by token.'
			));
		}

		$this->set('user', $this->currentUser->getOriginalData());
	}

	/**
	 * Update Profile
	 *
	 * User updated their own account information
	 *
	 * @return void
	 */
	public function updateProfile(): void
	{
		$this->getRequest()->allowMethod(['PATCH', 'POST', 'PUT']);

		$user = $this->Users->get($this->currentUser->id);

		$entity = $this->Users->patchEntity($user, $this->getRequest()->getData(), [
			'fields' => [
				'first_name',
				'middle_name',
				'last_name',
				'phone',
				'date_of_birth',
			],
		]);

		$this->Users->saveOrFail($entity);

		$this->set('data', $entity);
	}

	/**
	 * Change Password
	 *
	 * Change the current user's password
	 *
	 * @return void
	 */
	public function changePassword(): void
	{
		/** @var \App\Model\Entity\User */
		$entity = $this->Users->get($this->currentUser->id);

		try {
			$entity = $this->Users->patchEntity($entity, $this->getRequest()->getData(), [
				'associated' => [],
				'fields' => [
					'current_password',
					'password',
					'confirm_password',
				],
			]);

			$this->Users->saveOrFail($entity, [
				//'skipTenantCheck' => $entity->isAdmin()
			]);

			$entity = $this->Users->get($entity->id);

			$this->set('data', $entity);
		} catch (CakeException $e) {
			if (!empty($entity->getErrors())) {
				$this->set('errors', $entity->getErrors());
			}
			$this->set('message', $e->getMessage());
			$this->response = $this->response->withStatus(422);
		}
	}
}
