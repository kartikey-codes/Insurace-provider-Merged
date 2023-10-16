<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Model\Entity\InviteToken;
use Cake\Core\Configure;
use Cake\Mailer\Mailer;
use Cake\ORM\Locator\LocatorAwareTrait;

class UserInviteMailer extends Mailer
{
	use LocatorAwareTrait;

	/**
	 * Send an invite token to an email address
	 *
	 * @param \App\Model\Entity\InviteToken $inviteToken
	 * @param string $email
	 * @return \Cake\Mailer\Mailer
	 */
	public function sendToEmail(InviteToken $token, string $email): Mailer
	{
		$appName = Configure::readOrFail('App.name');

		$this->viewBuilder()->setTemplate('invite_token');

		$this
			->setTo($email, $email)
			->setSubject(__('Invite to {0}', $appName))
			->setViewVars([
				'email' => $email,
				'token' => $token
			])
			->setEmailFormat('both');

		return $this;
	}
}
