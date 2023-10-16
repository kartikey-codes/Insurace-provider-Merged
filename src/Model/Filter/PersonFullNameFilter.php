<?php

declare(strict_types=1);

namespace App\Model\Filter;

use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\IdentifierExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Search\Model\Filter\Base;

/**
 * @todo Make search case-insensitive at least on Postgres
 */
class PersonFullNameFilter extends Base
{
	/**
	 * Used when building an identifier expression to handle case sensitivity.
	 * Trying to figure out a case-insensitive collation compatible with
	 * Postgres on Azure for matching names on upper or lower case in the db.
     *
     * @var string
	 */
	private string $collation = '';

	/**
	 * Process string concatenated first, middle and last name.
	 *
	 * @return bool
	 */
	public function process(): bool
	{
		if (is_null($this->value())) {
			return false;
		}

		$this->getQuery()->where(function (QueryExpression $exp, Query $q) {
			$conditions = $this->buildConditions($q);

			return $exp->or($conditions);
		});

		return true;
	}

	/**
	 * Build LIKE conditions for different name formats
	 * based on filter configuration.
	 *
	 * @param \Cake\ORM\Query $q
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function buildConditions(Query $q): array
	{
		$conditions = [];

		// FirstName LastName
		if ($this->getConfig('searchFirstLast', true)) {
			$conditions[] = $q->newExpr()->like(
				$this->concatenateFirstLast($q),
				$this->getQueryString()
			);
		}

		// FirstName LastName
		if ($this->getConfig('searchLastFirst', true)) {
			$conditions[] = $q->newExpr()->like(
				$this->concatenateLastFirst($q),
				$this->getQueryString()
			);
		}

		// Don't include middle name if not configured for model
		if (!empty($this->getConfig('middleName'))) {
			// FirstName MiddleName LastName
			if ($this->getConfig('searchFirstMiddleLast', true)) {
				$conditions[] = $q->newExpr()->like(
					$this->concatenateFirstMiddleLast($q),
					$this->getQueryString()
				);
			}

			// LastName, FirstName MiddleName
			if ($this->getConfig('searchLastFirstMiddle', true)) {
				$conditions[] = $q->newExpr()->like(
					$this->concatenateLastFirstMiddle($q),
					$this->getQueryString()
				);
			}
		}

		return $conditions;
	}

	/**
	 * Get wildcard query string to use in comparison
     *
     * @return string
	 */
	public function getQueryString(): string
	{
		$value = $this->value();

		if ($this->getConfig('before')) {
			$value = '%' . $value;
		}

		if ($this->getConfig('after')) {
			$value = $value . '%';
		}

		return $value;
	}

	/**
	 * Get expression for "FirstName LastName" format
     *
     * @param \Cake\ORM\Query $q
	 * @return \Cake\Database\Expression\FunctionExpression
	 */
	public function concatenateFirstLast(Query $q): FunctionExpression
	{
		return $q->func()->concat([
			$this->getFirstNameIdentifier(),
			' ',
			$this->getLastNameIdentifier(),
		]);
	}

	/**
	 * Get expression for "LastName, FirstName" format
     *
     * @param \Cake\ORM\Query $q
	 * @return \Cake\Database\Expression\FunctionExpression
	 */
	public function concatenateLastFirst(Query $q): FunctionExpression
	{
		return $q->func()->concat([
			$this->getLastNameIdentifier(),
			', ',
			$this->getFirstNameIdentifier(),
		]);
	}

	/**
	 * Get expression for "FirstName MiddleName LastName" format
     *
     * @param \Cake\ORM\Query $q
	 * @return \Cake\Database\Expression\FunctionExpression
	 */
	public function concatenateFirstMiddleLast(Query $q): FunctionExpression
	{
		return $q->func()->concat([
			$this->getFirstNameIdentifier(),
			' ',
			$this->getMiddleNameIdentifier(),
			' ',
			$this->getLastNameIdentifier(),
		]);
	}

	/**
	 * Get expression for "LastName, FirstName MiddleName" format
     *
     * @param \Cake\ORM\Query $q
	 * @return \Cake\Database\Expression\FunctionExpression
	 */
	public function concatenateLastFirstMiddle(Query $q): FunctionExpression
	{
		return $q->func()->concat([
			$this->getLastNameIdentifier(),
			', ',
			$this->getFirstNameIdentifier(),
			' ',
			$this->getMiddleNameIdentifier(),
		]);
	}

	/**
	 * Get first name column literal identifier
     *
     * @return \Cake\Database\Expression\IdentifierExpression
	 */
	private function getFirstNameIdentifier(): IdentifierExpression
	{
		return new IdentifierExpression($this->getConfig('firstName', ''), $this->collation);
	}

	/**
	 * Get middle name column literal identifier
     *
     * @return \Cake\Database\Expression\IdentifierExpression
	 */
	private function getMiddleNameIdentifier(): IdentifierExpression
	{
		return new IdentifierExpression($this->getConfig('middleName', ''), $this->collation);
	}

	/**
	 * Get last name column literal identifier
     *
     * @return \Cake\Database\Expression\IdentifierExpression
	 */
	private function getLastNameIdentifier(): IdentifierExpression
	{
		return new IdentifierExpression($this->getConfig('lastName', ''), $this->collation);
	}
}
