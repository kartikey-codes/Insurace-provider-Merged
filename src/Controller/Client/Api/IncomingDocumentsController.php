<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Lib\StorageUtility\StorageUtility;
use App\Lib\TenantUtility\TenantUtility;
use App\Service\StorageServiceInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;

/**
 * Incoming Documents Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\DownloadComponent $Download
 * @property \App\Controller\Component\FileValidationComponent $FileValidation
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Controller\Component\UploadComponent $Upload
 * @property \App\Model\Table\IncomingDocumentsTable $IncomingDocuments
 */
class IncomingDocumentsController extends ApiController
{
	public bool $deepCheckPdfs = true;

	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_DEFAULT_LIMIT,
		'order' => [
			'created' => 'asc',
		],
		'sortableFields' => [
			'created',
			'modified',
			'deleted',
			'facility_id',
			'case_id',
			'file_name',
			'assigned',
			'assigned_to',
			'appeal_id',
			'original_name',
			'unable_to_complete'
		],
		'contain' => [
			'Facilities' => [
				'FacilityTypes',
			],
			'AssignedToUser',
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

		$this->loadComponent('Download');
		$this->loadComponent('Upload');
		$this->loadComponent('FileValidation');
	}

	/**
	 * Add method
	 *
	 * @return void
	 * @throws \App\Exception\FileUploadException When file is not valid
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function add(StorageServiceInterface $storage): void
	{
		$uploadedFiles = $this->getRequest()->getUploadedFiles();
		$clientId = TenantUtility::getTenantIdFromRequest();

		if (empty($uploadedFiles) || empty($uploadedFiles['files'])) {
			throw new BadRequestException(__('No valid file uploads were provided. Your upload size may be too large.'));
		}

		$entities = [];

		/** @var \Laminas\Diactoros\UploadedFile */
		foreach ($uploadedFiles['files'] as $uploadedFile) {
			// Ensure this file is a valid PDF (deep check)
			$this->FileValidation->assertPdf($uploadedFile, $this->deepCheckPdfs);

			// Create a new entity
			/** @var \App\Model\Entity\IncomingDocument */
			$entity = $this->IncomingDocuments->newEntity($this->getRequest()->getData());
			$entity->client_id = $clientId;

			// Assign the document to the user who uploaded it
			$entity->setAssignedTo($this->currentUser);

			// Create a unique filename based on the time and hash of the filename
			$clientFileName = $uploadedFile->getClientFilename();
			$newName = StorageUtility::generateFileName($clientFileName) . '.pdf';

			// Save the new filename to the entity
			$entity->set('file_name', $newName);
			$entity->set('original_name', $clientFileName);

			// Attempt upload to persistent storage
			$this->Upload->upload(
				$storage->incomingDocuments(),
				$newName,
				$uploadedFile,
				$uploadedFile->getClientMediaType()
			);

			// Save the document record to the database
			$entities[] = $this->IncomingDocuments->saveOrFail($entity);
		}

