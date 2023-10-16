<?php

/**
 * @author Logic Solutions
 */

declare(strict_types=1);

namespace App\Model\Behavior;

use App\Exception\VendorViolationException;
use App\Lib\TenantUtility\TenantUtility;
use App\Lib\TenantUtility\VendorUtility;
use ArrayObject;
use Cake\Core\Configure;
use Cake\Database\Expression\ComparisonExpression;
use Cake\Database\ExpressionInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use Cake\ORM\Behavior;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;
use Cake\ORM\Table;

/**
 * Vendor behavior
 */
class VendorBehavior extends Behavior
{
	use LocatorAwareTrait;

	protected $allTableNames;
	protected $noAccessTableNames;
	protected $readonlyTableNames;
	protected $vendorTableName;
	protected $userTableName;

	/**
	 * Default config
	 *
	 * @var array
	 */
	protected $_defaultConfig = [];

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

		$this->allTableNames = Configure::read('Vendor.allTableNames');
		$this->noAccessTableNames = Configure::read('Vendor.noAccessTableNames');
		$this->readonlyTableNames = Configure::read('Vendor.readonlyTableNames');
		$this->vendorTableName = Configure::read('Vendor.vendorTableName');
		$this->userTableName = Configure::read('Vendor.userTableName');
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
		if ($options && isset($options['skipVendorCheck']) && $options['skipVendorCheck'] === true) {
			return true;
		}

		if (!VendorUtility::isVendorUser()) {
			return true; // this behavior only applies to vendor users
		}

		/** @var \Cake\ORM\Table */
		$table = $event->getSubject();
		$tableName = $table->getTable();

		if (!in_array($tableName, $this->allTableNames)) {
			Log::warning("VendorBehavior: skip table $tableName");

			return true;
		}

		return false;
	}

	/**
	 * Assert vendor ID is available in request for queries that do not skip vendor check
	 *
	 * @param mixed $vendorId
	 * @param mixed $table
	 * @return void
	 * @throws \App\Exception\VendorViolationException
	 */
	protected function assertVendorId($vendorId, $table): void
	{
		// Added more descriptive message since this goes off all the time.
		if ($vendorId === null) {
			throw new VendorViolationException("Asserted vendor Id is null when querying table {$table->getTable()}. Ensure logged in user is a vendor user.");
		}
	}

	/**
	 * Need to check if a query is against user's token or username, need to skip vendor finder if so
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
				&& in_array($expression->getField(), ['Users.api_token', 'Users.email', 'Users.id', 'Owner.id'])
			) {
				//Log::debug("VendorBehavior.isUserTokenQuery: ".print_r($expression, true));
				$isUserLookupQuery = true;
			}
		});

		return $isUserLookupQuery;
	}

	/**
	 * Return whether query validates vendor or not
	 *
	 * @param \Cake\ORM\Table $table
	 * @param \Cake\ORM\Query $query
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	protected function isVendorLookupQuery(Table $table, Query $query): bool
	{
		if ($table->getTable() !== $this->vendorTableName) {
			return false;
		}

		/** @var \Cake\Database\Expression\QueryExpression */
		$where = $query->clause('where');
		$isVendorLookupQuery = false;
		$where->traverse(function (ExpressionInterface $expression) use (&$isVendorLookupQuery) {
			if (
				$expression instanceof ComparisonExpression
				&& $expression->getOperator() === '='
				&& in_array($expression->getField(), ['Vendors.name'])
			) {
				//Log::debug("VendorBehavior.isUserTokenQuery: ".print_r($expression, true));
				$isVendorLookupQuery = true;
			}
		});

		return $isVendorLookupQuery;
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
	 * @throws \App\Exception\VendorViolationException
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
		$tableName = $table->getTable();
		$vendorId = VendorUtility::getVendorIdFromRequest();

		if ($this->isUserLookupQuery($table, $query)) {
			return;
		}
		if ($this->isVendorLookupQuery($table, $query)) {
			return;
		}

		// Skip check for admins
		if (TenantUtility::isAdmin()) {
			return;
		}

		if (in_array($tableName, $this->noAccessTableNames)) {
			Log::warning("VendorBehavior: forbid vendor user access to table $tableName");
			throw new VendorViolationException("Forbid vendor user access to table $tableName");
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
	 * @throws \App\Exception\VendorViolationException
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
		$tableName = $table->getTable();
		$vendorId = VendorUtility::getVendorIdFromRequest();

		// Skip check for admins
		if (TenantUtility::isAdmin()) {
			return;
		}

		if (in_array($tableName, $this->noAccessTableNames)) {
			Log::warning("VendorBehavior: forbid vendor user modification to non-access table $tableName");
			throw new VendorViolationException("Forbid vendor user modification to non-access table $tableName");
		}

		if (in_array($tableName, $this->readonlyTableNames)) {
			Log::warning("VendorBehavior: forbid vendor user modification to readonly table $tableName");
			throw new VendorViolationException("Forbid vendor user modification to readonly table $tableName");
		}

		// set client_id's for selected tables that vendor can change
		if ($tableName === 'appeal_notes' || $tableName === 'appeal_not_defendable_reasons') {
			if (!VendorUtility::isAppealAccessibleByVendorUser($entity->appeal_id)) {
				Log::warning("VendorBehavior: forbid vendor user access to $tableName row related to appeal " . strval($entity->appeal_id));
				throw new VendorViolationException("Forbid vendor user access to $tableName row related to appeal " . strval($entity->appeal_id));
			}
			if (empty($entity->client_id)) {
				$appeals = $this->fetchTable('Appeals');
				$appealEntity = $appeals->get($entity->appeal_id);
				$entity->client_id = $appealEntity->client_id;
			}
		} elseif ($tableName === 'case_activity') {
			if (!VendorUtility::isCaseAccessibleByVendorUser($entity->case_id)) {
				Log::warning("VendorBehavior: forbid vendor user access to $tableName row related to case " . strval($entity->case_id));
				throw new VendorViolationException("Forbid vendor user access to $tableName row related to case " . strval($entity->case_id));
			}
			if (empty($entity->client_id)) {
				$cases = $this->fetchTable('Cases');
				$caseEntity = $cases->get($entity->case_id);
				$entity->client_id = $caseEntity->client_id;
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
	 * @throws \App\Exception\VendorViolationException
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
		$tableName = $table->getTable();
		$vendorId = VendorUtility::getVendorIdFromRequest();

		// Skip check for admins
		if (TenantUtility::isAdmin()) {
			return;
		}

		if (in_array($tableName, $this->noAccessTableNames)) {
			Log::warning("VendorBehavior: forbid vendor user deletion to non-access table $tableName");
			throw new VendorViolationException("Forbid vendor user deletion to non-access table $tableName");
		}

		if (in_array($tableName, $this->readonlyTableNames)) {
			Log::warning("VendorBehavior: forbid vendor user deletion to readonly table $tableName");
			throw new VendorViolationException("Forbid vendor user deletion to readonly table $tableName");
		}
	}
}
