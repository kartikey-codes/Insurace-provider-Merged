<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Statistics Controller
 */
class StatisticsController extends ApiController
{
	use LocatorAwareTrait;

	/**
	 * Collection of counts and anything else that
	 * may be relevant for overviews / reporting.
	 *
	 * @todo Cache this
	 * @return void
	 */
	public function index(): void
	{
		$this->set([
			'appeals_by_case_type' => $this->getAppealsByCaseType(),
			'closed_cases' => $this->countClosedCases(),
			'closed_cases_by_outcome' => $this->countClosedCasesByOutcome(),
			'empty_cases' => $this->countEmptyCases(),
			'unassigned_appeals' => $this->countUnassignedAppeals(),
			'open_claims_total' => $this->getOpenClaimsTotal(),
			'reimbursed_total' => $this->getReimbursedTotal()
		]);
	}

	/**
	 * Count closed cases
	 *
	 * @return int
	 */
	private function countClosedCases(): int
	{
		return $this->fetchTable('Cases')
			->find('closed')
			->count();
	}

	/**
	 * Count empty cases
	 *
	 * @return int
	 */
	private function countEmptyCases(): int
	{
		return $this->fetchTable('Cases')
			->find('withoutAppeals')
			->count();
	}

	/**
	 * Count unassigned appeals
	 *
	 * @return int
	 */
	private function countUnassignedAppeals(): int
	{
		return $this->fetchTable('Appeals')
			->find('notCompleted')
			->find('unassigned')
			->count();
	}

	/**
	 * Get appeal by case type
	 *
	 * @return array
	 */
	private function getAppealsByCaseType(): array
	{
		return $this->fetchTable('CaseTypes')
			->find('countMatchingAppeals', [
				'query' => function () {
					return $this->fetchTable('Appeals')->find('notCompleted');
				},
			])
			->all()
			->map(function ($result) {
				return [
					'name' => $result->name,
					'count' => $result->appeals,
				];
			})
			->toArray();
	}

	/**
	 * Count closed cases by outcome
	 *
	 * @return array
	 */
	private function countClosedCasesByOutcome(): array
	{
		$query = $this->fetchTable('Cases')
			->find('closed');

		return $this->fetchTable('Cases')
			->find('closed')
			->contain([
				'CaseOutcomes'
			])
			->select([
				'name' => 'CaseOutcomes.name',
				'count' => $query->func()->count('Cases.id')
			])
			->group([
				'CaseOutcomes.name'
			])
			// Simple key->value list:
			// ->formatResults(function ($results) {
			// 	/* @var $results \Cake\Datasource\ResultSetInterface|\Cake\Collection\CollectionInterface */
			// 	return $results->combine(
			// 		'name',
			// 		function ($row) {
			// 			return $row['count'];
			// 		}
			// 	);
			// })
			->disableHydration()
			->toArray();
	}

	/**
	 * Get open claims total
	 *
	 * @return float
	 */
	private function getOpenClaimsTotal(): float
	{
		$query = $this->fetchTable('Cases')
			->find('open');

		$total = $query
			->select([
				'total' => $query->func()->sum('Cases.total_claim_amount')
			])
			->first();

		return $total['total'] ?: 0;
	}

	/**
	 * Get reimbursed total
	 *
	 * @return float
	 */
	private function getReimbursedTotal(): float
	{
		$query = $this->fetchTable('Cases')
			->find('closed');

		$total = $query
			->select([
				'total' => $query->func()->sum('Cases.reimbursement_amount')
			])
			->first();

		return $total['total'] ?: 0;
	}
}
