<?php

declare(strict_types=1);

namespace App\Event;

use App\Mailer\UserMailer;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\ORM\Locator\LocatorAwareTrait;

class UserListener implements EventListenerInterface
{
	use LocatorAwareTrait;

	/**
	 * Implemented Events
	 *
	 * This method maps event names to functions within this class.
	 *
	 * @return array
	 */
	public function implementedEvents(): array
	{
		return [
			'Model.User.registered'			=> 'afterRegistered',
			'Model.User.created'            => 'afterCreated',
			'Model.User.modified'			=> 'afterModified',
			//'Model.User.forgot_password'    => 'forgotPassword', // Handled in UserMailer
			'Model.User.login'				=> 'afterLogin',
			'Model.User.logout'				=> 'afterLogout',
			'Model.User.failed_login'		=> 'failedLogin',
			'Model.User.request_denied'		=> 'requestDenied',
		];
	}

	/**
	 * After Login
	 *
	 * This event is fired after a succesful user login.
	 *
	 * @param \App\Event\Cake\Event\Event $event
	 * @param \App\Model\Entity\User $entity
	 * @return void
	 */
	public function afterLogin(EventInterface $event, EntityInterface $entity, $options = []): void
	{
		// After login
		$users = $this->fetchTable('Users');
		$users->updateLastLogin($entity->id);

		// After login
		$userLogins = $this->fetchTable('UserLogins');
		$userLogins->success($entity, $event, $options);

		Log::info(__(
			'User #{0} `{1}` logged in from IP address `{2}`',
			$entity->id,
			$entity->full_name,
			$_SERVER['REMOTE_ADDR']
		), 'login');
	}

	/**
	 * After Login
	 *
	 * This event is fired after a user initiates a logout
	 *
	 * @param \Cake\Event\Event $event
	 * @param \App\Model\Entity\User $entity
	 * @return void
	 */
	public function afterLogout(EventInterface $event, EntityInterface $entity, $options = []): void
	{
		$request = $event->getSubject()->getRequest();

		$reason = null;
		if (!empty($request->getQuery('reason'))) {
			$reason = $request->getQuery('reason');
		}

		/** @var \App\Model\Table\UserLoginsTable */
		$userLogins = $this->fetchTable('UserLogins');
		$userLogins->logout($entity, $event, [
			'details' => $request->getQuery('reason'),
		]);

		if (!empty($reason)) {
			Log::info(__(
				'User #{0} `{1}` logged out from IP `{2}` with reason: {3}',
				$entity->id,
				$entity->full_name,
				$_SERVER['REMOTE_ADDR'],
				$reason
			), 'login');
		} else {
			Log::info(__(
				'User #{0} `{1}` logged out from IP `{2}`',
				$entity->id,
				$entity->full_name,
				$_SERVER['REMOTE_ADDR']
			), 'login');
		}
	}

	/**
	 * Failed Login
	 *
	 * This event is fired after a failed user login attempt.
	 *
	 * An entity is not passed to the event, only what was entered in the form data
	 *
	 * @param \App\Event\Cake\Event\Event $event
	 * @param array $formData
	 * @param array $options
	 * @return void
	 */
	public function failedLogin(EventInterface $event, array $formData, array $options = []): void
	{
		$userLogins = $this->fetchTable('UserLogins');
		$userLogins->failure($formData, $event, $options);

		$userIdentifier = $formData['email'];

		if (!empty($userIdentifier)) {
			Log::notice(__(
				'Failed login as `{0}` from IP `{1}`',
				h($userIdentifier),
				$_SERVER['REMOTE_ADDR']
			), 'login');
		}
	}

	/**
	 * Request Denied
	 *
	 * A user was denied access to a specific request
	 *
	 * @param \App\Event\Cake\Event\Event $event
	 * @param \App\Event\App\Model\Entity\User $user
	 * @param \App\Event\Cake\Http\ServerRequest $request
	 * @param array $options
	 * @return void
	 */
	public function requestDenied(EventInterface $event, EntityInterface $user, ServerRequest $request, $options = []): void
	{
		Log::error(__(
			'User #{0} `{1}` was denied access to action `{2}` in controller `{3}`. URI: {4}',
			$user->id,
			$user->full_name,
			$request->getParam('action'),
			$request->getParam('controller'),
			$request->getUri()->getPath()
		), 'general');
	}

	/**
	 * After Registered
	 *
	 * A new user registered an account.
	 * May be used in the future to send welcome email or whatever else.
	 *
	 * @param \App\Event\Cake\Event\Event $event
	 * @param \App\Event\App\Model\Entity\User $entity
	 * @param array $options
	 * @return void
	 */
	public function afterRegistered(EventInterface $event, EntityInterface $entity, $options = []): void
	{
		$mailer = new UserMailer();
		$mailer->send('welcome', [$entity]);
	}

	/**
	 * After Created
	 *
	 * A new user was added to the application.
	 * May be used in the future to send welcome email or whatever else.
	 *
	 * @param \App\Event\Cake\Event\Event $event
	 * @param \App\Event\App\Model\Entity\User $entity
	 * @param array $options
	 * @return void
	 */
	public function afterCreated(EventInterface $event, EntityInterface $entity, $options = []): void
	{
		// Setup user
	}

	/**
	 * After Modified
	 *
	 * After an ORM update was issued for this user
	 *
	 * @param \App\Event\Cake\Event\Event $event
	 * @param \App\Event\App\Model\Entity\User $entity
	 * @param array $options
	 * @return void
	 */
	public function afterModified(EventInterface $event, EntityInterface $entity, $options = []): void
	{
		// Nothing here yet...
	}
}
