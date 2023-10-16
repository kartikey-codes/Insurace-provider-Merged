<?php

declare(strict_types=1);

namespace App\Event;

use App\Mailer\AppealMailer;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;
use Cake\ORM\Locator\LocatorAwareTrait;

class AppealListener implements EventListenerInterface
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
			'Model.Appeal.created' => 'afterCreated',
			'Model.Appeal.modified' => 'afterModified',
			'Model.Appeal.assignedToVendor' => 'assignedToVendor',
			'Model.Appeal.completedByVendor' => 'completedByVendor',
			'Model.Appeal.completed' => 'afterCompleted',
			'Model.Appeal.returnedByVendor' => 'returnedByVendor',
		];
	}

	/**
	 * After Created
	 *
	 * After a new entity was created
	 *
	 * @return void
	 */
	public function afterCreated(EventInterface $event, EntityInterface $entity, $options = []): void
	{
		// Send email, etc.
	}

	/**
	 * After Modified
	 *
	 * After an ORM update was issued for this entity
	 *
	 * @param \App\Event\Cake\Event\Event $event
	 * @param \App\Event\Cake\ORM\Entity $entity
	 * @param array $options
	 * @return void
	 */
	public function afterModified(EventInterface $event, EntityInterface $entity, $options = []): void
	{
		// Nothing here yet...
	}

	/**
	 * Assigned To Vendor
	 *
	 * After an appeal was assigned to a vendor
	 *
	 * @param \App\Event\Cake\Event\Event $event
	 * @param \App\Event\Cake\ORM\Entity $entity
	 * @param array $options
	 * @return void
	 */
	public function assignedToVendor(EventInterface $event, EntityInterface $entity, $options = []): void
	{
		/** @var \App\Model\Table\CasesTable */
		$cases = $this->fetchTable('Cases');

		/** @var \App\Model\Table\AppealsTable */
		$appeals = $this->fetchTable('Appeals');

		/** @var \App\Model\Table\PatientsTable */
		$patients = $this->fetchTable('Patients');

		/** @var \App\Model\Table\VendorsTable */
		$vendors = $this->fetchTable('Vendors');

		/** @var \App\Model\Table\UsersTable */
		$users = $this->fetchTable('Users');

		if ($cases->hasBehavior('Multitenancy')) {
			$cases->removeBehavior('Multitenancy');
		}

		if ($appeals->hasBehavior('Multitenancy')) {
			$appeals->removeBehavior('Multitenancy');
		}

		if ($patients->hasBehavior('Multitenancy')) {
			$patients->removeBehavior('Multitenancy');
		}

		$appeal = $appeals->get($entity->id, [
			'contain' => [
				'Cases' => [
					'Patients',
				],
				'AppealLevels',
			],
		]);

		$vendor = $vendors->get($entity->assigned_to_vendor_id, [
			'skipTenantCheck' => true,
		]);

		$users = $users->find('all', [
			'skipTenantCheck' => true,
		])
			->find('vendorUsersById', [
				'id' => $entity->assigned_to_vendor_id,
			])
			->all();

		foreach ($users as $user) {
			$emailer = new AppealMailer();
			$emailer->send('assignedToVendor', [$appeal, $vendor, $user]);
		}
	}

	/**
	 * Completed By Vendor
	 *
	 * After an appeal was marked as completed by a vendor
	 *
	 * @param \App\Event\Cake\Event\Event $event
	 * @param \App\Event\Cake\ORM\Entity $entity
	 * @param array $options
	 * @return void
	 */
	public function completedByVendor(EventInterface $event, EntityInterface $entity, $options = []): void
	{
		/** @var \App\Model\Table\CasesTable */
		$cases = $this->fetchTable('Cases');

		/** @var \App\Model\Table\AppealsTable */
		$appeals = $this->fetchTable('Appeals');

		/** @var \App\Model\Table\PatientsTable */
		$patients = $this->fetchTable('Patients');

		/** @var \App\Model\Table\ClientsTable */
		$clients = $this->fetchTable('Clients');

		/** @var \App\Model\Table\UsersTable */
		$users = $this->fetchTable('Users');

		if ($cases->hasBehavior('Multitenancy')) {
			$cases->removeBehavior('Multitenancy');
		}

		if ($appeals->hasBehavior('Multitenancy')) {
			$appeals->removeBehavior('Multitenancy');
		}

		if ($patients->hasBehavior('Multitenancy')) {
			$patients->removeBehavior('Multitenancy');
		}

		$appeal = $appeals->get($entity->id, [
			'contain' => [
				'Cases' => [
					'Patients',
				],
				'AppealLevels',
			],
		]);

		$client = $clients->get($entity->client_id, [
			'skipTenantCheck' => true,
		]);

		$users = $users->find('all', [
			'skipTenantCheck' => true,
		])
			->find('clientUsersById', [
				'id' => $entity->client_id,
			])
			->all();

		foreach ($users as $user) {
			$emailer = new AppealMailer();
			$emailer->send('completedByVendor', [$appeal, $client, $user]);
		}
	}

	/**
	 * Returned By Vendor
	 *
	 * After an appeal was marked as returned by a vendor
	 *
	 * @param \App\Event\Cake\Event\Event $event
	 * @param \App\Event\Cake\ORM\Entity $entity
	 * @param array $options
	 * @return void
	 */
	public function returnedByVendor(EventInterface $event, EntityInterface $entity, $options = []): void
	{
		/** @var \App\Model\Table\CasesTable */
		$cases = $this->fetchTable('Cases');

		/** @var \App\Model\Table\AppealsTable */
		$appeals = $this->fetchTable('Appeals');

		/** @var \App\Model\Table\PatientsTable */
		$patients = $this->fetchTable('Patients');

		/** @var \App\Model\Table\ClientsTable */
		$clients = $this->fetchTable('Clients');

		/** @var \App\Model\Table\UsersTable */
		$users = $this->fetchTable('Users');

		if ($cases->hasBehavior('Multitenancy')) {
			$cases->removeBehavior('Multitenancy');
		}

		if ($appeals->hasBehavior('Multitenancy')) {
			$appeals->removeBehavior('Multitenancy');
		}

		if ($patients->hasBehavior('Multitenancy')) {
			$patients->removeBehavior('Multitenancy');
		}

		$appeal = $appeals->get($entity->id, [
			'contain' => [
				'Cases' => [
					'Patients',
				],
				'AppealLevels',
			],
		]);

		$client = $clients->get($entity->client_id, [
			'skipTenantCheck' => true,
		]);

		$users = $users->find('all', [
			'skipTenantCheck' => true,
		])
			->find('clientUsersById', [
				'id' => $entity->client_id,
			])
			->all();

		foreach ($users as $user) {
			$emailer = new AppealMailer();
			$emailer->send('returnedByVendor', [$appeal, $client, $user]);
		}
	}

	/**
	 * After Completed
	 *
	 * After an appeal was marked as completed
	 *
	 * @param \App\Event\Cake\Event\Event $event
	 * @param \App\Event\Cake\ORM\Entity $entity
	 * @param array $options
	 * @return void
	 */
	public function afterCompleted(EventInterface $event, EntityInterface $entity, $options = []): void
	{
		// Nothing here yet...
	}
}
