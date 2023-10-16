<?php

declare(strict_types=1);

/**
 * Upload Component
 *
 * This component handles file uploads to the application through containers
 */

namespace App\Controller\Component;

use App\Exception\FileUploadException;
use App\Lib\StorageUtility\StorageUtility;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Laminas\Diactoros\UploadedFile;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToWriteFile;

class UploadComponent extends Component
{
	/**
	 * Upload File
	 *
	 * If the file is uploaded as a different name, specify it in the path parameter.
	 *
	 * @param string $container The full path to the file within the container
	 * @param mixed $path Array from a file upload, or string as a path to a file.
	 * @param string $file The mimetype of the file (to potentially be saved as metadata)
	 * @return bool
	 * @throws \App\Exception\FileUploadException
	 */
	public function upload(Filesystem $container, string $path, $file, $mimeType = null): bool
	{
		// UploadedFile object
		if ($file instanceof UploadedFile) {
			return $this->uploadFromObject($container, $path, $file, $mimeType);
		}

		// Array, this is a file upload (temp file)
		if (is_array($file)) {
			return $this->uploadFromTempFile($container, $path, $file, $mimeType);
		}

		$className = get_class($file);
		throw new FileUploadException(__('Unrecognized file upload format type {0}', $className));
	}

	/**
	 * Upload Multiple Files
	 *
	 * Pass array of files and upload all to the same path.
	 *
	 * @param \League\Flysystem\Filesystem $container
	 * @param string $path
	 * @param array $files
	 * @return bool
	 * @throws \League\Flysystem\FilesystemException
	 * @throws \League\Flysystem\UnableToWriteFile
	 */
	public function uploadMultiple(Filesystem $container, string $path, array $files): bool
	{
		if (empty($files)) {
			throw new FileUploadException(__('No files sent or upload too large'));
		}

		$ds = Configure::readOrFail('Storage.directorySeparator');

		foreach ($files as $file) {
			$fileName = $file->getClientFilename();
			$newPath = $path . $ds . $fileName;

			$container->write(
				$newPath,
				$file->getStream()->getContents()
			);
		}

		return true;
	}

	/**
	 * Upload From Temp File
	 *
	 * @param string $container The full path to the file within the container (provider)
	 * @param array $path Array from a file upload
	 * @param null $file The mime type of the file
	 * @return bool
	 * @throws \League\Flysystem\FilesystemException
	 * @throws \League\Flysystem\UnableToWriteFile
	 * @throws \App\Exception\FileUploadException
	 */
	public function uploadFromTempFile(Filesystem $container, string $path, array $file, $mimeType = null): bool
	{
		// Throws FileUploadExceptions on php upload error
		StorageUtility::validateUploadedFile($file);

		$tmpName = $file['tmp_name'];
		$originalName = $file['name'];

		try {
			$stream = fopen($tmpName, 'r+');

			$container->writeStream(
				$path,
				$stream,
				[
					// Config
				]
			);
		} catch (FilesystemException | UnableToWriteFile $e) {
			throw new FileUploadException($e->getMessage());
		} finally {
			if (isset($stream)) {
				fclose($stream);
			}
		}

		return true;
	}

	/**
	 * Upload From Object
	 *
	 * @param string $container The full path to the file within the container
	 * @param \Laminas\Diactoros\UploadedFile $path The file contents
	 * @param string $file The mime type of the file
	 * @return bool
	 */
	public function uploadFromObject(Filesystem $container, string $path, UploadedFile $file, $mimeType = null): bool
	{
		$container->write(
			$path,
			$file->getStream()->getContents(),
			[
				// Config
			]
		);

		return true;
	}
}
