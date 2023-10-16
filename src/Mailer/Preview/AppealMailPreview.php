<?php

declare(strict_types=1);

namespace App\Mailer\Preview;

use App\Model\Table\AppealsTable;
use App\Model\Table\CasesTable;
use App\Model\Table\ClientsTable;
use App\Model\Table\PatientsTable;
use App\Model\Table\UsersTable;
use App\Model\Table\VendorsTable;
use Cake\Datasource\FactoryLocator;
use Cake\Mailer\Mailer;
use DebugKit\Mailer\MailPreview;

/**
 * Appeal Mail Preview
 */
class AppealMailPreview extends MailPreview
{
	/**
     * @var \App\Model\Table\AppealsTable
     */
	public AppealsTable $Appeals;

	/**
     * @var \App\Model\Table\CasesTable
     */
	public CasesTable $Cases;

	/**
     * @var \App\Model\Table\ClientsTable
     */
	public ClientsTable $Clients;

	/**
     * @var \App\Model\Table\UsersTable
     */
	public UsersTable $Users;

	/**
     * @var \App\Model\Table\PatientsTable
     */
	public PatientsTable $Patients;

	/**
     * @var \App\Model\Table\VendorsTable
     */
	public VendorsTable $Vendors;

	/**
	 * Load models for this preview
     *
     * @return \App\Mailer\Preview\AppealMailPreview
	 * @throws \Cake\Datasource\Exception\MissingModelException
	 * @throws \UnexpectedValueException
	 */
	private function loadTables(): self
	{
		$this->Cases = FactoryLocator::get('Table')->get('Cases');
		$this->Appeals = FactoryLocator::get('Table')->get('Appeals');
		$this->Users = FactoryLocator::get('Table')->get('Users');
		$this->Vendors = FactoryLocator::get('Table')->get('Vendors');
		$this->Patients = FactoryLocator::get('Table')->get('Patients');
		$this->Clients = FactoryLocator::get('Table')->get('Clients');

		return $this;
	}

	/**
	 * Assigned To Vendor
     *
     * @return \Cake\Mailer\Mailer
	 * @throws \Cake\Datasource\Exception\MissingModelException
	 * @throws \UnexpectedValueException
	 * @throws \Cake\Mailer\Exception\MissingMailerException
	 */
	public function assignedToVendor(): Mailer
	{
		$this->loadTables();

		$options = ['skipTenantCheck' => true];

		/**
		 * Tenant check is going off on all associated queries so it has to be disabled
		 * to load an appeal for a preview.
		 */

		if ($this->Cases->hasBehavior('Multitenancy')) {
			$this->Cases->removeBehavior('Multitenancy');
		}

		if ($this->Patients->hasBehavior('Multitenancy')) {
			$this->Patients->removeBehavior('Multitenancy');
		}

		/** @var \App\Mailer\Preview\App\Model\Entity\Appeal */
		$appeal = $this->Appeals->find('all', $options)
			->contain([
				'Cases' => [
					'Patients',
				],
				'AppealLevels',
			])
			->firstOrFail();

		/** @var \App\Mailer\Preview\App\Model\Entity\Vendor */
		$vendor = $this->Vendors->find('all', $options)
			->firstOrFail();

		/** @var \App\Mailer\Preview\App\Model\Entity\User */
		$user = $this->Users->find('vendorUsersById', [
			'id' => $vendor->id,
			'skipTenantCheck' => true,
		])
			->firstOrFail();

		return $this->getMailer('Appeal')
			->assignedToVendor($appeal, $vendor, $user);
	}

	/**
	 * Completed By Vendor
     *
     * @return \Cake\Mailer\Mailer
	 * @throws \Cake\Datasource\Exception\MissingModelException
	 * @throws \UnexpectedValueException
	 * @throws \Cake\Mailer\Exception\MissingMailerException
	 */
	public function completedByVendor(): Mailer
	{
		$this->loadTables();

		$options = ['skipTenantCheck' => true];

		/**
		 * Tenant check is going off on all associated queries so it has to be disabled
		 * to load an appeal for a preview.
		 */

		if ($this->Cases->hasBehavior('Multitenancy')) {
			$this->Cases->removeBehavior('Multitenancy');
		}

		if ($this->Patients->hasBehavior('Multitenancy')) {
			$this->Patients->removeBehavior('Multitenancy');
		}

		if ($this->Users->hasBehavior('Multitenancy')) {
			$this->Users->removeBehavior('Multitenancy');
		}

		/** @var \App\Mailer\Preview\App\Model\Entity\Appeal */
		$appeal = $this->Appeals->find('all', $options)
			->contain([
				'Cases' => [
					'Patients',
				],
				'AppealLevels',
			])
			->firstOrFail();

		/** @var \App\Mailer\Preview\App\Model\Entity\Client */
		$client = $this->Clients->find('all', $options)
			->matching('Users')
			->firstOrFail();

		/** @var \App\Mailer\Preview\App\Model\Entity\User */
		$user = $this->Users->find('clientUsersById', [
			'id' => $client->id,
			'skipTenantCheck' => true,
		])
			->firstOrFail();

		return $this->getMailer('Appeal')
			->completedByVendor($appeal, $client, $user);
	}

	/**
	 * Returned By Vendor
     *
     * @return \Cake\Mailer\Mailer
	 * @throws \Cake\Datasource\Exception\MissingModelException
	 * @throws \UnexpectedValueException
	 * @throws \Cake\Mailer\Exception\MissingMailerException
	 */
	public function returnedByVendor(): Mailer
	{
		$this->loadTables();

		$options = ['skipTenantCheck' => true];

		/**
		 * Tenant check is going off on all associated queries so it has to be disabled
		 * to load an appeal for a preview.
		 */

		if ($this->Cases->hasBehavior('Multitenancy')) {
			$this->Cases->removeBehavior('Multitenancy');
		}

		if ($this->Patients->hasBehavior('Multitenancy')) {
			$this->Patients->removeBehavior('Multitenancy');
		}

		if ($this->Users->hasBehavior('Multitenancy')) {
			$this->Users->removeBehavior('Multitenancy');
		}

		/** @var \App\Mailer\Preview\App\Model\Entity\Appeal */
		$appeal = $this->Appeals->find('all', $options)
			->contain([
				'Cases' => [
					'Patients',
				],
				'AppealLevels',
			])
			->firstOrFail();

		/** @var \App\Mailer\Preview\App\Model\Entity\Client */
		$client = $this->Clients->find('all', $options)
			->matching('Users')
			->firstOrFail();

		/** @var \App\Mailer\Preview\App\Model\Entity\User */
		$user = $this->Users->find('clientUsersById', [
			'id' => $client->id,
			'skipTenantCheck' => true,
		])
			->firstOrFail();

		return $this->getMailer('Appeal')
			->returnedByVendor($appeal, $client, $user);
	}
}
