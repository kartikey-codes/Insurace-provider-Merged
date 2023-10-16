<?php

declare(strict_types=1);

namespace App\Controller\Client\Api\Appeals;

use App\Controller\Client\Api\ApiController;
use App\Lib\StorageUtility\StorageUtility;
use App\Service\DocumentServiceInterface;
use App\Service\StorageServiceInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use League\Flysystem\FileAttributes;

/**
 * Appeal Files Controller
 *
 * @property \App\Controller\Component\DownloadComponent $Download
 * @property \App\Controller\Component\EntityFilesComponent $EntityFiles
 * @property \App\Controller\Component\UploadComponent $Upload
 */
class AppealFilesController extends ApiController
{
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

		$this->loadComponent('EntityFiles', [
			'table' => 'Appeals',
			'id' => intval($this->getRequest()->getParam('appeal_id')),
			'isAdmin' => $this->currentUser->isAdmin(),
		]);
	}

	/**
	 * Index / List Files
	 *
	 * @return void
	 * @throws \League\Flysystem\FilesystemException
	 */
	public function index(StorageServiceInterface $storage): void
	{
		$this->getRequest()->allowMethod(['GET']);

		$path = $this->EntityFiles->getPrefix();

		$contents = $storage->appeals()
			->listContents($path)
			->toArray();

		$this->set('data', $contents);
	}

	/**
	 * Return the file in its original mimetype (i.e. render pdf in browser)
	 *
	 * @param string $fileName File name.
	 * @return \Cake\Http\Response|void
	 * @throws \League\Flysystem\FilesystemException
	 */
	public function preview(string $fileName, StorageServiceInterface $storage): Response
	{
		$this->getRequest()->allowMethod(['GET']);

		if (empty($fileName)) {
			throw new BadRequestException(__('Filename was not provided'));
		}

		$path = $this->EntityFiles->prefixPath($fileName);

		return $this->Download->previewFile(
			$path,
			$storage->appeals()->fileSize($path),
			$storage->appeals()->readStream($path),
			StorageUtility::getMimeTypeFromFilePath($path)
		);
	}

	/**
	 * Return the file with application/octet stream to force download.
	 *
	 * @param StorageServiceInterface $storage App storage service.
	 * @return \Cake\Http\Response|void
	 * @throws \League\Flysystem\FilesystemException
	 */
	public function download(StorageServiceInterface $storage): Response
	{
		$this->getRequest()->allowMethod(['GET']);

		$name = $this->getRequest()->getQuery('name');

		if (empty($name)) {
			throw new BadRequestException(__('Filename was not provided'));
		}

		$path = $this->EntityFiles->prefixPath($name);

		return $this->Download->downloadFile(
			$path,
			$storage->appeals()->fileSize($path),
			$storage->appeals()->readStream($path)
		);
	}

	/**
	 * Upload files.
	 *
	 * @param StorageServiceInterface $storage App storage service.
	 * @return void
	 * @throws \League\Flysystem\FilesystemException
	 * @throws \League\Flysystem\UnableToWriteFile
	 */
	public function add(StorageServiceInterface $storage): void
	{
		$this->getRequest()->allowMethod(['POST']);

		$uploadedFiles = $this->getRequest()->getUploadedFiles();

		if (empty($uploadedFiles['files'])) {
			throw new BadRequestException(__('No file uploads were provided'));
		}

		$this->Upload->uploadMultiple(
			$storage->appeals(),
			$this->EntityFiles->getPrefix(),
			$uploadedFiles['files']
		);

		$this->set('success', true);
	}

	/**
	 * Remove File
	 *
	 * @param StorageServiceInterface $storage App storage service.
	 * @return void
	 * @throws \League\Flysystem\FilesystemException
	 * @throws \League\Flysystem\UnableToDeleteFile
	 */
	public function delete(StorageServiceInterface $storage): void
	{
		$this->getRequest()->allowMethod(['PATCH', 'POST', 'DELETE']);

		$name = $this->getRequest()->getQuery('name');

		if (empty($name)) {
			throw new BadRequestException(__('Filename was not provided'));
		}

		$path = $this->EntityFiles->prefixPath($name);

		$storage->appeals()->delete($path);

		$this->set('filename', $name);
		$this->set('success', true);
	}

	/**
	 * Rename File
	 *
	 * @param StorageServiceInterface $storage App storage service.
	 * @return void
	 * @throws \League\Flysystem\FilesystemException
	 * @throws \League\Flysystem\UnableToMoveFile
	 */
	public function rename(StorageServiceInterface $storage): void
	{
		$this->getRequest()->allowMethod(['POST', 'PUT', 'PATCH']);

		$fileName = $this->getRequest()->getData('filename');
		$newName = $this->getRequest()->getData('newname');

		if (empty($fileName)) {
			throw new BadRequestException(__('The original filename was not provided'));
		}

		if (empty($newName)) {
			throw new BadRequestException(__('A new filename was not provided'));
		}

		$storage->appeals()->move(
			$this->EntityFiles->prefixPath($fileName),
			$this->EntityFiles->prefixPath($newName)
		);

		$this->set('filename', $newName);
		$this->set('success', true);
	}

	/**
	 * Merge PDFs
	 *
	 * @param StorageServiceInterface $storage App storage service.
	 * @param DocumentServiceInterface $documentService App document service.
	 * @return void
	 * @throws \League\Flysystem\FilesystemException
	 */
	public function merge(
		StorageServiceInterface $storage,
		DocumentServiceInterface $documentService
	): void {
		$this->getRequest()->allowMethod(['POST']);

		$files = $this->getRequest()->getData('files');
		$name = $this->getRequest()->getData('name');

		$filePaths = array_map(function (string $file) {
			return $this->EntityFiles->prefixPath($file);
		}, $files);

		$documentService->mergePdfs(
			$storage->appeals(),
			$filePaths,
			$storage->appeals(),
			$this->EntityFiles->prefixPath($name)
		);

		$this->set([
			'success' => true,
			'data' => $files,
			'name' => $name
		]);
	}

	/**
	 * Create Zip of Appeal Files
	 *
	 * @param StorageServiceInterface $storage App storage service.
	 * @param DocumentServiceInterface $documentService App document service.
	 * @return void
	 * @throws \League\Flysystem\FilesystemException
	 */
	public function zip(
		StorageServiceInterface $storage,
		DocumentServiceInterface $documentService
	): void {
		$this->getRequest()->allowMethod(['POST']);

		$files = $this->getRequest()->getData('files');
		$name = $this->getRequest()->getData('name');

		$filePaths = array_map(function (string $file) {
			return $this->EntityFiles->prefixPath($file);
		}, $files);

		$documentService->zipFiles(
			$storage->appeals(),
			$filePaths,
			$storage->appeals(),
			$this->EntityFiles->prefixPath($name)
		);

		$this->set([
			'success' => true,
			'data' => $files,
			'name' => $name
		]);
	}
}
