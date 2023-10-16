<?php

declare(strict_types=1);

namespace App\Event;

use App\Mailer\AdminMailer;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use Cake\ORM\Locator\LocatorAwareTrait;
use RuntimeException;

class ClientListener implements EventListenerInterface
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
			'Model.Client.registered' => 'afterRegister',
			'Model.Client.updatedLicenses' => 'updatedLicenses',
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
			$emailer->send('clientRegister', [$adminUser, $entity]);
		}
	}

	/**
	 * Updated Licenses
	 *
	 * After the license count was updated
	 *
	 * @param \App\Event\Cake\Event\Event $event
	 * @param \App\Event\Cake\ORM\Entity $entity
	 * @param array $options
	 * @return void
	 */
	public function updatedLicenses(EventInterface $event, EntityInterface $entity, $options = []): void
	{
		$original = (int)$options['original_licenses'];
		$new = (int)$options['new_licenses'];

		$clientEmployees = $this->fetchTable('ClientEmployees');

		$licensedCount = $clientEmployees
			->find('active', [
				'skipTenantCheck' => true,
			])
			->where([
				'client_id' => $entity->id,
			])
			->count();

		Log::write('info', __(
			'Client #{0} ({1}) updated their licenses from {2} to {3}. Has {4} licensed employees.',
			$entity->id,
			$entity->name,
			$original,
			$new,
			$licensedCount
		), [
			'scope' => 'general',
		]);

		// Added Licenses
		if ($new > $original) {
			$difference = $new - $original;

			$employeesToActivate = $clientEmployees
				->find('inactive', [
					'skipTenantCheck' => true,
				])
				->where([
					'client_id' => $entity->id,
				])
				->limit($difference)
				->all();

			foreach ($employeesToActivate as $employee) {
				$employee->set('active', true);
				$clientEmployees->saveOrFail($employee);
			}
		}

		// Removed Licenses
		if ($new < $original && $licensedCount > $new) {
			$deficit = $licensedCount - $new;

			$employeesToInactivate = $clientEmployees
				->find('active', [
					'skipTenantCheck' => true,
				])
				->where([
					'client_id' => $entity->id,
				])
				->limit($deficit)
				->all();

			foreach ($employeesToInactivate as $employee) {
				$employee->set('active', false);
				$clientEmployees->saveOrFail($employee);
			}
		}
	}
}
