<?php

declare(strict_types=1);

namespace App\Event;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;

class ModelInitializationListener implements EventListenerInterface
{
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
			'Model.initialize' => 'afterModelInitialize',
		];
	}

	/**
	 * After a model is initialized
	 *
	 * @return void
	 */
	public function afterModelInitialize(EventInterface $event): void
	{
		/** @var \Cake\ORM\Table */
		$table = $event->getSubject();

		if (in_array($table->getTable(), Configure::read('Multitenancy.tablesDontNeedTenantKey'))) {
			//Log::debug("ModelInitializationListener.afterModelInitialize: skip table {$table->getTable()} for Multitenancy behavior");
		} elseif (in_array($table->getTable(), Configure::read('Multitenancy.allTableNames'))) {
			$table->addBehavior('Multitenancy');
			$table->addBehavior('AuditStash.AuditLog', [
				'blacklist' => ['created', 'created_by', 'modified', 'modified_by'],
			]);
		}

		if (in_array($table->getTable(), Configure::read('Vendor.allTableNames'))) {
			$table->addBehavior('Vendor');
		}
	}
}
