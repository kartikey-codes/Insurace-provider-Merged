<?php

declare(strict_types=1);

namespace App\Controller\Vendor\Api;

use App\Lib\StorageUtility\StorageUtility;
use App\Service\StorageServiceInterface;
use Cake\Http\Response;

/**
 * Incoming Documents Controller
 *
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\DownloadComponent $Download
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\IncomingDocumentsTable $IncomingDocuments
 */
class IncomingDocumentsController extends ApiController
{
	/**
	 * @var array
	 */
	public $paginate = [
		'finder' => 'new',
		'limit' => PAGINATION_LOW_LIMIT,
		'order' => [
			'created' => 'asc',
		],
		'sortableFields' => [
			'sender_number',
			'file_name',
			'pages',
			'ackowledgement_sent',
			'assigned',
			'created',
			'modified',
			'deleted',
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
	 * Preview method
	 *
	 * Return the file in its original mimetype (i.e. render pdf in browser)
	 *
	 * @param string $id
	 * @return \Cake\Http\Response|void
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
}
