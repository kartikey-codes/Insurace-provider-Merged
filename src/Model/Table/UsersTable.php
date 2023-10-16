<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Event\UserListener;
use App\Lib\PasswordUtility\PasswordUtility;
use App\Model\Entity\User;
use App\Model\Filter\UsersCollection;
use ArrayObject;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\I18n\FrozenTime;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Entity;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use RuntimeException;

/**
 * Users Model
 */
class UsersTable extends Table
{
	use LocatorAwareTrait;
	use MailerAwareTrait;

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config): void
	{
		parent::initialize($config);

		$this->setTable('users');

		$this->setPrimaryKey('id');

		$this->setDisplayField('full_name');

		/**
		 * -------------------------------------
		 * Associations
		 * -------------------------------------
		 */

		$this->belongsTo('Clients', [
			'foreignKey' => 'client_id',
		]);

		$this->belongsTo('Vendors', [
			'foreignKey' => 'vendor_id',
		]);

		// Belongs To Many
		$this->belongsToMany('Roles', [
			'through' => 'UsersRoles',
			'foreignKey' => 'user_id',
			'targetForeignKey' => 'role_id',
		]);

		// @todo Fix this
		// $this->belongsToMany('Permissions', [
		// ]);

		// Has Many

		// $this->hasMany('FailedLogins', [
		// 	'foreignKey' => 'user_id',
		// ]);

		$this->hasMany('Appeals', [
			'foreignKey' => 'assigned_to',
		]);

		$this->hasMany('UserLogins', [
			'foreignKey' => 'user_id',
		]);

		/**
		 * -------------------------------------
		 * Behaviors
		 * -------------------------------------
		 */
		// Active
		$this->addBehavior('Active', [
			'field' => $this->aliasField('active'),
		]);

		// Cache Clearing
		$this->addBehavior('CacheClear', [
			'key' => 'Users',
			'stores' => ['all', 'active'],
		]);

		// Enhanced Finder
		$this->addBehavior('EnhancedFinder', [
			'fullContain' => [
				'Roles' => [
					'finder' => 'ordered',
					'Permissions'
				]
			],
			'limitedFields' => [
				'id',
				'first_name',
				'middle_name',
				'last_name',
				'email',
				'active',
				'admin',
				'last_seen',
			],
		]);

		// Last Login
		$this->addBehavior('LastLogin', [
			'primaryKey' => 'id',
			'timestampField' => 'last_login',
			'ipField' => 'last_login_ip',
		]);

		// Lock Out
		$this->addBehavior('LockOut', [
			'primaryKey' => 'id',
			'lockField' => 'locked',
			'expirationField' => 'lock_expiration',
			'duration' => Configure::readOrFail('Login.lockoutDuration'),
			'maxAttempts' => Configure::readOrFail('Login.lockoutAttempts'),
		]);

		// Timestamp Behavior
		$this->addBehavior('Timestamp', [
			'events' => [
				'Model.beforeSave' => [
					'created' => 'new',
					'modified' => 'always',
				],
			],
		]);

		// Soft Delete
		$this->addBehavior('Muffin/Trash.Trash', [
			'field' => 'deleted',
		]);

		// Orderable
		$this->addBehavior('Orderable', [
			'default' => [
				$this->aliasField('first_name') => 'asc',
				$this->aliasField('last_name') => 'asc',
			],
		]);

		// Online
		$this->addBehavior('Online', [
			'fields' => [
				'primary_key' => 'id',
				'last_seen' => 'last_seen',
			],
			'threshold' => '-5 minutes',
		]);

		// Password Behavior
		$this->addBehavior('PasswordReset', [
			'forgotPasswordEvent' => 'Model.User.forgot_password',
		]);

		// Random Behavior
		$this->addBehavior('Random');

		// Search Plugin
		$this->addBehavior('Search.Search', [
			'collectionClass' => UsersCollection::class,
		]);

		/**
		 * -------------------------------------
		 * Event Listener
		 * -------------------------------------
		 */
		// Event Manager
		$this->getEventManager()->on(
			new UserListener()
		);

		$this->getEventManager()->on($this->getMailer('User'));
	}

	/**
	 * Logic to execute before saving
	 */
	public function beforeSave(EventInterface $event, User $entity, ArrayObject $options): void
	{
		// Generate API token
		if ($entity->isNew() || empty($entity->api_token)) {
			$entity->set('api_token', $entity->generateApiToken());
		}

		// Clear phone if masked input was left empty
		if (!empty($entity->get('phone')) && trim($entity->get('phone')) == '(') {
			$entity->set('phone', null);
		}
	}

	/**
	 * After Save
	 *
	 * @return void
	 */
	public function afterSave(EventInterface $event, Entity $entity, ArrayObject $options): void
	{
		// Clear cache or anything else
	}

	/**
	 * After Save Commit
	 *
	 * @return void
	 */
	public function afterSaveCommit(EventInterface $event, Entity $entity, ArrayObject $options): void
	{
		// New Users
		if ($entity->isNew()) {
			$event = new Event('Model.User.created', $this, ['user' => $entity]);
			$this->getEventManager()->dispatch($event);
		} else {
			$event = new Event('Model.User.modified', $this, ['user' => $entity]);
			$this->getEventManager()->dispatch($event);
		}
	}

	/**
	 * Default validation rules.
	 *
	 * @param \Cake\Validation\Validator $validator Validator instance.
	 * @return \Cake\Validation\Validator
	 */
	public function validationDefault(Validator $validator): Validator
	{
		$validator
			->add('id', 'valid', ['rule' => 'numeric'])
			->allowEmptyString('id', 'create');

		$validator
			->add('email', 'valid', [
				'rule' => 'email',
				'message' => __('Must be a valid email address'),
			])
			->add('email', [
				'unique' => [
					'rule' => 'validateUnique',
					'provider' => 'table',
					'message' => __('This email address is already registered.'),
				],
			])
			//->requirePresence('email', 'create')
			->notEmptyString('email');

		/**
		 * Password Related Validation
		 *
		 * @note When registering a user account, the UserRegisterForm validation is used
		 */
		$minLength = PasswordUtility::getMinimumLength();

		$validator
			->add('current_password', 'currentPasswordValid', [
				'rule' => function ($value, $context) {
					$user = $this->get($context['data']['id']);
					if ($user) {
						if ((new DefaultPasswordHasher())->check($value, $user->password)) {
							return true;
						}
					}

					return false;
				},
				'message' => 'The current password does not match the password on file.',
			])
			->notEmptyString('current_password');

		$validator
			//->requirePresence('Password', 'create')
			->notEmptyString('password', __('Password cannot be empty.'))
			->add('password', [
				'minLength' => [
					'rule' => ['minLength', $minLength],
					'message' => __('Passwords need to be at least {0} characters long.', $minLength),
				],
			])
			->add('password', 'isCommonlyUsed', [
				'rule' => function ($value, $context) {
					return PasswordUtility::isCommonlyUsed($value) ? false : true;
				},
				'message' => __('This password is commonly used and is insecure. Please choose a different password.'),
			]);

		$validator
			->add(
				'confirm_password',
				'compareWith',
				[
					'rule' => ['compareWith', 'password'],
					'message' => __('Password and confirm password must match.'),
				]
			)
			//->requirePresence('confirm_password', 'create')
			->notEmptyString('confirm_password', __('Password cannot be empty.'))
			->add('confirm_password', [
				'minLength' => [
					'rule' => ['minLength', $minLength],
					'message' => __('Passwords need to be at least {0} characters long.', $minLength),
				],
			]);

		$validator
			->allowEmptyString('password_reset_token');

		$validator
			->allowEmptyString('password_reset_token_expiration', 'valid', ['rule' => 'datetime']);

		/**
		 * End Password Related Validation
		 */

		$validator
			//->requirePresence('first_name', 'create')
			->notEmptyString('first_name');

		$validator
			//->requirePresence('first_name', 'create')
			->allowEmptyString('middle_name');

		$validator
			//->requirePresence('last_name', 'create')
			->notEmptyString('last_name');

		$validator
			// ->add('phone', 'validFormat', [
			// 	'rule' => array('custom', '/^\(\d{3}\) \d{3}-\d{4}$/'),
			// 	'message' => __('Please enter a phone number in (###) ###-#### format.'),
			// ])
			->allowEmptyString('phone');

		$validator
			->add('active', 'valid', ['rule' => 'boolean'])
			//->requirePresence('active', 'create')
			->allowEmptyString('active');

		$validator
			->add('admin', 'valid', ['rule' => 'boolean'])
			//->requirePresence('admin', 'create')
			->allowEmptyString('admin');

		$validator
			->add('client_admin', 'valid', ['rule' => 'boolean'])
			//->requirePresence('admin', 'create')
			->allowEmptyString('client_admin');

		// $validator
		// 	->notEmptyString('Timezone');

		/**
		 * Last Login
		 */

		$validator
			->allowEmptyString('last_login', 'valid', ['rule' => 'datetime']);

		$validator
			->allowEmptyString('last_login_ip');

		return $validator;
	}

	/**
	 * Returns a rules checker object that will be used for validating
	 * application integrity.
	 *
	 * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
	 * @return \Cake\ORM\RulesChecker
	 */
	public function buildRules(RulesChecker $rules): RulesChecker
	{
		$rules->add($rules->isUnique(['email']));

		// Cannot delete if the user is a global admin (staff)
		$rules->addDelete(function ($entity, $options) {
			return empty($entity->isAdmin());
		}, 'cannotDeleteAdmins', [
			'errorField' => 'admin',
			'message' => 'Administrators must be demoted before they can be deleted.',
		]);

		// Cannot delete if the user is an admin of their client organization
		$rules->addDelete(function ($entity, $options) {
			return empty($entity->isClientAdmin());
		}, 'cannotDeleteClientAdmins', [
			'errorField' => 'client_admin',
			'message' => 'Organization administrators must be demoted before they can be deleted.',
		]);

		return $rules;
	}

	/**
	 * Finder For AuthComponent
	 * Used for allowing or denying a login attempt
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findAuth(Query $query, array $options): Query
	{
		return $query
			->find('active')

			->where(function (QueryExpression $exp, Query $q) {
				$lockExpired = $exp
					->eq($this->aliasField('locked'), true)
					->lte($this->aliasField('lock_expiration'), new FrozenTime());

				return $exp
					->or([$lockExpired])
					->eq($this->aliasField('locked'), false)
					->isNull($this->aliasField('locked'));
			});
	}

	/**
	 * Return if user is global admin (staff) or not
	 *
	 * @param int $userId
	 * @return bool
	 */
	public function isAdmin(int $userId): bool
	{
		/** @var \App\Model\Entity\User */
		$user = $this->get($userId);

		return $user->isAdmin();
	}

	/**
	 * Return if user is admin of their client organization (tenant) or not
	 *
	 * @param int $userId
	 * @return bool
	 */
	public function isClientAdmin(int $userId): bool
	{
		/** @var \App\Model\Entity\User */
		$user = $this->get($userId);

		return $user->isClientAdmin();
	}

	/**
	 * Find Global Admins (staff)
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findAdmins(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->eq($this->aliasField('admin'), true);
		});
	}

	/**
	 * Find Client Organization Admins (tenant)
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findClientAdmins(Query $query, array $options): Query
	{
		return $query->where(function (QueryExpression $exp, Query $q) {
			return $exp->eq($this->aliasField('client_admin'), true);
		});
	}

	/**
	 * Find all new users: those not belong to any vendor or client
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findNew(Query $query, array $options): Query
	{
		return $query
			->find('active')
			->where(function (QueryExpression $exp, Query $q) {
				return $exp
					->isNull($this->aliasField('client_id'));
			});
	}

	/**
	 * Find active admin users
	 *
	 * @return \Cake\Datasource\ResultSetInterface
	 */
	public function getAllActiveAdmins(): ResultSetInterface
	{
		return $this->find('all', ['skipTenantCheck' => true])
			->find('admins')
			->find('active')
			->all();
	}

	/**
	 * Find Client Users by Client ID
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findClientUsersById(Query $query, array $options): Query
	{
		if (empty($options['id'])) {
			throw new RuntimeException(__('A client ID must be provided to find users.'));
		}

		return $query->where([
			$this->aliasField('client_id') => $options['id'],
		]);
	}

	/**
	 * Find Vendor Users by Vendor ID
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findVendorUsersById(Query $query, array $options): Query
	{
		if (empty($options['id'])) {
			throw new RuntimeException(__('A vendor ID must be provided to find users.'));
		}

		return $query->where([
			$this->aliasField('vendor_id') => $options['id'],
		]);
	}

	/**
	 * Find user ID by email (if exists)
	 *
	 * @param string $email
	 * @param array $options
	 * @return int|null
	 */
	public function getIdByEmail(string $email, array $options = []): ?int
	{
		$result = $this->find('all', [
			'skipTenantCheck' => true,
			'skipVendorCheck' => true,
		])
			->select(['id'])
			->where([
				'email' => $email,
			])
			->first();

		if (empty($result)) {
			return null;
		}

		return $result->id;
	}
}
