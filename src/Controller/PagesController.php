<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Pages Controller
 *
 * Used for static pages
 */
class PagesController extends AppController
{
	/**
	 * Initialization hook method.
	 *
	 * Use this method to add common initialization code like loading components.
	 *
	 * @return void
	 */
	public function initialize(): void
	{
		parent::initialize();

		$this->Authentication->allowUnauthenticated([
			'clientTerms',
			'vendorTerms',
		]);
	}

	/**
	 * Before render callback.
	 * Executes before the view is rendered
	 *
	 * @param \Cake\Event\Event $event The beforeRender event.
	 * @return void
	 */
	public function beforeRender(EventInterface $event): void
	{
		parent::beforeRender($event);

		$this->viewBuilder()
			->setLayout('login');
	}

	/**
	 * Client Terms & Conditions
	 *
	 * @return void
	 */
	public function clientTerms(): void
	{
	}

	/**
	 * Vendor Terms & Conditions
	 *
	 * @return void
	 */
	public function vendorTerms(): void
	{
	}
}
