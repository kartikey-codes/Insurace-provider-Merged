<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Model\Entity\Client;
use App\Model\Entity\User;
use App\Model\Entity\Vendor;
use Cake\Mailer\Mailer;

class AdminMailer extends Mailer
{
	/**
	 * Notify admin user of new client registration
	 *
	 * @param \App\Model\Entity\User $user
	 * @param \App\Model\Entity\Client $client
	 * @return \Cake\Mailer\Mailer
	 */
	public function clientRegister(User $user, Client $client): Mailer
	{
		$this
			->setTo($user->email, $user->full_name)
			->setSubject(__('Client Registered - {0}', $client->name))
			->setViewVars(compact('user', 'client'))
			->setEmailFormat('both');

		return $this;
	}

	/**
	 * Notify admin user of new vendor registration
	 *
	 * @param \App\Model\Entity\User $user
	 * @param \App\Model\Entity\Vendor $vendor
	 * @return \Cake\Mailer\Mailer
	 */
	public function vendorRegister(User $user, Vendor $vendor): Mailer
	{
		$this
			->setTo($user->email, $user->full_name)
			->setSubject(__('Vendor Registered - {0}', $vendor->name))
			->setViewVars(compact('user', 'vendor'))
			->setEmailFormat('both');

		return $this;
	}
}
