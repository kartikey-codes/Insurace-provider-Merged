<?php

declare(strict_types=1);

namespace App\Controller\Client\Api;

use App\Controller\Client\Api\ApiController;
use App\Lib\StorageUtility\StorageUtility;
use App\Lib\TenantUtility\TenantUtility;
use App\Service\DocumentServiceInterface;
use App\Service\StorageServiceInterface;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use League\Flysystem\StorageAttributes;
use Exception;

/**
 * Library Files Controller
 *
 * @property \App\Controller\Component\DownloadComponent $Download
 * @property \App\Controller\Component\UploadComponent $Upload
 */
class LibraryFilesController extends ApiController
{
	/**
	 * Directory separator string
	 * @var string
	 */
	private string $dirSeparator;

	/**
	 * Prefix to use for subfolder (client ID)
	 * @var string
	 */
	private string $prefix;

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

		$this->dirSeparator = Configure::readOrFail('Storage.directorySeparator');

		$tenantId = TenantUtility::getTenantIdFromRequest();
		$this->prefix = (string) $tenantId;

		if (empty($this->prefix)) {
			throw new BadRequestException(__('Unable to determine tenant ID for library files'));
		}
	}

	/**
	 * Generate prefixed path to file (i.e. {client_id}/{filename})
	 * @param $path Path to file name
	 * @param $userPrefix Path to folder the user is requesting
	 * @return string
	 */
	public function prefixPath(string $path = '', string $userPrefix = ''): string
	{
		$basePath = $this->prefix . $this->dirSeparator;

		if (!empty($userPrefix)) {
			return $basePath . $userPrefix . $this->dirSeparator . $path;
		}

		return $basePath . $path;
	}

	/**
	 * Index / List Files
	 *
	 * @param StorageServiceInterface $storage App storage service.
	 * @return void
	 * @throws \League\Flysystem\FilesystemException
	 */
	public function index(StorageServiceInterface $storage): void
	{
		$this->getRequest()->allowMethod(['GET']);

		// Allow sub-directories to be queried
		$prefix = $this->getRequest()->getQuery('path', '');
		$path = $this->prefixPath($prefix);

		$contents = $storage->library()
			->listContents($path)
			// Hide base prefix from path
			->map(fn (StorageAttributes $attributes) => $attributes->withPath(
				str_replace($path, '', $attributes->path())
			))
			->toArray();

		$this->set('data', $contents);
	}

	/**
	 * Return the file in its original mimetype (i.e. render pdf in browser)
	 *
	 * @param string $fileName File name.
	 * @param StorageServiceInterface $storage App storage service.
	 * @return \Cake\Http\Response|void
	 * @throws \League\Flysystem\FilesystemException
	 */
	public function preview(string $fileName, StorageServiceInterface $storage): Response
	{
		$this->getRequest()->allowMethod(['GET']);

		// Allow user to specify a subdirectory to look in for library files
		$prefix = $this->getRequest()->getQuery('path', '');

		if (empty($fileName)) {
			throw new BadRequestException(__('Filename was not provided'));
		}

		$path = $this->prefixPath($fileName, $prefix);

		return $this->Download->previewFile(
			$path,
			$storage->library()->fileSize($path),
			$storage->library()->readStream($path),
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
		$prefix = $this->getRequest()->getQuery('path', '');

		if (empty($name)) {
			throw new BadRequestException(__('Filename was not provided'));
		}

		$fullPath = $this->prefixPath($name, $prefix);

		return $this->Download->downloadFile(
			$name,
			$storage->library()->fileSize($fullPath),
			$storage->library()->readStream($fullPath)
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
		$prefix = $this->getRequest()->getData('path', '');

		if (empty($uploadedFiles['files'])) {
			throw new BadRequestException(__('No file uploads were provided'));
		}

		$this->Upload->uploadMultiple(
			$storage->library(),
			$this->prefixPath($prefix),
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
		$prefix = $this->getRequest()->getQuery('path', '');

		if (empty($name)) {
			throw new BadRequestException(__('Filename was not provided'));
		}

		$path = $this->prefixPath($name, $prefix);

		if ($storage->library()->fileExists($path)) {
			$storage->library()->delete($path);
		} else if ($storage->library()->directoryExists($path)) {
			$storage->library()->deleteDirectory($path);
		} else {
			throw new Exception("Path to delete is not a file or directory.");
		}

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
		$prefix = $this->getRequest()->getData('path', '');

		if (empty($fileName)) {
			throw new BadRequestException(__('The original filename was not provided'));
		}

		if (empty($newName)) {
			throw new BadRequestException(__('A new filename was not provided'));
		}

		$storage->library()->move(
			$this->prefixPath($fileName, $prefix),
			$this->prefixPath($newName, $prefix)
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
		$prefix = $this->getRequest()->getData('path', '');

		$filePaths = array_map(function (string $file) use ($prefix) {
			return $this->prefixPath($file, $prefix);
		}, $files);

		$documentService->mergePdfs(
			$storage->library(),
			$filePaths,
			$storage->library(),
			$this->prefixPath($name, $prefix)
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
		$prefix = $this->getRequest()->getData('path', '');

		$filePaths = array_map(function (string $file) use ($prefix) {
			return $this->prefixPath($file, $prefix);
		}, $files);

		$documentService->zipFiles(
			$storage->library(),
			$filePaths,
			$storage->library(),
			$this->prefixPath($name, $prefix)
		);

		$this->set([
			'success' => true,
			'data' => $files,
			'name' => $name
		]);
	}
}
