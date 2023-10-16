<?php

declare(strict_types=1);

namespace App\Controller\Client\Api\Appeals;

use App\Controller\Client\Api\ApiController;
use App\Exception\PdfGenerationException;
use App\Exception\PdfMergerException;
use App\Model\Entity\Appeal;
use App\Model\Entity\OutgoingDocument;
use App\Model\Table\AppealsTable;
use App\Service\DocumentServiceInterface;
use App\Service\StorageServiceInterface;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Http\Response;
use Exception;

/**
 * Appeal Packets Controller
 *
 * @property \App\Controller\Component\DownloadComponent $Download
 * @property \App\Controller\Component\ApiErrorComponent $ApiError
 * @property \App\Controller\Component\LogComponent $Log
 * @property \App\Model\Table\AppealsTable $Appeals
 */
class AppealPacketsController extends ApiController
{
	use LocatorAwareTrait;

	public const SOURCE_APPEAL = 'Appeal';
	public const SOURCE_CASE = 'Case';

	/**
	 * @var \App\Model\Table\AppealsTable
	 */
	public AppealsTable $Appeals;

	/**
	 * @var int
	 */
	public int $appealId;

	/**
	 * @var \App\Model\Entity\Appeal
	 */
	public Appeal $appeal;

	/**
	 * @var string
	 */
	public string $fileName;

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

		$this->Appeals = $this->fetchTable('Appeals');

		$this->appealId = intval($this->getRequest()->getParam('appeal_id'));

		$this->appeal = $this->Appeals->get($this->appealId, [
			'finder' => 'full',
			'skipTenantCheck' => $this->currentUser->isAdmin(),
		]);

