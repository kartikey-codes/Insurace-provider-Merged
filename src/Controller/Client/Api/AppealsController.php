<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Lib\StorageUtility\StorageUtility;
use App\Lib\TenantUtility\TenantUtility;
use App\Model\Entity\Appeal;
use App\Model\Table\UsersTable;
use App\Model\Table\VendorsTable;
use App\Service\DocumentServiceInterface;
use App\Service\StorageServiceInterface;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Cake\Log\Log;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Appeals Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\DownloadComponent $Download
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\AppealsTable $Appeals
 * @property \App\Model\Table\CasesTable $Cases
 */
class AppealsController extends ApiController
{
	use LocatorAwareTrait;

	/**
	 * @var \App\Model\Table\VendorsTable
	 */
	public VendorsTable $Vendors;

	/**
	 * @var \App\Model\Table\UsersTable
	 */
	public UsersTable $Users;

	/**
	 * View file for cover page
	 * @var string
	 */
	public string $coverPageView = '/Client/Api/Appeals/pdf/cover_page';

	/**
	 * Headers for CSV export
	 *
	 * @var array
	 */
	public array $csvHeaders = [
		'Appeal ID',
		'Status',
		'Patient',
		'Created',
		'Due Date'
	];

	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'id' => 'desc',
		],
		'sortableFields' => [
			'id',
			'case_id',
			'defendable',
			'letter_date',
			'received_date',
			'due_date',
			'hearing_date',
			'priority',
			'assigned',
			'completed',
			'created',
			'modified',
			'unable_to_complete',
			'audit_identifier',
			// Associations
			'Agencies.name',
			'AppealTypes.name',
			'Cases.id',
			'Cases.visit_number',
			'Cases.total_claim_amount',
			'Cases.disputed_amount',
			'Cases.settled_amount',
			'Cases.reimbursement_amount',
			'Cases.insurance_number',
			'Cases.admit_date',
			'Cases.discharge_date',
			'Cases.closed',
			'CaseTypes.name',
			'Clients.name',
			'DenialTypes.name',
			'Facilities.name',
			'Patients.first_name',
			'Patients.last_name',
			'CaseOutcomes.name',
			'AppealLevels.name',
			'InsuranceProviders.name',
			'InsuranceTypes.name',
			'AssignedToUser.first_name',
			'AssignedToUser.last_name',
			'CompletedByUser.first_name',
			'CompletedByUser.last_name',
			'CreatedByUser.first_name',
			'CreatedByUser.last_name',
			'ModifiedByUser.first_name',
			'ModifiedByUser.last_name',
		],
		'contain' => [
			'Agencies',
			'AppealTypes',
			'AppealLevels',
			'Cases' => [
				'CaseTypes',
				'Clients',
				'Patients',
				'Facilities',
				'DenialTypes',
				'CaseOutcomes',
				'InsuranceProviders',
				'InsuranceTypes',
			],
			'CompletedByUser',
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

		$this->Vendors = $this->fetchTable('Vendors');
		$this->Users = $this->fetchTable('Users');

		$this->loadComponent('Download');

		// Unused view code to refactor later

		// $this->viewBuilder()->setOption(
		// 	'pdfConfig',
		// 	[
		// 		'download' => false,
		// 		'orientation' => 'portrait',
		// 		//'filename' => $entity->list_name // Forces download
		// 	]
		// );

		// $this->viewBuilder()
		// 	->setClassName('CakePdf.Pdf')
		// 	->setOption('serialize', false)
		// 	->setLayout('pdf/default')
		// 	->disableAutoLayout()
		// 	->setTemplatePath('Client' . DS . 'Api' . DS . 'Appeals')
		// 	->setTemplate('cover_page');

		// $this->setResponse(
		// 	$this->getResponse()
		// 		->withType('application/pdf')
		// 	//->withStringBody($contents)
		// );
	}

	/**
	 * Get the extraction rules for CSV formatting.
	 * Should be provided by extending controller class.
	 *
	 * @return array
	 */
	protected function getCsvExtract(): array
	{
		return [
			'id',
			'appeal_status',
			function (array $row) {
				return $row['case']['patient']['full_name'];
			},
			'created',
			'due_date'
		];
	}

	/**
	 * Add method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function add(): void
	{
		$entity = $this->Appeals->newEntity($this->getRequest()->getData());
		$entity->appeal_status = Appeal::STATUS_OPEN;

		// Always ensure client ID is set
		$entity->client_id = TenantUtility::getTenantIdFromRequest();

		try {
			$this->Appeals->saveOrFail($entity, [
				'associated' => [
					'AppealReferenceNumbers',
				],
			]);

			if (!empty($this->getRequest()->getData('attach_document_id'))) {
				$this->Cases->IncomingDocuments->attachToAppeal(
					$this->getRequest()->getData('attach_document_id'),
					$entity->id
				);
			}

			$entity = $this->Appeals->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * View method
	 *
	 * @param int $id Primary Key.
	 * @return void
	 * @throws \Cake\Core\Exception\CakeException When record not found.
	 */
	public function view($id): void
	{
		$entity = $this->Appeals->getFull($id);

		$this->set('data', $entity);
	}

	/**
	 * PDF method
	 *
	 * @param int $id Primary Key.
	 * @return \Cake\Http\Response|void
	 * @throws \Cake\Core\Exception\CakeException When record not found.
	 */
	public function pdf($id, StorageServiceInterface $storageService)
	{
		$this->getRequest()->allowMethod(['GET']);

		$packetFilename = $id . '.pdf';
		$packetExists = $storageService->appealPackets()->fileExists($packetFilename);

		$entity = $this->Appeals->get($id, [
			'finder' => 'full',
			'skipTenantCheck' => $this->currentUser->isAdmin()
		]);

		$this->set('title', $entity->pdf_title);
		$this->set('data', $entity);
		$this->set('packet', $packetExists);

		// Send packet if it has already been generated
		if ($packetExists) {
			$fileSize = $storageService->appealPackets()->fileSize($packetFilename);
			$mimeType = StorageUtility::getMimeTypeFromFilePath($packetFilename);

			return $this->Download->previewFile(
				$packetFilename,
				$fileSize,
				$storageService->appealPackets()->readStream($packetFilename),
				$mimeType
			);
		} else {
			// Render PDF if not generated
			$this->viewBuilder()->setClassName('CakePdf.Pdf');

			// $this->viewBuilder()->setOption(
			// 	'pdfConfig',
			// 	[
			// 		'download' => false,
			// 		'orientation' => 'portrait',
			// 		//'filename' => $entity->list_name // Forces download
			// 	]
			// );

			$this->setResponse(
				$this->getResponse()->withType('application/pdf')
			);
		}
	}

	/**
	 * Cover Page method
	 *
	 * @param int $id Primary Key.
	 * @param DocumentServiceInterface $documentService
	 * @param StorageServiceInterface $storageService
	 * @return void
	 * @throws \Cake\Core\Exception\CakeException When record not found.
	 */
	public function coverPage(int $id, DocumentServiceInterface $documentService, StorageServiceInterface $storageService)
	{
		$fileName = $id . '/' . 'Cover Page.pdf';
		$exists = $storageService->appeals()->fileExists($fileName);

		if ($exists) {
			$fileSize = $storageService->appeals()->fileSize($fileName);
			$mimeType = StorageUtility::getMimeTypeFromFilePath($fileName);

			return $this->Download->previewFile(
				$fileName,
				$fileSize,
				$storageService->appeals()->readStream($fileName),
				$mimeType
			);
		}

		$entity = $this->Appeals->getFull($id);

		$this->set([
			'data' => $entity,
		]);

		$this->viewBuilder()->setClassName('CakePdf.Pdf');

		// $this->viewBuilder()->setOption(
		// 	'pdfConfig',
		// 	[
		// 		'download' => false,
		// 		'orientation' => 'portrait',
		// 		//'filename' => $entity->list_name // Forces download
		// 	]
		// );

		$this->setResponse(
			$this->getResponse()
				->withType('application/pdf')
			//->withStringBody($contents)
		);
	}

	/**
	 * Generate cover page file
	 *
	 * @param int $id Appeal ID
	 * @param DocumentServiceInterface $documentService
	 * @param StorageServiceInterface $storageService
	 * @return void
	 * @throws Exception
	 * @throws PdfGenerationException
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public function generateCoverPage(int $id, DocumentServiceInterface $documentService, StorageServiceInterface $storageService): void
	{
		$this->request->allowMethod(['POST']);

		$entity = $this->Appeals->getFull($id);
		$fileName = $id . '/' . 'Cover Page.pdf';
		$exists = $storageService->appeals()->fileExists($fileName);

		$contents = $documentService->renderViewAsPdf($this->coverPageView, [
			'data' => $entity,
			'letter' => $this->getRequest()->getData('letter', '')
		]);

		$storageService->appeals()->write($fileName, $contents);

		$this->set([
			'exists' => $exists,
			'success' => true
		]);
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
	 * Unassigned method
	 *
	 * Used for dispatch
	 *
	 * @return void
	 */
	public function unassigned(): void
	{
		// Search Plugin
		$entities = $this->Appeals->find(
			'search',
			['search' => $this->getRequest()->getQuery()]
		)
			->find('open')
			->find('unassigned')
			->find('queueOrder');

		$this->set('data', $this->paginate($entities));
		$this->set('pagination', $this->getDefaultTablePagination());
	}

	/**
	 * Open By Facility method
	 *
	 * @return void
	 */
	public function openByFacility(): void
	{
		// Search Plugin
		$entities = $this->Appeals
			->find('openByFacility')
			->order([
				'count' => 'desc',
				'Facilities.name' => 'asc',
			]);

		$this->set('data', $entities);
	}

	/**
	 * Open By Assigned User method
	 *
	 * @return void
	 */
	public function openByAssignedUser(): void
	{
		$entities = $this->Appeals->AssignedToUser->find('active');

		if (!empty($this->getRequest()->getQuery('case_type_id'))) {
			$entities->leftJoinWith('Appeals.Cases')->andWhere([
				'Cases.case_type_id' => $this->getRequest()->getQuery('case_type_id'),
			]);
		}

		$entities->leftJoinWith('Appeals', function ($q) {
			return $q->find('open');
		});

		$entities->enableAutoFields(false);

		$entities->select([
			'AssignedToUser.id',
			'AssignedToUser.first_name',
			'AssignedToUser.last_name',
			'AssignedToUser.active',
			'count' => $entities->func()->count('Appeals.id'),
		]);

		$entities->group([
			'AssignedToUser.id',
			'AssignedToUser.first_name',
			'AssignedToUser.last_name',
			'AssignedToUser.active',
		]);

		$entities->order([
			'count' => 'desc',
		]);

		$this->set('data', $entities);
	}

	/**
	 * Edit method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function edit($id): void
	{
		$entity = $this->Appeals->getFull($id);
		$entity = $this->Appeals->patchEntity($entity, $this->getRequest()->getData());

		try {
			$this->Appeals->saveOrFail($entity, [
				'associated' => [
					'AppealReferenceNumbers',
				],
			]);
			$entity = $this->Appeals->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Unable to complete method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function utc($id): void
	{
		$this->request->allowMethod(['patch', 'post', 'put']);

		$entity = $this->Appeals->get($id);

		$entity = $this->Appeals->patchEntity($entity, $this->getRequest()->getData(), [
			'associated' => [
				'UtcReasons'
			]
		]);

		try {
			$this->Appeals->saveOrFail($entity, [
				'associated' => [
					'UtcReasons',
				],
			]);
			$entity = $this->Appeals->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Defendable method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function defendable($id): void
	{
		$this->request->allowMethod(['patch', 'post', 'put']);

		$entity = $this->Appeals->get($id);

		$entity = $this->Appeals->patchEntity($entity, $this->getRequest()->getData(), [
			'associated' => [
				'NotDefendableReasons'
			]
		]);

		try {
			$this->Appeals->saveOrFail($entity, [
				'associated' => [
					'NotDefendableReasons',
				],
			]);
			$entity = $this->Appeals->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Reopen method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function reopen($id): void
	{
		/** @var \App\Model\Entity\Appeal */
		$entity = $this->Appeals->getFull($id);
		/** @var \App\Model\Entity\Appeal */
		$entity = $this->Appeals->patchEntity($entity, $this->getRequest()->getData());

		$entity->assigned_to_vendor_id = null;
		$entity->setReopenedBy($this->currentUser);

		try {
			$this->Appeals->saveOrFail($entity);
			$entity = $this->Appeals->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Submit method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function submit($id): void
	{
		/** @var \App\Model\Entity\Appeal */
		$entity = $this->Appeals->getFull($id);
		/** @var \App\Model\Entity\Appeal */
		$entity = $this->Appeals->patchEntity($entity, $this->getRequest()->getData(), [
			'associated' => [],
		]);

		try {
			$assignNow = Configure::read('Appeals.assignImmediately', false);

			if ($assignNow) {
				$vendor = $this->Vendors->find('assignableToAppeal', [
					'appeal' => $entity,
					'skipTenantCheck' => true,
				])->first();

				$entity->assignToVendor($vendor);

				$this->Appeals->saveOrFail($entity, [
					'skipTenantCheck' => true,
				]);

				$event = new Event('Model.Appeal.assignedToVendor', $this, [$entity]);
				$this->Appeals->getEventManager()->dispatch($event);
			} else {
				$entity->setSubmittedBy($this->currentUser);

				$this->Appeals->saveOrFail($entity, [
					'associated' => [],
				]);
			}

			$entity = $this->Appeals->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Cancel method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function cancel($id): void
	{
		/** @var \App\Model\Entity\Appeal */
		$entity = $this->Appeals->getFull($id);
		/** @var \App\Model\Entity\Appeal */
		$entity = $this->Appeals->patchEntity($entity, $this->getRequest()->getData());
		$entity->setCancelledBy($this->currentUser);

		try {
			$this->Appeals->saveOrFail($entity);
			$entity = $this->Appeals->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Close method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function close($id): void
	{
		/** @var \App\Model\Entity\Appeal */
		$entity = $this->Appeals->getFull($id);
		/** @var \App\Model\Entity\Appeal */
		$entity = $this->Appeals->patchEntity($entity, $this->getRequest()->getData());
		$entity->setClosedBy($this->currentUser);

		try {
			$this->Appeals->saveOrFail($entity);
			$entity = $this->Appeals->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Assign method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When rules check fails.
	 */
	public function assign($id): void
	{
		$entity = $this->Appeals->get($id);

		$entity->set([
			'assigned' => new FrozenTime(),
			'assigned_to' => $this->getRequest()->getData('user_id'),
		]);

		$this->Appeals->saveOrFail($entity);

		$entity = $this->Appeals->getFull($id);

		$this->set('data', $entity);
		$this->set('result', true);
	}

	/**
	 * Complete method
	 *
	 * @return void
	 * @throws \Cake\ORM\Exception\PersistenceFailedException
	 */
	public function complete($id): void
	{
		/** @var \App\Model\Entity\Appeal */
		$entity = $this->Appeals->getFull($id);
		/** @var \App\Model\Entity\Appeal */
		$entity = $this->Appeals->patchEntity($entity, $this->getRequest()->getData());
		$entity->setCompletedBy($this->currentUser);

		try {
			$this->Appeals->saveOrFail($entity, [
				'associated' => [],
			]);
			$entity = $this->Appeals->getFull($entity->id);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Delete method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When rules check fails.
	 */
	public function delete($id): void
	{
		/** @var \App\Model\Entity\Appeal */
		$entity = $this->Appeals->getFull($id);

		try {
			$this->Appeals->deleteOrFail($entity);
			$this->Log->deleteSuccess($entity);
			$this->set('data', $entity);
			$this->set('success', true);
		} catch (PersistenceFailedException $e) {
			$this->Log->deleteFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Bulk reassign
	 *
	 * @return \App\Controller\Client\Api\count $updated
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function assignAll(): void
	{
		if (empty($this->getRequest()->getData('_ids'))) {
			throw new BadRequestException(__('No IDs were provided for bulk actions.'));
		}

		$assignedUser = null;
		if (!empty($this->getRequest()->getData('assigned_to'))) {
			$assignedUser = $this->Appeals
				->AssignedToUser
				->get($this->getRequest()->getData('assigned_to'));
		}

		$entities = $this->Appeals->updateAll(
			[  // fields
				'assigned_to' => $this->getRequest()->getData('assigned_to'),
			],
			[  // conditions
				'id IN' => $this->getRequest()->getData('_ids'),
			]
		);

		if (!empty($assignedUser)) {
			Log::warning(__(
				'User #{0} `{1}` bulk reassigned {2} appeal(s) to user #{3} `{4}`. IDs: {5}',
				$this->currentUser->id,
				$this->currentUser->full_name,
				$entities,
				$assignedUser->id,
				$assignedUser->full_name,
				implode(', ', $this->getRequest()->getData('_ids'))
			), 'general');
		} else {
			Log::warning(__(
				'User #{0} `{1}` bulk reassigned {2} appeal(s) to the open queue. IDs: {3}',
				$this->currentUser->id,
				$this->currentUser->full_name,
				$entities,
				implode(', ', $this->getRequest()->getData('_ids'))
			), 'general');
		}

		$this->set('data', $entities);
	}
}
