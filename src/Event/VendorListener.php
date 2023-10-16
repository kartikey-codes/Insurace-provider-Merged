<?php

declare(strict_types=1);

namespace App\Event;

use App\Mailer\AdminMailer;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;
use Cake\ORM\Locator\LocatorAwareTrait;
use RuntimeException;

class VendorListener implements EventListenerInterface
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
			'Model.Vendor.registered' => 'afterRegister',
		];
	}

	/**
	 * After Register
	 *
	 * After an appeal was marked as completed
	 *
	 * @param \App\Event\Cake\Event\Event $event
	 * @param \App\Event\Cake\ORM\Entity $entity
	 * @param array $options
	 * @return void
	 */
	public function afterRegister(EventInterface $event, EntityInterface $entity, $options = []): void
	{
		/** @var \App\Model\Table\UsersTable */
		$users = $this->fetchTable('Users');
		$adminUsers = $users->getAllActiveAdmins();

		foreach ($adminUsers as $adminUser) {
			if (empty($adminUser->email) || $adminUser->email == 'NULL') {
				throw new RuntimeException(__('Admin #{0} `{1}` does not have an email address.', $adminUser->id, $adminUser->full_name));
			}

			$emailer = new AdminMailer();
			$emailer->send('vendorRegister', [$adminUser, $entity]);
		}
	}
}
