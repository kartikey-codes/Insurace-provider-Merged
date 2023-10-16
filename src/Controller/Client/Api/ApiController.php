<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Model\Entity\User;
use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotImplementedException;

/**
 * API Controller
 *
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \Authorization\Controller\Component\AuthorizationComponent $Authorization
 */
abstract class ApiController extends Controller
{
	/**
	 * The primary key field on the User model
	 *
	 * @var string
	 */
	public string $userPrimaryKey = 'id';

	/**
	 * The unique username field on the User model for logging in
	 *
	 * @var string
	 */
	public string $userNameField = 'email';

	/**
	 * The hashed password field on the User model
	 *
	 * @var string
	 */
	public string $passwordField = 'password';

	/**
	 * The confirmed password field (not on the User model)
	 *
	 * @var string
	 */
	public string $confirmPasswordField = 'confirm_password';

	/**
	 * The field on the User model that indicates a password change is required
	 *
	 * @var string
	 */
	public string $forcePasswordChangeField = 'force_change_password';

	/**
	 * The unique API token field on the User model
	 *
	 * @var string
	 */
	public string $apiTokenField = 'api_token';

	/**
	 * Reference to the current user
	 *
	 * @var null|\App\Model\Entity\User
	 */
	public ?User $currentUser;

	/**
	 * Headers for CSV extract. Should be provided by extending controller class.
	 *
	 * @var array
	 */
	public array $csvHeaders = [];

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

		// Authentication Plugin
		$this->loadComponent('Authentication.Authentication', [
			'loginRedirect' => false,
			'logoutRedirect' => false,
		]);

		// Authorization Plugin
		$this->loadComponent('Authorization.Authorization');

		/**
		 * Get reference to the current authenticated user
		 */
		/** @var IdentityInterface */
		$identity = $this->Authentication->getIdentity();

		/** @var ?\App\Model\Entity\User */
		$this->currentUser = !empty($identity) ? $identity->getOriginalData() : null;

		// Core 'Paginator' component
		// @deprecated
		// $this->loadComponent('Paginator');

		// Core 'RequestHandler' component
		$this->loadComponent('RequestHandler');

		// App 'Permission' component
		$this->loadComponent('Permission', [
			'user' => $this->currentUser,
		]);

		// App 'ApiError' component
		$this->loadComponent('ApiError');

		// App 'Log' Component
		$this->loadComponent('Log');

		// App 'User Activity' Component
		$this->loadComponent('UserActivity', [
			'modelName' => 'Users',
			'primaryKey' => $this->userPrimaryKey,
		]);

		// Force json responses
		$this->RequestHandler->renderAs($this, 'json');

		$this->setResponse(
			$this->getResponse()->withType('application/json')
		);

		$this->viewBuilder()->setOption('serialize', true);
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
	 *
	 * @param \Cake\Event\Event $event The beforeRender event.
	 * @return void
	 */
	public function beforeRender(EventInterface $event): void
	{
		parent::beforeRender($event);

		if ($this->getRequest()->getQuery('download', false)) {
			// Force CSV responses
			$this->RequestHandler->renderAs($this, 'csv');

			$this->viewBuilder()
				->setClassName('CsvView.Csv')
				->setTemplate('index')
				->setOption('serialize', 'data')
				->setOption('extract', $this->getCsvExtract())
				->setOption('header', $this->csvHeaders);

			$this->setResponse(
				$this->getResponse()->withType('text/csv')
			);
		}
	}

	/**
	 * Get the pagination data for the default table associated
	 * with this controller.
	 *
	 * @return array|null
	 */
	protected function getDefaultTablePagination(): ?array
	{
		return $this->getRequest()->getAttribute('paging')[$this->defaultTable];
	}

	/**
	 * Paginate with the search filter, which is used all over the place
	 * and return a common set of variables in the response (results & pagination metadata)
	 *
	 * @param \Cake\ORM\Table|\Cake\ORM\Query|string|null $object Table to paginate
	 * (e.g: Table instance, 'TableName' or a Query object)
	 * @return void
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
	protected function crudIndex(mixed $object = null): void
	{
		$this->set([
			// Results
			'data' => $this->paginate($object, [
				'search' => $this->getRequest()->getQuery(),
			]),
			// Pagination Metadata
			'pagination' => $this->getDefaultTablePagination(),
		]);
	}

	/**
	 * Get the extraction rules for CSV formatting.
	 * Should be provided by extending controller class.
	 *
	 * @return array
	 */
	protected function getCsvExtract(): array
	{
		return [];
	}
}
