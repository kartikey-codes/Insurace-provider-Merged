<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Lib\ClientUtility\ClientUtility;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * State Controller
 */
class StateController extends ApiController
{
	use LocatorAwareTrait;

	/**
	 * State method
	 *
	 * This is polled every 30 seconds or so on the SPA to
	 * keep global things (like incoming documents) in sync with all online users.
	 *
	 * @return void
	 */
	public function index(): void
	{
		$incomingDocumentCount = $this->fetchTable('IncomingDocuments')
			->find('new')
			->count();

		$outgoingDocumentCount = $this->fetchTable('OutgoingDocuments')
			->find('new')
			->count();

		$openCases = $this->fetchTable('Cases')
			->find('open')
			->count();

		$openCaseRequests = $this->fetchTable('CaseRequests')
			->find('open')
			->count();

		$openAppeals = $this->fetchTable('Appeals')
			->find('notFinished')
			->count();

		$onlineUsers = $this->fetchTable('Users')
			->find('online')
			->select([
				'id',
				'first_name',
				'middle_name',
				'last_name',
				'email',
				'last_seen'
			])
			->all();

		$this->set(compact(
			'incomingDocumentCount',
			'outgoingDocumentCount',
			'openCases',
			'openCaseRequests',
			'openAppeals',
			'onlineUsers',
		));
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
		$this->set([
			// Client Name
			'clientName' => ClientUtility::getClientName(),

			// Permissions
			'myPermissions' => $this->fetchTable('Permissions')
				->find('userEffective', [
					'user_id' => $this->currentUser->id
				])
				->all(),

			// Auth
			'roles' => $this->fetchTable('Roles')
				->find('all')
				->find('ordered')
				->find('limited')
				->all(),

			// Application
			'agencies' => $this->fetchTable('Agencies')
				->find('active')
				->find('ordered')
				->find('limited')
				->all(),

			'auditReviewers' => $this->fetchTable('AuditReviewers')
				->find('active')
				->find('ordered')
				->find('limited')
				->contain(['Agencies'])
				->select([
					'Agencies.id',
					'Agencies.name'
				])
				->all(),

			'clientEmployees' => $this->fetchTable('ClientEmployees')
				->find('active')
				->find('ordered')
				->all(),

			'disciplines' => $this->fetchTable('Disciplines')
				->find('limited')
				->find('ordered')
				->all(),

			'facilities' => $this->fetchTable('Facilities')
				->find('active')
				->find('ordered')
				->contain(['FacilityTypes'])
				->all(),

			'facilityTypes' =>  $this->fetchTable('FacilityTypes')
				->find('ordered')
				->find('limited')
				->all(),

			'insuranceProviders' => $this->fetchTable('InsuranceProviders')
				->find('active')
				->find('ordered')
				->find('limited')
				->all(),

			'insuranceTypes' => $this->fetchTable('InsuranceTypes')
				->find('ordered')
				->find('limited')
				->all(),

			'referenceNumbers' => $this->fetchTable('ReferenceNumbers')
				->find('ordered')
				->find('limited')
				->all(),

			// Cases
			'caseOutcomes' => $this->fetchTable('CaseOutcomes')
				->find('ordered')
				->find('limited')
				->all(),

			'caseTypes' => $this->fetchTable('CaseTypes')
				->find('ordered')
				->find('limited')
				->all(),

			'denialTypes' => $this->fetchTable('DenialTypes')
				->find('ordered')
				->find('limited')
				->all(),

			'denialReasons' => $this->fetchTable('DenialReasons')
				->find('ordered')
				->find('limited')
				->all(),

			'notDefendableReasons' => $this->fetchTable('NotDefendableReasons')
				->find('limited')
				->all(),

			'withdrawnReasons' => $this->fetchTable('WithdrawnReasons')
				->find('limited')
				->all(),

			// Appeals
			'appealLevels' => $this->fetchTable('AppealLevels')
				->find('ordered')
				->find('limited')
				->all(),

			'appealTypes' => $this->fetchTable('AppealTypes')
				->find('ordered')
				->find('limited')
				->all(),

			'daysToRespondFroms' => $this->fetchTable('DaysToRespondFroms')
				->find('ordered')
				->find('limited')
				->all(),

			'utcReasons' => $this->fetchTable('UtcReasons')
				->find('limited')
				->all(),
		]);
	}
}
