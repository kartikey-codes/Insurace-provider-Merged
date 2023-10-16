<?php

declare(strict_types=1);

namespace App\Event;

use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;
use Cake\ORM\Locator\LocatorAwareTrait;

class AgencyListener implements EventListenerInterface
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
			'Model.Agency.created' => 'afterCreated',
		];
	}

	/**
	 * After Created
	 *
	 * @param \App\Event\Cake\Event\Event $event
	 * @param \App\Event\Cake\ORM\Entity $entity
	 * @param array $options
	 * @return void
	 */
	public function afterCreated(EventInterface $event, EntityInterface $entity, $options = []): void
	{
		// Nothing Yet
	}
}
