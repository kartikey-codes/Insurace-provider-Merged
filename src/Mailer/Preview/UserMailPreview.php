<?php

declare(strict_types=1);

namespace App\Mailer\Preview;

use App\Model\Table\UsersTable;
use Cake\Datasource\FactoryLocator;
use Cake\Mailer\Mailer;
use DebugKit\Mailer\MailPreview;

/**
 * User Mail Preview
 */
class UserMailPreview extends MailPreview
{
	/**
     * @var \App\Model\Table\UsersTable
     */
	public UsersTable $Users;

	/**
	 * Welcome
     *
     * @return \Cake\Mailer\Mailer
	 * @throws \Cake\Datasource\Exception\MissingModelException
	 * @throws \UnexpectedValueException
	 * @throws \Cake\Mailer\Exception\MissingMailerException
	 */
	public function welcome(): Mailer
	{
		/** @var \App\Model\Table\UsersTable */
		$this->Users = FactoryLocator::get('Table')->get('Users');

		/** @var \App\Mailer\Preview\App\Model\Entity\User */
		$user = $this->Users
			->find('all', ['skipTenantCheck' => true])
			->firstOrFail();

		$tempPassword = $user->generateTemporaryPassword();

		return $this->getMailer('User')
			->welcome($user, $tempPassword);
	}

	/**
	 * Forgot Password
     *
     * @return \Cake\Mailer\Mailer
	 * @throws \Cake\Datasource\Exception\MissingModelException
	 * @throws \UnexpectedValueException
	 * @throws \Cake\Mailer\Exception\MissingMailerException
	 */
	public function forgotPassword(): Mailer
	{
		/** @var \App\Model\Table\UsersTable */
		$this->Users = FactoryLocator::get('Table')->get('Users');

		/** @var \App\Mailer\Preview\App\Model\Entity\User */
		$user = $this->Users
			->find('all', ['skipTenantCheck' => true])
			->firstOrFail();

		return $this->getMailer('User')
			->forgotPassword($user);
	}
}
