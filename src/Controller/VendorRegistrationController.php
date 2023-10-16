<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\VendorListener;
use App\Form\VendorRegisterForm;
use App\Lib\LocationUtility\LocationUtility;
use App\Model\Entity\Vendor;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Vendor Registration Controller
 *
 * Allows registration of a vendor group after user registration
 *
 * @property \App\Controller\Component\LogComponent $Log
 */
class VendorRegistrationController extends AppController
{
	use LocatorAwareTrait;

	/** @var string */
	public const VENDOR_REGISTER_ERROR = 'Please check for errors and try again.';

	/** @var string */
	public const VENDOR_REGISTER_SUCCESS = 'Your organization was registered! Your temporary password was emailed to you.';

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

		// App 'Log' Component
		$this->loadComponent('Log');

		// Request Handler
		$this->loadComponent('RequestHandler');

		// Events
		$this->getEventManager()->on(new VendorListener());
	}

	/**
	 * Before filter callback.
	 * Executes before every request.
	 *
	 * @param \Cake\Event\Event $event The beforeFilter event.
	 * @return void
	 */
	public function beforeFilter(EventInterface $event): void
	{
		parent::beforeFilter($event);
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

		$this->viewBuilder()->setLayout('login');
	}

	/**
	 * New Vendor Registration
	 *
	 * @return void
	 */
	public function index(): void
	{
		$form = new VendorRegisterForm();
		$states = LocationUtility::states();

		if ($this->request->is('post')) {
			if ($form->execute($this->request->getData())) {
				$vendor = $this->registerVendor($this->request->getData());
				$this->Flash->success(__(self::VENDOR_REGISTER_SUCCESS));
				$this->redirect(['_name' => 'redirector']);
			} else {
				$this->Flash->error(__(self::VENDOR_REGISTER_ERROR));
			}
		}

		$this->set(compact(
			'form',
			'states'
		));
	}

	/**
	 * Called with form data after a vendor registration is valid and user is created
	 *
	 * @param array $data
	 * @return \App\Model\Entity\Vendor
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	private function registerVendor(array $data): Vendor
	{
		// Load models needed
		$users = $this->fetchTable('Users');
		$vendors = $this->fetchTable('Vendors');

		// Get user who created this client
		$identity = $this->Authentication->getIdentity();

		/**
		 * @var ?\App\Model\Entity\User $user
		 */
		$user = $identity->getOriginalData();

		// Terms of Service Date
		$tosDate = Configure::read('TermsOfService.vendorDate');

		/**
		 * @todo Figure out why skipTenantCheck isn't registering when validating this entity.
		 * Somewhere the $options are not passed when checking isUnique and it throws tenancy errors.
		 */

		if ($vendors->hasBehavior('Multitenancy')) {
			$vendors->removeBehavior('Multitenancy');
		}

		$vendor = $vendors->newEntity([
			'name' => $data['name'],
			'active' => true,
			'street_address_1' => $data['street_address_1'],
			'street_address_2' => $data['street_address_2'],
			'city' => $data['city'],
			'state' => $data['state'],
			'zip' => $data['zip'],
			'phone' => $data['phone'],
			'owner_user_id' => $user->id,
			'tos_date' => $tosDate,
		]);

		$vendors->saveOrFail($vendor, [
			'skipTenantCheck' => true,
			'skipVendorCheck' => true,
		]);

		$user->set('vendor_id', $vendor->id);
		$user = $users->saveOrFail($user, [
			'skipTenantCheck' => true,
			'skipVendorCheck' => true,
		]);

		$vendor = $vendors->get($vendor->id, [
			'skipTenantCheck' => true,
			'skipVendorCheck' => true,
			'contain' => [
				'Owner',
			],
		]);

		$this->getEventManager()->dispatch(
			new Event('Model.Vendor.registered', $this, [
				$vendor,
			])
		);

		return $vendor;
	}
}
