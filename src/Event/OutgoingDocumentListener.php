<?php

declare(strict_types=1);

namespace App\Event;

use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;
use Cake\ORM\Locator\LocatorAwareTrait;

class OutgoingDocumentListener implements EventListenerInterface
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
			'Model.OutgoingDocument.created'  	=> 'afterCreated',
			'Model.OutgoingDocument.modified' 	=> 'afterModified',
		];
	}

	/**
	 * After Created
	 *
	 * A new fax was created in the system, whether it was imported or
	 * manually uploaded.
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
	 * A fax was updated in the database.
	 *
	 * @return void
	 */
	public function afterModified(EventInterface $event, EntityInterface $entity, $options = []): void
	{
		// Nothing here yet...
	}
}