		$this->fileName = $this->appeal->id . '.pdf';
	}

	/**
	 * Download packet method
	 *
	 * @return Response
	 * @throws \Cake\Core\Exception\CakeException When record not found.
	 */
	public function download(StorageServiceInterface $storageService): Response
	{
		$this->getRequest()->allowMethod(['GET']);

		if (!$storageService->appealPackets()->fileExists($this->fileName)) {
			throw new \Exception(__("Packet has not been generated for this appeal"));
		}

		return $this->Download->downloadFile(
			$this->fileName,
			$storageService->appealPackets()->fileSize($this->fileName),
			$storageService->appealPackets()->readStream($this->fileName)
		);
	}

	/**
	 * Packet exists method
	 *
	 * @return Response
	 * @throws \Cake\Core\Exception\CakeException When record not found.
	 */
	public function exists(StorageServiceInterface $storageService): void
	{
		$this->getRequest()->allowMethod(['GET']);

		$exists = $storageService->appealPackets()->fileExists($this->fileName);

		$this->set(compact('exists'));
	}

	/**
	 * Generate packet method
	 *
	 * @param int $id Primary Key.
	 * @return void
	 * @throws \Cake\Core\Exception\CakeException When record not found.
	 */
	public function generate(DocumentServiceInterface $documentService, StorageServiceInterface $storageService): void
	{
		$this->getRequest()->allowMethod(['POST']);

		$selectedCaseFiles = $this->request->getData('case_files', []);
		$selectedAppealFiles = $this->request->getData('appeal_files', []);
		$orderedList = $this->request->getData('ordered_list', []);

		if (!empty($orderedList)) {
			$this->generatePacketOrdered(
				$documentService,
				$storageService,
				$orderedList
			);
		} else {
			$this->generatePacketUnordered(
				$documentService,
				$storageService,
				$selectedCaseFiles,
				$selectedAppealFiles
			);
		}

		$this->set([
			'filename' => $this->fileName,
			'case_files' => $selectedCaseFiles,
			'appeal_files' => $selectedAppealFiles,
			'ordered_list' => $orderedList,
			'success' => true
		]);
	}

	/**
	 * New code for taking ordered list of files to merge together
	 *
	 * @param DocumentServiceInterface $documentService
	 * @param StorageServiceInterface $storageService
	 * @param array $fileList
	 * @return void
	 * @throws Exception
	 * @throws PdfMergerException
	 */
	protected function generatePacketOrdered(DocumentServiceInterface $documentService, StorageServiceInterface $storageService, array $fileList): void
	{
		$mergeFiles = [];

		// Contains 'source' filesystem key and 'file' path inside
		foreach ($fileList as $file) {
			switch ($file['source']) {
				case self::SOURCE_CASE:
					$mergeFiles[] = [
						'source' => $storageService->cases(),
						'path' => $this->appeal->case_id . '/' . $file['file']
					];
					break;
				case self::SOURCE_APPEAL:
					$mergeFiles[] = [
						'source' => $storageService->appeals(),
						'path' => $this->appeal->id . '/' . $file['file']
					];
					break;
				default:
					throw new Exception(__("Invalid file source {0}", $file['source']));
					break;
			}
		}

		$documentService->mergeMultipleSourcePdfs(
			$mergeFiles,
			$storageService->appealPackets(),
			$this->fileName
		);
	}

	/**
	 * Old code for generating a packet without any file order considerations
	 * @todo Remove this when drag and drop ordering is working well
	 *
	 * @param DocumentServiceInterface $documentService
	 * @param StorageServiceInterface $storageService
	 * @param array $caseFiles
	 * @param array $appealFiles
	 * @return void
	 * @throws Exception
	 * @throws PdfMergerException
	 */
	protected function generatePacketUnordered(DocumentServiceInterface $documentService, StorageServiceInterface $storageService, array $caseFiles, array $appealFiles): void
	{
		$finalParts = [];

		/**
		 * Combine case files into single PDF
		 * @var array
		 */
		if (!empty($caseFiles)) {
			$casePaths = array_map(function ($caseFile) {
				return $this->appeal->case_id . '/' . $caseFile;
			}, $caseFiles);

			$caseFile = $this->appeal->id . '_case.pdf';

			$documentService->mergePdfs(
				$storageService->cases(),
				$casePaths,
				$storageService->appealPackets(),
				$caseFile
			);
		}

		/**
		 * Combine appeal files into single PDF
		 * @var array
		 */
		if (!empty($appealFiles)) {
			$appealPaths = array_map(function ($appealFile) {
				return $this->appeal->id . '/' . $appealFile;
			}, $appealFiles);

			$appealFile = $this->appeal->id . '_appeal.pdf';

			$documentService->mergePdfs(
				$storageService->appeals(),
				$appealPaths,
				$storageService->appealPackets(),
				$appealFile
			);
		}

		/**
		 * Merge finalized PDF
		 */

		if (!empty($caseFiles)) {
			$finalParts[] = $caseFile;
		}
		if (!empty($appealFiles)) {
			$finalParts[] = $appealFile;
		}

		/** Merge final part of generate if nothing selected */
		if (!empty($finalParts)) {
			$documentService->mergePdfs(
				$storageService->appealPackets(),
				$finalParts,
				$storageService->appealPackets(),
				$this->fileName
			);
		}
	}

	/**
	 * Submit packet method
	 *
	 * @return void
	 * @throws \Cake\Core\Exception\CakeException When record not found.
	 */
	public function submit(DocumentServiceInterface $documentService, StorageServiceInterface $storageService): void
	{
		$this->getRequest()->allowMethod(['POST']);

		$outgoingDocuments = $this->fetchTable('OutgoingDocuments');

		$outgoing = $outgoingDocuments->newEntity([
			'case_id' => $this->appeal->case_id,
			'appeal_id' => $this->appeal->id,
			'agency_id' => $this->appeal->agency_id ?? null,
			'filename' => $this->appeal->id . '.pdf',
			'delivery_method' => OutgoingDocument::DELIVERY_METHOD_MANUAL,
			'status_message' => OutgoingDocument::STATUS_NEW
		]);

		$outgoingDocuments->saveOrFail($outgoing);

		// Close appeal when submitting
		$this->appeal->setCompletedBy($this->currentUser);
		$this->appeal->setClosedBy($this->currentUser);
		$this->Appeals->save($this->appeal);

		$this->set([
			'success' => true,
			'data' => $outgoing,
			'appeal' => $this->appeal
		]);
	}
}
