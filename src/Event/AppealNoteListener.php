<?php

declare(strict_types=1);

namespace App\Event;

use App\Mailer\AppealNoteMailer;
use App\Model\Entity\AppealNote;
use App\Model\Entity\Client;
use App\Model\Entity\User;
use App\Model\Entity\Vendor;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;
use Cake\ORM\Locator\LocatorAwareTrait;

class AppealNoteListener implements EventListenerInterface
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
			'Model.AppealNote.created' => 'afterCreated',
		];
	}

	/**
	 * After a new entity was created
	 *
	 * @param \App\Event\Cake\Event\Event $event
	 * @param \App\Model\Entity\AppealNote $entity
	 * @param array $options
	 * @return void
	 */
	public function afterCreated(EventInterface $event, EntityInterface $entity, $options = []): void
	{
		$appeals = $this->fetchTable('Appeals');
		$users = $this->fetchTable('Users');

		/**
		 * Get related appeal and any association info for the notification
		 *
		 * @var \App\Model\Entity\Appeal
		 */
		$appeal = $appeals->get($entity->appeal_id, [
			'contain' => [
				'AppealLevels',
				'Cases' => [
					'CaseTypes',
					'Patients',
				],
				'Clients' => [
					// @disabled @todo - This can potentially cause too many emails per second
					//'Users'
				],
				'AssignedToVendors' => [
					'Users',
				],
			],
		]);

		// Pass full appeal back into appeal note association
		$entity->appeal = $appeal;

		/**
		 * Get user who created this appeal note
		 *
		 * @var \App\Model\Entity\User
		 */
		$createdByUser = $users->get($entity->created_by, [
			'skipTenantCheck' => true,
		]);

		/**
		 * Get client this appeal note is for
		 *
		 * @var ?\App\Model\Entity\Client
		 */
		$client = $appeal->client;

		/**
		 * Get vendor this appeal note is potentially for
		 *
		 * @var ?\App\Model\Entity\Vendor
		 */
		$vendor = $appeal->assigned_to_vendor;

		// Determine who to email based on who created the note
		if ($createdByUser->isAdmin()) {
			if (!empty($client)) {
				$this->notifyClient($entity, $createdByUser, $client);
			}
			if (!empty($vendor)) {
				$this->notifyVendor($entity, $createdByUser, $vendor);
			}
		} elseif ($createdByUser->isVendorUser() && !empty($client)) {
			$this->notifyClient($entity, $createdByUser, $client);
		} elseif ($createdByUser->isClientUser() && !empty($vendor)) {
			$this->notifyVendor($entity, $createdByUser, $vendor);
		}
	}

	/**
	 * Send new appeal note email to all users under a client
	 *
	 * @param \App\Event\App\Model\Entity\AppealNote $entity
	 * @param array $createdByUser
	 * @return void
	 */
	public function notifyClient(AppealNote $entity, User $createdByUser, Client $client): void
	{
		$mailer = new AppealNoteMailer();

		// Ignore no client users for now
		if (empty($client->users)) {
			//throw new RuntimeException(__('No client users were provided to notify of appeal note.'));
			return;
		}

		foreach ($client->users as $user) {
			$mailer->send('notifyClient', [$entity, $createdByUser, $client, $user]);
		}
	}

	/**
	 * Send new appeal note email to all users under a vendor
	 *
	 * @param \App\Model\Entity\AppealNote $entity
	 * @param \App\Model\Entity\User $createdByUser
	 * @param \App\Model\Entity\Vendor $vendor
	 * @return void
	 */
	public function notifyVendor(AppealNote $entity, User $createdByUser, Vendor $vendor): void
	{
		$mailer = new AppealNoteMailer();

		if (empty($vendor->users)) {
			// Log::warning('No vendor users were provided to notify of appeal note.');
			// throw new RuntimeException(__('No vendor users were provided to notify of appeal note.'));

			// Client may write a note without an assigned vendor, so ignore no vendor users.
			return;
		}

		foreach ($vendor->users as $user) {
			$mailer->send('notifyVendor', [$entity, $createdByUser, $vendor, $user]);
		}
	}
}
