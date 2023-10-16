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
 * Calendar Controller
 *
 * Handles finding models in date ranges
 */
class CalendarController extends ApiController
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
	 * Calendar Events
	 *
	 * @return void
	 */
	public function index(): void
	{
		$startDate = $this->getRequest()->getData('start', new FrozenDate('-32 days'));
		$endDate = $this->getRequest()->getData('end', new FrozenDate('+32 days'));

		$results = [];

		$this->set([
			'start' => $startDate,
			'end' => $endDate,
			'data' => $results
		]);
	}

	/**
	 * Calendar Events
	 *
	 * @param string $date
	 * @return void
	 */
	public function view(string $date): void
	{
		$frozenDate = new FrozenDate($date);

		$appeals = $this->Appeals
			->find('open')
			->where([
				'due_date' => $frozenDate
			])
			->contain([
				'AppealLevels',
				'Cases' => [
					'Patients'
				]
			])
			->toList();

		$requests = $this->CaseRequests
			->find('open')
			->where([
				'due_date' => $frozenDate
			])
			->toList();

		$count = count($appeals) + count($requests);

		$this->set([
			'date' => $frozenDate,
			'count' => $count,
			'appeals' => $appeals,
			'requests' => $requests
		]);
	}
}