		$this->set('data', $entities);
	}

	/**
	 * View method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function view($id): void
	{
		$this->set('data', $this->IncomingDocuments->getFull($id));
	}

	/**
	 * Download method
	 *
	 * Download a copy of the incoming document
	 *
	 * @param int $id Incoming document ID.
	 * @return \Cake\Http\Response
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function download($id, StorageServiceInterface $storage): Response
	{
		$this->getRequest()->allowMethod(['GET']);

		$entity = $this->IncomingDocuments->get($id, [
			'skipTenantCheck' => $this->currentUser->isAdmin(),
		]);

		$path = $entity->file_name;

		return $this->Download->downloadFile(
			$path,
			$storage->incomingDocuments()->fileSize($path),
			$storage->incomingDocuments()->readStream($path)
		);
	}

	/**
	 * Preview method
	 *
	 * Return the file in its original mimetype (i.e. render pdf in browser)
	 *
	 * @param int $id Incoming document ID.
	 * @return \Cake\Http\Response
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function preview($id, StorageServiceInterface $storage): Response
	{
		$this->getRequest()->allowMethod(['GET']);

		$entity = $this->IncomingDocuments->get($id, [
			'skipTenantCheck' => $this->currentUser->isAdmin(),
		]);

		$path = $entity->file_name;

		return $this->Download->previewFile(
			$path,
			$storage->incomingDocuments()->fileSize($path),
			$storage->incomingDocuments()->readStream($path),
			StorageUtility::getMimeTypeFromFilePath($path)
		);
	}

	/**
	 * Index method
	 *
	 * @return void
	 */
	public function index(): void
	{
		// Build Query
		$query = $this->IncomingDocuments->find('new');
		$this->crudIndex($query);
	}

	/**
	 * Count method
	 *
	 * @return void
	 */
	public function count(): void
	{
		$this->set('new', $this->IncomingDocuments->find('new')->count());
	}

	/**
	 * Edit method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function edit($id): void
	{
		$entity = $this->IncomingDocuments->get($id);
		$entity = $this->IncomingDocuments->patchEntity($entity, $this->getRequest()->getData());
		$this->IncomingDocuments->saveOrFail($entity);
		$entity = $this->IncomingDocuments->getFull($entity->id);
		$this->set('data', $entity);
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
		/** @var \App\Model\Entity\IncomingDocument */
		$entity = $this->IncomingDocuments->get($id);

		$userId = $this->getRequest()->getData('user_id');

		if (!empty($userId)) {
			$user = $this->IncomingDocuments->AssignedToUser->get($userId);
			$entity->setAssignedTo($user);
		} else {
			$entity->set('assigned', new FrozenTime());
			$entity->set('assigned_to', null);
		}

		$this->IncomingDocuments->saveOrFail($entity);
		$entity = $this->IncomingDocuments->getFull($id);
		$this->set('data', $entity);
	}

	/**
	 * Attach Case method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function attachCase($id, StorageServiceInterface $storage): void
	{
		$caseId = $this->getRequest()->getData('case_id');

		if (empty($caseId)) {
			throw new BadRequestException(__('A case ID must be provided'));
		}

		$entity = $this->IncomingDocuments->get($id);
		$entity->set('case_id', $caseId);
		$entity->set('appeal_id', null);

		// Copy to case files
		$newFileName = $entity->original_name ?: $entity->file_name;
		$newPath = $caseId . '/' . $newFileName;
		$originalContents = $storage->incomingDocuments()->readStream($entity->file_name);
		$storage->cases()->writeStream($newPath, $originalContents);

		$this->IncomingDocuments->saveOrFail($entity);
		$entity = $this->IncomingDocuments->getFull($id);

		$this->set('data', $entity);
	}

	/**
	 * Detach From Case method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function detachCase($id): void
	{
		$entity = $this->IncomingDocuments->get($id);
		$entity->set('case_id', null);
		$entity->set('appeal_id', null);

		$this->IncomingDocuments->saveOrFail($entity);
		$entity = $this->IncomingDocuments->getFull($id);

		$this->set('data', $entity);
	}

	/**
	 * Attach Appeal method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function attachAppeal($id, StorageServiceInterface $storage): void
	{
		$appealId = $this->getRequest()->getData('appeal_id');

		if (empty($appealId)) {
			throw new BadRequestException(__('An appeal ID must be provided'));
		}

		$appeal = $this->IncomingDocuments->Appeals->get($appealId);

		$entity = $this->IncomingDocuments->get($id);
		$entity->set('appeal_id', $this->getRequest()->getData('appeal_id'));
		$entity->set('case_id', $appeal->case_id);

		// Copy to appeal files
		$newFileName = $entity->original_name ?: $entity->file_name;
		$newPath = $appealId . '/' . $newFileName;
		$originalContents = $storage->incomingDocuments()->readStream($entity->file_name);
		$storage->appeals()->writeStream($newPath, $originalContents);

		$this->IncomingDocuments->saveOrFail($entity);
		$entity = $this->IncomingDocuments->getFull($id);
		$this->set('data', $entity);
	}

	/**
	 * Detach From Appeal method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function detachAppeal($id): void
	{
		$entity = $this->IncomingDocuments->get($id);
		$entity->set('appeal_id', null);
		$entity->set('case_id', null);

		$this->IncomingDocuments->saveOrFail($entity);
		$entity = $this->IncomingDocuments->getFull($id);

		$this->set('data', $entity);
	}

	/**
	 * Delete method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When rules check fails.
	 */
	public function delete($id, StorageServiceInterface $storage): void
	{
		$entity = $this->IncomingDocuments->get($id);

		$this->IncomingDocuments->deleteOrFail($entity, [
			'skipTenantCheck' => $this->currentUser->getOriginalData()->admin,
		]);

		$storage->incomingDocuments()->delete($entity->file_name);

		$this->Log->deleteSuccess($entity);

		$this->set('data', $entity);
		$this->set('success', true);
	}

	/**
	 * Bulk Assign
	 *
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function bulkAssign(): void
	{
		$userId = (int) $this->getRequest()->getData('user_id');
		$documentIds = $this->getRequest()->getData('document_ids', []);

		$this->IncomingDocuments->updateAll([
			'assigned_to' => $userId,
		], [
			'id IN' => $documentIds
		]);

		$this->set('data', true);
	}
}
