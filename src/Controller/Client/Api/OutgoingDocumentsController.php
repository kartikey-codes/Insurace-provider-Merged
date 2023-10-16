<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Lib\StorageUtility\StorageUtility;
use App\Lib\TenantUtility\TenantUtility;
use App\Model\Entity\OutgoingDocument;
use App\Service\StorageServiceInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Outgoing Documents Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\DownloadComponent $Download
 * @property \App\Controller\Component\FileValidationComponent $FileValidation
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Controller\Component\UploadComponent $Upload
 * @property \App\Model\Table\OutgoingDocumentsTable $OutgoingDocuments
 */
class OutgoingDocumentsController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'search',
		'limit' => PAGINATION_LOW_LIMIT,
		'order' => [
			'created' => 'desc',
		],
		'sortableFields' => [
			'filename',
			'delivery_method',
			'completed',
			'failed',
			'status_message',
			'created',
			'modified',
			'deleted',
			// Associations
			'Agency.name'
		],
		'contain' => [
			'Cases' => [
				'Patients'
			],
			'Appeals' => [
				'AppealLevels'
			],
			'Agencies' => [
				'OutgoingProfile'
			],
			'CreatedByUser'
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
	 * Index method
	 *
	 * @return void
	 */
	public function index(): void
	{
		$this->crudIndex();
	}

	/**
	 * Count method
	 *
	 * @return void
	 */
	public function count(): void
	{
		$this->set([
			'new' => $this->OutgoingDocuments->find('new')->count()
		]);
	}

	/**
	 * Download method
	 *
	 * Download a copy of the outgoing document
	 *
	 * @param int $id Incoming document ID.
	 * @return \Cake\Http\Response
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 */
	public function download($id, StorageServiceInterface $storage): Response
	{
		$this->getRequest()->allowMethod(['GET']);

		$entity = $this->OutgoingDocuments->get($id, [
			'skipTenantCheck' => $this->currentUser->isAdmin(),
		]);

		$path = $entity->filename;

		return $this->Download->downloadFile(
			$entity->filename,
			$storage->appealPackets()->fileSize($path),
			$storage->appealPackets()->readStream($path)
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

		$entity = $this->OutgoingDocuments->get($id, [
			'skipTenantCheck' => $this->currentUser->isAdmin(),
		]);

		$path = $entity->filename;

		return $this->Download->previewFile(
			$path,
			$storage->appealPackets()->fileSize($path),
			$storage->appealPackets()->readStream($path),
			StorageUtility::getMimeTypeFromFilePath($path)
		);
	}

	/**
	 * Cancel method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function cancel($id): void
	{
		$this->request->allowMethod(['POST']);

		$entity = $this->OutgoingDocuments->get($id);

		try {
			$entity->set([
				'status_message' => OutgoingDocument::STATUS_CANCELLED,
				'processed' => new FrozenTime(),
				'cancelled' => new FrozenTime(),
				'cancelled_by' => $this->currentUser->id
			]);
			$this->OutgoingDocuments->saveOrFail($entity);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Mark Delivered method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function delivered($id): void
	{
		$this->request->allowMethod(['POST']);

		$entity = $this->OutgoingDocuments->get($id);

		try {
			$entity->set([
				'status_message' => OutgoingDocument::STATUS_DELIVERED,
				'processed' => new FrozenTime(),
				'completed' => new FrozenTime(),
				'cancelled' => null,
				'cancelled_by' => null
			]);
			$this->OutgoingDocuments->saveOrFail($entity);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}

	/**
	 * Retry method
	 *
	 * @param string $id
	 * @return void
	 * @throws \Cake\Network\Exception\NotFoundException When record not found.
	 * @throws \Cake\ORM\Exception\PersistenceFailedException When not valid.
	 */
	public function retry($id): void
	{
		$this->request->allowMethod(['POST']);

		/** @var \App\Model\Entity\OutgoingDocument */
		$entity = $this->OutgoingDocuments->get($id);

		if (!$entity->isRetryable()) {
			throw new BadRequestException(__('Cannot retry sending this document'));
		}

		try {
			$entity->set([
				'status_message' => OutgoingDocument::STATUS_NEW,
				'processed' => null,
				'completed' => null,
				'failed' => null,
				'cancelled' => null,
				'cancelled_by' => null
			]);
			$this->OutgoingDocuments->saveOrFail($entity);
			$this->set('data', $entity);
		} catch (PersistenceFailedException $e) {
			$this->Log->saveFailed($e, $entity);
			$this->setResponse($this->ApiError->entity($e, $entity));
		}
	}
}
