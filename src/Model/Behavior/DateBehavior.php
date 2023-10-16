<?php

declare(strict_types=1);

namespace App\Model\Behavior;

use Cake\Datasource\ResultSetInterface;
use Cake\I18n\FrozenDate;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use InvalidArgumentException;

/**
 * Date behavior
 *
 * Behavior for models that have dates and provides counting methods
 */
class DateBehavior extends Behavior
{
	/**
	 * @var array
	 */
	protected $_defaultConfig = [
		'implementedFinders' => [
			'withinDateRange' => 'findWithinDateRange',
			'countedByDate' => 'findCountedByDate',
			'summedByDate' => 'findSummedByDate',
		],
	];

	/**
	 * Find Within Date Range
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \App\Model\Behavior\Cake\ORM\Query
	 */
	public function findWithinDateRange(Query $query, array $options): Query
	{
		// Date field is required
		if (empty($options['field'])) {
			throw new InvalidArgumentException(__('A date field name is required to find within date range.'));
		}

		// Start date is required
		if (empty($options['start'])) {
			throw new InvalidArgumentException(__('A start argument is required to find within date range.'));
		}

		// Create date object from start date
		if (!$options['start'] instanceof FrozenDate) {
			$options['start'] = new FrozenDate($options['start']);
		}

		// Filter by start date
		$query->andWhere(function ($exp) use ($options) {
			return $exp->gte(
				$this->_table->aliasField($options['field']),
				$options['start']->format('Y-m-d')
			);
		});

		// End date not required
		if (!empty($options['end'])) {
			if (!$options['end'] instanceof FrozenDate) {
				$options['end'] = new FrozenDate($options['end']);
			}

			$query->andWhere(function ($exp) use ($options) {
				return $exp->lte(
					$this->_table->aliasField($options['field']),
					$options['end']->format('Y-m-d')
				);
			});
		}

		// Skip null dates
		if (!empty($options['skipNull'])) {
			$query->andWhere(function ($exp, $q) use ($options) {
				return $exp->isNotNull($this->_table->aliasField($options['field']));
			});
		}

		return $query;
	}

	/**
	 * Find Counted By Date
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findCountedByDate(Query $query, array $options): Query
	{
		// Date field is required
		if (empty($options['field'])) {
			throw new InvalidArgumentException(__('A date field name is required to count by date.'));
		}

		// Start date is required
		if (empty($options['start'])) {
			throw new InvalidArgumentException(__('A start argument is required to count by date.'));
		}

		/**
		 * Limit date range
		 *
		 * @var \Cake\ORM\Query $query
		 */
		$query = $this->findWithinDateRange($query, $options);

		// Convert & count by date
		$query->select([
			'count' => $query->func()->count($this->_table->getPrimaryKey()),
			$options['field'] => $query->func()->convert([
				'date' => 'literal',
				$this->_table->aliasField($options['field']) => 'identifier',
			]),
		]);

		// Group by date field
		$query->group([
			$options['field'] => $query->func()->convert([
				'date' => 'literal',
				$this->_table->aliasField($options['field']) => 'identifier',
			]),
		]);

		// Sort by the date field
		$query->order([
			$options['field'] => 'asc',
		]);

		// Format keys as year-month-day format
		$query->formatResults(function (ResultSetInterface $results) use ($options) {
			return $results->map(function ($row) use ($options) {
				$field = $options['field'];
				$frozenDate = new FrozenDate($row[$field]);
				$row['date'] = $frozenDate->format('Y-m-d');

				return $row;
			});
		});

		// Return a list instead of entities
		$query->find('list', [
			'keyField' => 'date',
			'valueField' => 'count',
		]);

		return $query;
	}

	/**
	 * Find Summed By Date
	 *
	 * @param \Cake\ORM\Query $query
	 * @param array $options
	 * @return \Cake\ORM\Query
	 */
	public function findSummedByDate(Query $query, array $options): Query
	{
		// Date field is required
		if (empty($options['field'])) {
			throw new InvalidArgumentException(__('A date field name is required to sum by date.'));
		}

		if (empty($options['sumField'])) {
			throw new InvalidArgumentException(__('A sum field name is required to sum by date.'));
		}

		// Start date is required
		if (empty($options['start'])) {
			throw new InvalidArgumentException(__('A start argument is required to sum by date.'));
		}

		/**
		 * Limit date range
		 *
		 * @var \Cake\ORM\Query $query
		 */
		$query = $this->findWithinDateRange($query, $options);

		// Convert & sum by date
		$query->select([
			'sum' => $query->func()->sum($options['sumField']),
			$options['field'] => $query->func()->convert([
				'date' => 'literal',
				$this->_table->aliasField($options['field']) => 'identifier',
			]),
		]);

		// Group by date field
		$query->group([
			$options['field'] => $query->func()->convert([
				'date' => 'literal',
				$this->_table->aliasField($options['field']) => 'identifier',
			]),
		]);

		// Sort by the date field
		$query->order([
			$options['field'] => 'asc',
		]);

		// Format keys as year-month-day format
		$query->formatResults(function (ResultSetInterface $results) use ($options) {
			return $results->map(function ($row) use ($options) {
				$field = $options['field'];

				$row['date'] = $row[$field]->format('Y-m-d');

				return $row;
			});
		});

		// Return a list instead of entities
		$query->find('list', [
			'keyField' => 'date',
			'valueField' => 'sum',
		]);

		return $query;
	}
}
