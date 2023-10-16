<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Service\DocumentServiceInterface;
use App\Service\StorageServiceInterface;
use Cake\I18n\FrozenTime;

/**
 * Documents Controller
 */
class DocumentsController extends ApiController
{
	/**
	 * Get test document as PDF
	 *
	 * @return void
	 */
	public function test(): void
	{
		$this->request->allowMethod(['get']);

		$this->viewBuilder()->setClassName('CakePdf.Pdf');

		$this->set([
			'phpVersion' => phpversion(),
			'time' => new FrozenTime(),
			'message' => 'If you can read this, variables are successfully being set.'
		]);
	}

	/**
	 * Test merging documents together
	 *
	 * Provide an array of file paths as `files` such as:
	 * ["file1.pdf", "file2.pdf"]
	 *
	 * Provide a string output filename including extension:
	 * name: "output.pdf"
	 *
	 * Currently just uses the incoming documents container.
	 *
	 * @return void
	 */
	public function merge(
		DocumentServiceInterface $documentService,
		StorageServiceInterface $storageService
	): void {
		$this->request->allowMethod(['POST', 'PUT', 'PATCH']);

		/** @var string[] */
		$inputFiles = $this->request->getData('files') ?? [];

		/** @var string */
		$outputPath = $this->request->getData('name') ?? '';

		$documentService->mergePdfs(
			$storageService->incomingDocuments(),
			$inputFiles,
			$storageService->incomingDocuments(),
			$outputPath
		);
	}
}
