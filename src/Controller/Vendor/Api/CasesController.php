<?php

declare(strict_types=1);

namespace App\Controller\Vendor\Api;

use App\Model\Entity\Appeal;
use App\Model\Table\AppealsTable;
use Cake\Log\Log;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;

/**
 * Cases Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\CasesTable $Cases
 */
class CasesController extends ApiController
{
	use LocatorAwareTrait;

	/**
	 * @var \App\Model\Table\AppealsTable
	 */
	public AppealsTable $Appeals;

	/**
	 * @var array
	 */
	public $paginate = [
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'id' => 'desc',
		],
		'sortableFields' => [
			'id',
			'insurance_plan',
			'total_claim_amount',
			'disputed_amount',
			'settled_amount',
			'reimbursement_amount',
			'insurance_number',
			'visit_number',
			'admit_date',
			'discharge_date',
			'closed',
			'assigned',
			'created',
			'modified',
			// Associations
			'CaseTypes.name',
			'Clients.name',
			'Patients.first_name',
			'Patients.middle_name',
			'Patients.last_name',
			'Patients.date_of_birth',
			'Patients.phone',
			'Patients.fax',
			'Patients.sex',
			'Patients.city',
			'Patients.state',
			'Patients.zip',
			'Patients.marital_status',
			'Facilities.name',
			'DenialTypes.name',
			'CaseOutcomes.name',
			'InsuranceProviders.name',
			'InsuranceTypes.name',
			'ClosedByUser.first_name',
			'ClosedByUser.last_name',
			'AssignedToUser.first_name',
			'AssignedToUser.last_name',
			'CreatedByUser.first_name',
			'CreatedByUser.last_name',
			'ModifiedByUser.first_name',
			'ModifiedByUser.last_name',
		],
		'contain' => [
			'Appeals' => [
				'AppealLevels' => [
					'finder' => 'ordered',
				],
				'AppealTypes' => [
					'finder' => 'ordered',
				],
				'DaysToRespondFroms' => [
					'finder' => 'ordered',
				],
			],
			'CaseTypes',
			'Clients',
			'Patients',
			'Facilities',
			'DenialTypes',
			'DenialReasons' => [
				'finder' => 'ordered',
			],
			'CaseOutcomes',
			'CaseReadmissions',
			'InsuranceProviders',
			'InsuranceTypes',
			'ClosedByUser',
			'AssignedToUser',
			'CreatedByUser',
			'ModifiedByUser',
		],
	];

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
	}

	/**
	 * View method
	 *
	 * @param int $id
	 * @return void
	 * @throws \Cake\Core\Exception\CakeException When record not found.
	 */
	public function view($id): void
	{
		$entity = $this->Cases->getFull($id);

		$this->set('data', $entity);
	}

	/**
	 * Index method
	 *
	 * @return void
	 */
	public function index(): void
	{
		$this->crudIndex();
	}

	/**
	 * Activity method
	 *
	 * Returns users currently viewing a case
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function activity($id): void
	{
		// Update current users activity
		$activityTable = $this->fetchTable('CaseActivity');

		$activityTable->touch(
			intval($id),
			intval($this->currentUser->id)
		);

		// Return currently active users
		$entities = $this->Cases
			->ActiveUsers
			->find('all')
			->select([
				'ActiveUsers.id',
				'ActiveUsers.first_name',
				'ActiveUsers.last_name',
			])
			->select($this->Cases->CaseActivity)
			->matching('CaseActivity', function (Query $q) use ($id) {
				return $q->find('current')->where([
					'CaseActivity.case_id' => $id,
				]);
			});

		$this->set('data', $entities->all());
	}

	/**
	 * Complete the appeals under this case assigned to current vendor
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When rules check fails.
	 */
	public function completeAppeals($id): void
	{
		$entity = $this->Cases->getFull($id);

		try {
			if (empty($entity->appeals)) {
				Log::warning('completeAppeals: trying to complete appeals for case without appeals');

				return;
			}

			foreach ($entity->appeals as $appeal) {
				$appealEntity = $this->Appeals->getFull($appeal->id);

				if ($appealEntity->appeal_status == Appeal::STATUS_ASSIGNED && $appealEntity->assigned_to_vendor_id == $this->vendorId) {
					$appealEntity->set('appeal_status', Appeal::STATUS_COMPLETED);
					$this->Appeals->saveOrFail($appealEntity);
				}
			}
			$entity = $this->Cases->getFull($id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}
}
