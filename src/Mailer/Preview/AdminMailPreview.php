<?php

declare(strict_types=1);

namespace App\Mailer\Preview;

use App\Model\Table\ClientsTable;
use App\Model\Table\UsersTable;
use App\Model\Table\VendorsTable;
use Cake\Datasource\FactoryLocator;
use Cake\Mailer\Mailer;
use DebugKit\Mailer\MailPreview;

/**
 * Admin Email Preview
 */
class AdminMailPreview extends MailPreview
{
	/**
	 * @var \App\Model\Table\UsersTable
	 */
	public UsersTable $Users;

	/**
	 * @var \App\Model\Table\ClientsTable
	 */
	public ClientsTable $Clients;

	/**
	 * @var \App\Model\Table\VendorsTable
	 */
	public VendorsTable $Vendors;

	/**
	 * Client Register
	 *
	 * @return \Cake\Mailer\Mailer
	 * @throws \Cake\Datasource\Exception\MissingModelException
	 * @throws \UnexpectedValueException
	 * @throws \Cake\Mailer\Exception\MissingMailerException
	 */
	public function clientRegister(): Mailer
	{
		/** @var \App\Model\Table\UsersTable */
		$this->Users = FactoryLocator::get('Table')->get('Users');
		/** @var \App\Model\Table\ClientsTable */
		$this->Clients = FactoryLocator::get('Table')->get('Clients');

		/** @var \App\Mailer\Preview\App\Model\Entity\User */
		$user = $this->Users
			->find('all', ['skipTenantCheck' => true])
			->find('admins')
			->find('active')
			->firstOrFail();

		/** @var \App\Mailer\Preview\App\Model\Entity\Client */
		$client = $this->Clients
			->find('all', ['skipTenantCheck' => true])
			->contain([
				'Owner',
				'ClientTypes',
			])
			->firstOrFail();

		return $this->getMailer('Admin')
			->clientRegister($user, $client);
	}

	/**
	 * Vendor Register
	 *
	 * @return \Cake\Mailer\Mailer
	 * @throws \Cake\Datasource\Exception\MissingModelException
	 * @throws \UnexpectedValueException
	 * @throws \Cake\Mailer\Exception\MissingMailerException
	 */
	public function vendorRegister(): Mailer
	{
		/** @var \App\Model\Table\UsersTable */
		$this->Users = FactoryLocator::get('Table')->get('Users');
		/** @var \App\Model\Table\VendorsTable */
		$this->Vendors = FactoryLocator::get('Table')->get('Vendors');

		/** @var \App\Mailer\Preview\App\Model\Entity\User */
		$user = $this->Users
			->find('all', ['skipTenantCheck' => true])
			->find('admins')
			->find('active')
			->firstOrFail();

		/** @var \App\Mailer\Preview\App\Model\Entity\Vendor */
		$vendor = $this->Vendors
			->find('all', ['skipTenantCheck' => true])
			->contain([
				'Owner',
			])
			->firstOrFail();

		return $this->getMailer('Admin')
			->vendorRegister($user, $vendor);
	}
}
