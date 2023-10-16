<?php

declare(strict_types=1);

namespace App\Controller\Vendor\Api;

use App\Model\Table\AppealsTable;
use App\Model\Table\CasesTable;
use App\Model\Table\CaseTypesTable;
use App\Model\Table\IncomingDocumentsTable;
use Cake\Event\EventInterface;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Dashboard Controller
 */
class DashboardController extends ApiController
{
	use LocatorAwareTrait;

	/**
	 * This controller does not have an inflected model
	 *
	 * @var string
	 */
	public $modelClass = false;

	/**
	 * @var \App\Model\Table\AppealsTable
	 */
	public AppealsTable $Appeals;

	/**
	 * @var \App\Model\Table\CasesTable
	 */
	public CasesTable $Cases;

	/**
	 * @var \App\Model\Table\CaseTypesTable
	 */
	public CaseTypesTable $CaseTypes;

	/**
	 * @var \App\Model\Table\IncomingDocumentsTable
	 */
	public IncomingDocumentsTable $IncomingDocuments;

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

		// Load models used by this controller here
		$this->Appeals = $this->fetchTable('Appeals');
		$this->Cases = $this->fetchTable('Cases');
		$this->CaseTypes = $this->fetchTable('CaseTypes');
		$this->IncomingDocuments = $this->fetchTable('IncomingDocuments');
	}

	/**
	 * Startup method
	 *
	 * This is called when starting the SPA to fetch lists for
	 * vuex to store in state.
	 *
	 * @return void
	 */
	public function startup(): void
	{
		$data = [
			// Application
			'caseOutcomes' => $this->fetchTable('CaseOutcomes')->find()->all(),
			'caseTypes' => $this->fetchTable('CaseTypes')->find()->all(),
			'denialTypes' => $this->fetchTable('DenialTypes')->find()->all(),
			'facilityTypes' => $this->fetchTable('FacilityTypes')->find()->all(),
			'insuranceTypes' => $this->fetchTable('InsuranceTypes')->find()->all(),

			// Appeals
			'appealLevels' => $this->fetchTable('AppealLevels')->find()->all(),
			'appealTypes' => $this->fetchTable('AppealTypes')->find()->all(),
			'daysToRespondFroms' => $this->fetchTable('DaysToRespondFroms')->find()->all(),
			'notDefendableReasons' => $this->fetchTable('NotDefendableReasons')->find()->all(),
		];

		$this->set($data);
	}
}
