<?php

/**
 * @author Logic Solutions
 */

declare(strict_types=1);

namespace App\Model\Behavior;

use App\Exception\TenancyViolationException;
use App\Lib\TenantUtility\TenantUtility;
use App\Lib\TenantUtility\VendorUtility;
use ArrayObject;
use Cake\Core\Configure;
use Cake\Database\Expression\ComparisonExpression;
use Cake\Database\ExpressionInterface;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\RulesChecker;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Multitenancy behavior
 */
class MultitenancyBehavior extends Behavior
{
	/**
	 * @var array
	 */
	protected array $allTableNames;

	/**
	 * @var string
	 */
	protected string $tenantTableName;

	/**
	 * @var string
	 */
	protected string $tenantForeignKeyName;

	/**
	 * @var string
	 */
	protected string $userTableName;

	/**
	 * @var array
	 */
	protected array $tablesDontNeedTenantKey;

	/**
	 * @inheritDoc
	 */
	protected $_defaultConfig = [];

	/**
	 * @var
	 */
	protected $_rule;
	protected $_fields;
	protected $_options;
	protected $rule;
	protected $_rules;

	/**
	 * Initialization
	 *
	 * @param array $config
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	public function initialize(array $config): void
	{
		parent::initialize($config);

		$this->allTableNames = Configure::read('Multitenancy.allTableNames');
		$this->tenantTableName = Configure::read('Multitenancy.tenantTableName');
		$this->tenantForeignKeyName = Configure::read('Multitenancy.tenantForeignKeyName');
		$this->userTableName = Configure::read('Multitenancy.userTableName');
		$this->tablesDontNeedTenantKey = Configure::read('Multitenancy.tablesDontNeedTenantKey');
	}

	/**
	 * Determine if behavior should be skipped
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param null|\ArrayObject $options
	 * @return bool
	 * @throws \InvalidArgumentException
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException
	 * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
	 */
	protected function skipBehavior(EventInterface $event, ?ArrayObject $options): bool
	{
		/** @var \Cake\ORM\Table */
		$table = $event->getSubject();
		$tableName = $table->getTable();

		// Updated to empty()
		if (!empty($options['skipTenantCheck']) && $options['skipTenantCheck']) {
			return true;
		}

		if (in_array($tableName, $this->tablesDontNeedTenantKey)) {
			Log::warning("MultitenancyBehavior: skip table $table");

			return true;
		}

		if (!in_array($tableName, $this->allTableNames)) {
			Log::warning("MultitenancyBehavior: skip table $table");

			return true;
		}

		if (VendorUtility::isVendorUser()) {
			//Log::warning("MultitenancyBehavior: skip case table $tableName for vendor user");
			return true;
		}

		return false;
	}

	/**
	 * Assert tenant ID is available in request for queries that do not skip tenant check
	 *
	 * @param mixed $tenantId
	 * @param mixed $table
	 * @return void
	 * @throws \App\Exception\TenancyViolationException
	 */
	protected function assertTenantId($tenantId, $table): void
	{
		// Added more descriptive message since this goes off all the time.
		if ($tenantId === null) {
			throw new TenancyViolationException("Asserted Tenant Id is null when querying table '{$table->getTable()}'. Ensure tenant ID is properly set or add skipTenantCheck option.");
		}
	}

	/**
	 * Need to check if a query is against user's token or username, need to skip tenancy finder if so
	 * because this query is used for user authentication
	 *
	 * @param \Cake\ORM\Table $table
	 * @param \Cake\ORM\Query $query
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	protected function isUserLookupQuery(Table $table, Query $query): bool
	{
		if ($table->getTable() !== $this->userTableName) {
			return false;
		}

		/** @var \Cake\Database\Expression\QueryExpression */
		$where = $query->clause('where');
		$isUserLookupQuery = false;
		$where->traverse(function (ExpressionInterface $expression) use (&$isUserLookupQuery) {
			if (
				$expression instanceof ComparisonExpression
				&& $expression->getOperator() === '='
				&& in_array($expression->getField(), ['Users.api_token', 'Users.email', 'Users.id', 'Owner.id', 'CreatedByUser.id', 'ModifiedByUser.id', 'Clients.owner_user_id'])
			) {
				//Log::debug("MultitenancyBehavior.isUserTokenQuery: ".print_r($expression, true));
				$isUserLookupQuery = true;
			}
		});

