<?php

declare(strict_types=1);

/**
 * File Validation Component
 *
 * This component handles making sure uploaded files are the correct type
 */

namespace App\Controller\Component;

use App\Exception\FileUploadException;
use Cake\Controller\Component;
use Laminas\Diactoros\UploadedFile;

class FileValidationComponent extends Component
{
	/** @var string */
	public const MIME_PDF = 'application/pdf';

	/**
	 * Assert an uploaded file is a valid PDF
	 *
	 * @param \Laminas\Diactoros\UploadedFile $file
	 * @param bool $deep Check contents of the file
	 * @throws \App\Controller\Component\UploadedFileException
	 * @return bool
	 */
	public function assertPdf(UploadedFile $file, bool $deep = false): bool
	{
		$fileName = $file->getClientFilename();
		$extension = pathinfo($fileName, PATHINFO_EXTENSION);
		$mediaType = $file->getClientMediaType();

		if (empty($mediaType)) {
			throw new FileUploadException(__('Uploaded file "{0}" does not have a valid media type.', $fileName));
		}

		if ($extension !== 'pdf') {
			throw new FileUploadException(__('Uploaded file "{0}" is not a PDF document. Please either convert the file or upload a different document.', $fileName));
		}

		if ($mediaType !== self::MIME_PDF) {
			throw new FileUploadException(__('Uploaded file "{0}" does not appear to be a valid PDF file.', $fileName));
		}

		// Check contents of the PDF
		if ($deep) {
			return $this->deepCheckPdf($file);
		}

		return true;
	}

	/**
	 * Attempt to read an uploaded file's contents and ensure it is a valid PDF
	 *
	 * @param \Laminas\Diactoros\UploadedFile $file
	 * @return void
	 */
	private function deepCheckPdf(UploadedFile $file): bool
	{
		$fileName = $file->getClientFilename();
		$stream = $file->getStream();
		$firstBytes = $stream->read(10);

		// Have to rewind or the file gets corrupted when moving
		$stream->rewind();

		// Look for magic 'PDF' string in the first bytes
		$foundPdfMarker = strpos($firstBytes, '%PDF');
		if ($foundPdfMarker === false) {
			throw new FileUploadException(__('File "{0}" does not contain a valid PDF marker, and may be a different type of file than specified. Please ensure the file is actually a valid PDF.', $fileName));
		}

		return true;
	}
}
