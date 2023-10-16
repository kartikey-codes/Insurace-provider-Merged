<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\ClientListener;
use App\Form\ClientRegisterForm;
use App\Lib\LocationUtility\LocationUtility;
use App\Lib\NpiUtility\NpiOrganizationResult;
use App\Model\Entity\Client;
use App\Model\Entity\User;
use App\Model\Table\ClientsTable;
use App\Model\Table\UsersTable;
use App\Service\NpiServiceInterface;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Routing\Router;

/**
 * Registration Controller
 *
 * Allows registration of a client.
 *
 * @property \App\Controller\Component\LogComponent $Log
 */
class ClientRegistrationController extends AppController
{
	use LocatorAwareTrait;

	/** @var string */
	public const CLIENT_REGISTER_ERROR = 'Please check for errors and try again.';

	/** @var string */
	public const CLIENT_REGISTER_SUCCESS = 'Your organization was registered!';

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
		$this->getEventManager()->on(new ClientListener());
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
	 * New Client Registration
	 *
	 * @return void
	 */
	public function index(NpiServiceInterface $npiService): void
	{
		$form = new ClientRegisterForm();
		$states = LocationUtility::states();
		$npiLookupUrl = Router::url(['_name' => 'registerNpiLookup'], true);

		if ($this->request->is('post')) {
			if ($form->validate($this->request->getData())) {
				$this->registerClient($this->request->getData(), $npiService);
				$this->Flash->success(__(self::CLIENT_REGISTER_SUCCESS));
				$this->redirect(['_name' => 'clientSubscription']);
			} else {
				$this->Flash->error(__(self::CLIENT_REGISTER_ERROR));
			}
		}

		$this->set(compact(
			'form',
			'states',
			'npiLookupUrl'
		));
	}

	/**
	 * Get the organization details by NPI number provided
	 * @param NpiServiceInterface $npiService
	 * @param string $npiNumber
	 * @return NpiOrganizationResult
	 */
	private function NpiLookupByNumber(NpiServiceInterface $npiService, string $npiNumber): NpiOrganizationResult
	{
		// Get NPI information
		$npiNumber = intval($npiNumber);

		/** @var \App\Lib\NpiUtility\NpiOrganizationResult[] */
		$results = $npiService->searchOrganizationByNumber($npiNumber);

		return $results[0];
	}

	/**
	 * Filter the primary location address from the list of addresses
	 * @param array $addresses
	 * @return array
	 */
	private function NpiGetPrimaryLocationAddress(array $addresses): array
	{
		// Get Location Addresses
		$locationAddresses = array_filter($addresses, function ($address) {
			return trim($address['address_purpose']) == 'LOCATION';
		});

		// Reindex array from 0 and get first address
		$locationAddress = array_values($locationAddresses)[0];

		return $locationAddress;
	}

	/**
	 * Called with form data after a client registration is valid
	 *
	 * @param array $data
	 * @return \App\Model\Entity\Client
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	private function registerClient(array $data, NpiServiceInterface $npiService): Client
	{
		// Load models needed
		$users = $this->fetchTable('Users');
		$clients = $this->fetchTable('Clients');

		// Get user who created this client
		$identity = $this->Authentication->getIdentity();

		/** @var \App\Model\Entity\User|null */
		$user = $identity->getOriginalData();

		/** @var \App\Lib\NpiUtility\NpiOrganizationResult */
		$npiResult = $this->NpiLookupByNumber($npiService, $data['npi_number']);

		/** @var array */
		$locationAddress = $this->NpiGetPrimaryLocationAddress($npiResult->addresses);

		// Get Primary Taxonomy
		$primaryTaxonomy = null;
		foreach ($npiResult->taxonomies as $taxonomy) {
			if ($taxonomy['primary'] == true) {
				$primaryTaxonomy = $taxonomy;
				break;
			}
		}

		// Terms of Service Date
		$tosDate = Configure::read('TermsOfService.clientDate');

		/**
		 * @todo Figure out why skipTenantCheck isn't registering when validating this entity.
		 * Somewhere the $options are not passed when checking isUnique and it throws tenancy errors.
		 */
		$clients->removeBehavior('Multitenancy');

		// Populate entity from request data and NPI data
		$client = $clients->newEntity([
			// Name and Type
			'name' => $data['name'],
			'client_type_id' => !empty($data['client_type_id']) ? $data['client_type_id'] : null,
			'active' => true,
			'email' => $data['email'],
			'phone' => $data['phone'],
			'fax' => null,

			// Address
			'street_address_1' => $locationAddress['address_1'] ?? null,
			'street_address_2' => $locationAddress['address_2'] ?? null,
			'city' => $locationAddress['city'] ?? null,
			'state' => $locationAddress['state'] ?? null,
			'zip' => $locationAddress['postal_code'] ?? null,

			// Contact Info
			'contact_first_name' => $npiResult->authorized_official_first_name ?? null,
			'contact_last_name' => $npiResult->authorized_official_last_name ?? null,
			'contact_email' => $data['email'],
			'contact_phone' => $locationAddress['telephone_number'] ?? null,
			'contact_fax' => $locationAddress['fax_number'] ?? null,

			// Additional
			'npi_number' => $data['npi_number'],
			'licenses' => $data['licenses'] ?? 1,
			'status' => 'Active',
			'tos_date' => $tosDate,
			'primary_taxonomy' => $primaryTaxonomy['code'] ?? "",

			// Owner
			'owner_user_id' => $user->getIdentifier(),
		]);

		$clients->saveOrFail($client, [
			'skipTenantCheck' => true,
		]);

		// Update user with new Client ID
		$user->set('client_id', $client->id);

		// User creating client is default granted admin status of their client
		$user->set('client_admin', true);

		$users->saveOrFail($user, [
			'skipTenantCheck' => true,
		]);

		// Get back full association for event
		$client = $clients->get($client->id, [
			'skipTenantCheck' => true,
			'contain' => [
				'Owner',
			],
		]);

		$this->getEventManager()->dispatch(
			new Event('Model.Client.registered', $this, [
				$client
			])
		);

		return $client;
	}
}
