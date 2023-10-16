<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Lib\DateUtility\DateUtility;
use App\Model\Table\AppealsTable;
use App\Model\Table\CasesTable;
use App\Model\Table\CaseRequestsTable;
use Cake\Cache\Cache;
use Cake\I18n\FrozenDate;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\I18n\I18nDateTimeInterface;

/**
 * Charts Controller
 *
 * Handles building chart data for Chart.js
 */
class ChartsController extends ApiController
{
	use LocatorAwareTrait;

	/**
	 * @var \App\Model\Table\AppealsTable
	 */
	public AppealsTable $Appeals;

	/**
	 * @var \App\Model\Table\CasesTable
	 */
	public CasesTable $Cases;

	/**
	 * @var \App\Model\Table\CaseRequestsTable
	 */
	public CaseRequestsTable $CaseRequests;

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

		$this->Appeals = $this->fetchTable('Appeals');
		$this->Cases = $this->fetchTable('Cases');
		$this->CaseRequests = $this->fetchTable('CaseRequests');
	}

	/**
	 * Appeals
	 *
	 * @return void
	 */
	public function appeals(): void
	{
		$startDate = new FrozenDate($this->getRequest()->getData('start'));
		$endDate = new FrozenDate($this->getRequest()->getData('end'));

		$this->set([
			'created' => $this->appealsCreated($startDate, $endDate),
			'completed' => $this->appealsCompleted($startDate, $endDate),
		]);
	}

	/**
	 * Cases
	 *
	 * @return void
	 */
	public function cases(): void
	{
		$this->set('open', $this->Cases->find('open')->count());
		$this->set('utc', $this->Cases->find('utc')->count());
		$this->set('closed', $this->Cases->find('closed')->count());
	}

	/**
	 * Created Appeals
	 *
	 * @param I18nDateTimeInterface $startDate
	 * @param ?I18nDateTimeInterface $endDate
	 * @return array
	 */
	private function appealsCreated(I18nDateTimeInterface $startDate, ?I18nDateTimeInterface $endDate = null): array
	{
		// Get counts of assessments during the specified time period
		$results = $this->Appeals
			->find('countedByDate', [
				'field' => 'created',
				'start' => $startDate,
				'end'   => $endDate,
			])
			->find(
				'search',
				['search' => $this->getRequest()->getQuery()]
			)
			->enableHydration(false)
			->toArray();

		// Add days with zero results to the array
		$data = DateUtility::fillDateArray($results, $startDate, $endDate);

		return $data;
	}

	/**
	 * Completed Appeals
	 *
	 * @param I18nDateTimeInterface $startDate
	 * @param ?I18nDateTimeInterface $endDate
	 * @return array
	 */
	private function appealsCompleted(I18nDateTimeInterface $startDate, ?I18nDateTimeInterface $endDate = null): array
	{
		// Get counts of assessments during the specified time period
		$results = $this->Appeals
			->find('countedByDate', [
				'field' => 'completed',
				'start' => $startDate,
				'end'   => $endDate,
			])
			->find(
				'search',
				['search' => $this->getRequest()->getQuery()]
			)
			->enableHydration(false)
			->toArray();

		// Add days with zero results to the array
		$data = DateUtility::fillDateArray($results, $startDate, $endDate);

		return $data;
	}
}
