<?php

declare(strict_types=1);

namespace App\Lib\StorageUtility;

use App\Exception\FileUploadException;
use League\MimeTypeDetection\FinfoMimeTypeDetector;

/**
 * Storage Utility
 *
 * This class is used for generic file supporting functions
 * such as determining mime types or handling upload error codes.
 *
 * @package App\Storage\Utility
 */
class StorageUtility
{
	/**
	 * List of common file extensions to known mimetypes.
	 * Primarily used when mime detecting functions fail.
	 *
	 * @var array
	 */
	private static array $commonExtensionMimeTypes = [
		'pdf' => 'application/pdf',
		'xps' => 'application/vnd.ms-xpsdocument',
	];

	/**
	 * Exception Messages
	 */
	private const INVALID_FORMAT = 'Unable to find any uploaded files';
	private const INVALID_UPLOAD = 'File upload is not in the correct format';
	private const UPLOAD_TOO_LARGE = 'File `{0}` exceeded the maximum allowed size';
	private const UPLOAD_PARTIAL = 'Uploaded file `{0}` was only partially received';
	private const NO_TMP_DIR = 'The server is missing a temporary folder for receiving file uploads';
	private const CANT_WRITE = 'The server failed to write the uploaded file to storage';
	private const EXTENSION_ERROR = 'The server has an extension installed that has blocked the file upload';

	/**
	 * Validate that an uploaded file was ok and received correctly
	 *
	 * @param array $file Array from a PHP file upload.
	 * @return bool
	 * @throws \App\Exception\FileUploadException
	 */
	public static function validateUploadedFile(array $file = []): bool
	{
		$fileName = h($file['name']);

		// Ensure $file is an array
		if (!is_array($file)) {
			throw new FileUploadException(__(self::INVALID_FORMAT));
		}

		// Not a valid PHP upload
		if (!isset($file['error'])) {
			throw new FileUploadException(__(self::INVALID_UPLOAD));
		}

		// Check PHP error and throw exceptions
		switch ($file['error']) {
				// Return true if everything went ok
			case UPLOAD_ERR_OK:
				return true;
				break;

				// File size limit
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new FileUploadException(__(self::UPLOAD_TOO_LARGE, $fileName));
				break;

				// Partial upload
			case UPLOAD_ERR_PARTIAL:
				throw new FileUploadException(__(self::UPLOAD_PARTIAL, $fileName));
				break;

				// No temporary directory
			case UPLOAD_ERR_NO_TMP_DIR:
				throw new FileUploadException(__(self::NO_TMP_DIR));
				break;

				// Cannot write
			case UPLOAD_ERR_CANT_WRITE:
				throw new FileUploadException(__(self::CANT_WRITE));
				break;

				// Cannot write
			case UPLOAD_ERR_EXTENSION:
				throw new FileUploadException(__(self::EXTENSION_ERROR));
				break;
		}

		// Default to false if not ok
		return false;
	}

	/**
	 * Validate MIME Type to use by file extension
	 *
	 * Pass an extension and file path to double check and enforce some custom
	 * rules about mime types. Sometimes XPS documents were interpreted as ZIP files.
	 *
	 * @param string $path The path to the file's data so mime_content_type can take a shot
	 * @param string $originalName The filename
	 * @return string
	 */
	public static function getMimeTypeFromFilePath(string $path, $originalName = null): string
	{
		// If no original name is passed, used the path
		if (empty($originalName)) {
			$originalName = basename($path);
		}

		// Check with league-mime-type-detection
		$detector = new FinfoMimeTypeDetector();
		$detectedMimetype = $detector->detectMimeTypeFromPath($originalName);
		if (!empty($detectedMimetype)) {
			return $detectedMimetype;
		}

		// Extract just the file extension from the original filename (in lower-case)
		$extension = strtolower(
			self::getFileExtensionFromName($originalName)
		);

		// Empty extension means its just a download
		if (empty($extension)) {
			return 'application/octet-stream';
		}

		// Check our internal list of well-known mimetypes
		if (!empty(self::$commonExtensionMimeTypes[$extension])) {
			return self::$commonExtensionMimeTypes[$extension];
		}

		// Lastly, guess from the mime_content_type function
		try {
			return mime_content_type($path);
		} catch (\Error $e) {
			// Nothing
		}

		// Return false if we just can't figure it out somehow
		return false;
	}

	/**
	 * Get a file name without extension
	 *
	 * @param string $fileName The name of the file to check
	 * @return string
	 */
	public static function getFileNameWithoutExtension(string $fileName): string
	{
		return pathinfo($fileName, PATHINFO_FILENAME);
	}

	/**
	 * Get a file extension from the file name
	 *
	 * @param string $fileName The name of the file to check
	 * @return string
	 */
	public static function getFileExtensionFromName(string $fileName): string
	{
		return pathinfo($fileName, PATHINFO_EXTENSION);
	}

	/**
	 * Generate a unique filename to avoid storage collisions.
	 *
	 * @param string $fileName
	 * @return string
	 */
	public static function generateFileName(string $fileName): string
	{
		return date('Y-m-d') . 'T' . date('H-i-s') . '_' . md5($fileName);
	}

	/**
	 * Ensure a user-provided filename ends with the correct file extension
	 *
	 * @param string $fileName The name of the file to check
	 * @param string $extension The desired extension (include .)
	 * @return string
	 */
	public static function ensureFilenameUsesExtension(string $fileName, string $extension): string
	{
		if (str_ends_with($fileName, $extension)) {
			return $fileName;
		}

		return $fileName . $extension;
	}
}
