<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Mailer\Mailer;

class UserMailer extends Mailer
{
	/**
	 * Implemented event listeners
	 *
	 * @return array
	 */
	public function implementedEvents(): array
	{
		return [
			'Model.User.forgot_password' => 'onForgotPassword',
		];
	}

	/**
	 * Send forgot password email
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param \Cake\Datasource\EntityInterface $entity
	 * @param array $options
	 * @return void
	 * @throws \Cake\Mailer\Exception\MissingActionException
	 * @throws \BadMethodCallException
	 */
	public function onForgotPassword(EventInterface $event, EntityInterface $entity, array $options): void
	{
		$this->send('forgotPassword', [$entity]);
	}

	/**
	 * Send welcome to application email
	 *
	 * @param \App\Model\Entity\User $user
	 * @return \Cake\Mailer\Mailer
	 */
	public function welcome(User $user): Mailer
	{
		return $this
			->setTo($user->email, $user->full_name)
			->setSubject(sprintf('Welcome %s', $user->full_name))
			->setViewVars(compact('user'))
			->setEmailFormat('both');
	}

	/**
	 * Send forgot password email
	 *
	 * @param \App\Model\Entity\User $user
	 * @return \Cake\Mailer\Mailer
	 */
	public function forgotPassword(User $user): Mailer
	{
		return $this
			->setTo($user->email, $user->full_name)
			->setSubject(sprintf('Forgot Password request for %s', Configure::readOrFail('App.name')))
			->setViewVars(compact('user'))
			->setEmailFormat('both');
	}
}