		return $isUserLookupQuery;
	}

	/**
	 * Return whether query validates tenant or not
	 *
	 * @param \Cake\ORM\Table $table
	 * @param \Cake\ORM\Query $query
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	protected function isTenantLookupQuery(Table $table, Query $query): bool
	{
		if ($table->getTable() !== $this->tenantTableName) {
			return false;
		}

		/** @var \Cake\Database\Expression\QueryExpression */
		$where = $query->clause('where');
		$isTenantLookupQuery = false;
		$where->traverse(function (ExpressionInterface $expression) use (&$isTenantLookupQuery) {
			if (
				$expression instanceof ComparisonExpression
				&& $expression->getOperator() === '='
				&& in_array($expression->getField(), ['Clients.name', 'Clients.email', 'Clients.owner_user_id'])
			) {
				//Log::debug("MultitenancyBehavior.isUserTokenQuery: ".print_r($expression, true));
				$isTenantLookupQuery = true;
			}
		});

		return $isTenantLookupQuery;
	}

	/**
	 * BeforeFind Event
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param \Cake\ORM\Query $query
	 * @param \ArrayObject $options
	 * @param mixed $primary
	 * @return void
	 * @throws \InvalidArgumentException
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException
	 * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
	 * @throws \App\Exception\TenancyViolationException
	 * @throws \Cake\Datasource\Exception\MissingDatasourceConfigException
	 * @throws \RuntimeException
	 * @throws \Cake\Database\Exception\DatabaseException
	 */
	public function beforeFind(EventInterface $event, Query $query, ArrayObject $options, $primary): void
	{
		if ($this->skipBehavior($event, $options)) {
			return;
		}

		/** @var \Cake\ORM\Table */
		$table = $event->getSubject();
		$tenantId = TenantUtility::getTenantIdFromRequest();

		if ($this->isUserLookupQuery($table, $query)) {
			return;
		}
		if ($this->isTenantLookupQuery($table, $query)) {
			return;
		}

		// Allow admins to skip tenant checking on associations
		if (!$primary && TenantUtility::isAdmin()) {
			return;
		}

		if ($table->getTable() === $this->tenantTableName) {
			if (TenantUtility::isAdmin()) {
				// Do nothing (admin can find all clients)
			} else {
				$this->assertTenantId($tenantId, $table);
				$query->where([$table->getAlias() . '.' . $table->getPrimaryKey() => $tenantId]);
			}
		} else { // For other tables, admin and non-admin have the same logic
			$this->assertTenantId($tenantId, $table);
			$query->where([$table->getAlias() . '.' . $this->tenantForeignKeyName => $tenantId]);
		}
	}

	/**
	 * BeforeSave Event
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param \Cake\Datasource\EntityInterface $entity
	 * @param \ArrayObject $options
	 * @return void
	 * @throws \InvalidArgumentException
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException
	 * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
	 * @throws \App\Exception\TenancyViolationException
	 * @throws \Cake\Datasource\Exception\MissingDatasourceConfigException
	 * @throws \RuntimeException
	 * @throws \Cake\Database\Exception\DatabaseException
	 */
	public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
	{
		if ($this->skipBehavior($event, $options)) {
			return;
		}

		/** @var \Cake\ORM\Table */
		$table = $event->getSubject();
		$tenantId = TenantUtility::getTenantIdFromRequest();

		if ($table->getTable() === $this->tenantTableName) {
			if (TenantUtility::isAdmin()) {
				// Do nothing (admin user can add/update client freely)
			} else {
				$this->assertTenantId($tenantId, $table);
				if ($entity->isNew()) {
					throw new TenancyViolationException('Only admin can create new client.');
				} else {
					$clientId = $entity->{$table->getPrimaryKey()};
					if ($clientId != $tenantId) {
						throw new TenancyViolationException("Tenant {$tenantId} cannot save another tenant {$clientId}'s data in client table.");
					}
				}
			}
		} else { // For other tables, admin and non-admin have the same logic
			$this->assertTenantId($tenantId, $table);
			if ($entity->isNew()) {
				$entity->{$this->tenantForeignKeyName} = $tenantId;
			} else {
				$entityClientId = $entity->{$this->tenantForeignKeyName};
				if ($entityClientId != $tenantId) {
					throw new TenancyViolationException("Tenant {$tenantId} cannot save another tenant ({$entityClientId})'s data in {$table->getTable()} table.");
				}
			}
		}
	}

	/**
	 * BeforeDelete Event
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param \Cake\Datasource\EntityInterface $entity
	 * @param \ArrayObject $options
	 * @return void
	 * @throws \InvalidArgumentException
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException
	 * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
	 * @throws \App\Exception\TenancyViolationException
	 * @throws \Cake\ORM\Exception\MissingEntityException
	 * @throws \Cake\Datasource\Exception\MissingDatasourceConfigException
	 * @throws \Cake\Database\Exception\DatabaseException
	 * @throws \RuntimeException
	 */
	public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
	{
		if ($this->skipBehavior($event, $options)) {
			return;
		}

		/** @var \Cake\ORM\Table */
		$table = $event->getSubject();
		$tenantId = TenantUtility::getTenantIdFromRequest();

		if ($table->getTable() === $this->tenantTableName) {
			if (TenantUtility::isAdmin()) {
				// Do nothing (admin user can delete client freely)
			} else {
				throw new TenancyViolationException('Only admin can delete client.');
			}
		} else { // For other tables, admin and non-admin have the same logic
			$this->assertTenantId($tenantId, $table);
			//$entityClientId = $entity->{$this->tenantForeignKeyName};
			$entityClientId = TenantUtility::getTenantIdFromEntity($table, $entity);
			if ($entityClientId != $tenantId) {
				throw new TenancyViolationException("Tenant {$tenantId} cannot delete another tenant ({$entityClientId})'s data in {$table->getTable()} table.");
			}
		}
	}

	/**
	 * Build Validation Rules
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param \Cake\Validation\Validator $validator
	 * @param mixed $name
	 * @return void
	 * @throws \InvalidArgumentException
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException
	 * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
	 */
	public function buildValidator(EventInterface $event, Validator $validator, $name): void
	{
		if ($this->skipBehavior($event, null)) {
			return;
		}

		$changeValidationRule = function () {
			if ($this->_rule === 'validateUnique') {
				$this->_rule = 'validateUniqueWithinTenant';
			}
		};

		/** @var \Cake\ORM\Table */
		$table = $event->getSubject();
		if ($table->getTable() !== $this->tenantTableName && $table->getTable() !== $this->userTableName) {
			// disable all unique validators since they wont' work with client id
			/** @var \Cake\Validation\ValidationSet $validationSet */
			foreach ($validator as $field => &$validationSet) {
				/** @var \Cake\Validation\ValidationRule $validationRule */
				foreach ($validationSet->rules() as $ruleName => &$validationRule) {
					if ($validationRule->get('rule') === 'validateUnique') {
						$changeValidationRule->call($validationRule);
						//$validationSet->remove($ruleName);
						break;
					}
				}
			}
		}
	}

	/**
	 * Add alternative validateUnique to Table which takens client_id into account
	 *
	 * @param mixed $value
	 * @param array $options
	 * @param array $context
	 * @return bool
	 */
	public function validateUniqueWithinTenant($value, array $options, ?array $context = null): bool
	{
		$tenantId = TenantUtility::getTenantIdFromRequest();

		if ($context === null) {
			$context = $options;
		}

		$data = $context['data'];
		if ($tenantId !== null) {
			$data[$this->tenantForeignKeyName] = $tenantId;
		}
		$entity = new Entity(
			$data,
			[
				'useSetters' => false,
				'markNew' => $context['newRecord'],
				'source' => $this->table()->getRegistryAlias(),
			]
		);

		$fieldNames = [$context['field']];
		if ($tenantId !== null) {
			array_push($fieldNames, $this->tenantForeignKeyName);
		}
		$fields = array_merge(
			$fieldNames,
			isset($options['scope']) ? (array)$options['scope'] : []
		);

		$values = $entity->extract($fields);
		foreach ($values as $field) {
			if ($field !== null && !is_scalar($field)) {
				return false;
			}
		}
		$class = Table::IS_UNIQUE_CLASS;
		/** @var \Cake\ORM\Rule\IsUnique $rule */
		$rule = new $class($fields, $options);

		return $rule($entity, ['repository' => $this->table()]);
	}

	/**
	 * Build Application Rules
	 *
	 * @param \Cake\Event\EventInterface $event
	 * @param \Cake\Datasource\RulesChecker $rules
	 * @return void
	 * @throws \InvalidArgumentException
	 * @throws \Cake\Datasource\Exception\RecordNotFoundException
	 * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException
	 */
	public function buildRules(EventInterface $event, RulesChecker $rules): void
	{
		if ($this->skipBehavior($event, null)) {
			return;
		}

		$changeRulesChecker = function (Table $table) {
			$changeRuleInvoker = function (Table $table) {
				$createIsUniqueRuleWithClientId = function (Table $table) {
					$fields = $this->_fields;
					$tenantForeignKeyName = Configure::read('Multitenancy.tenantForeignKeyName');
					if (in_array($tenantForeignKeyName, $fields)) {
						$fields[] = $tenantForeignKeyName;
					}

					return new \Cake\ORM\Rule\IsUnique($fields, $this->_options);
				};

				if ($this->rule instanceof \Cake\ORM\Rule\IsUnique) {
					$this->rule = $createIsUniqueRuleWithClientId->call($this->rule, $table);
				}
			};

			foreach ($this->_rules as &$ruleInvoker) {
				$changeRuleInvoker->call($ruleInvoker, $table);
			}
		};

		/** @var \Cake\ORM\Table */
		$table = $event->getSubject();
		if ($table->getTable() !== $this->tenantTableName && $table->getTable() !== $this->userTableName) {
			$changeRulesChecker->call($rules, $table);
		}
	}
}
