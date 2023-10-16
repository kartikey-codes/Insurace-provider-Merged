<?php

declare(strict_types=1);

namespace App\Lib\PdfMergeUtility;

use App\Exception\PdfMergerException;
use App\Lib\PdfMergeUtility\AppPdfMergerDriver;
use App\Lib\StorageUtility\StorageUtility;

use iio\libmergepdf\Merger;
use League\Flysystem\FilesystemOperator;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Exception;

/**
 * PDF Merging Utility
 *
 * Not a static utility class and must be instantiated.
 * Uses Flysystem filesystem interfaces for accessing files.
 *
 * Used as a glue between Flysystem filesystems and libpdfmerge.
 *
 * libpdfmerge seems to be abandoned, so this is a
 * hope-for-the-best situation.
 */
class PdfMergeUtility
{
	/**
	 * @var string
	 */
	public string $mimePdf = "application/pdf";

	/**
	 * @var \League\Flysystem\FilesystemOperator
	 */
	public FilesystemOperator $destination;

	/**
	 * @var string
	 */
	public string $destinationPath;

	/**
	 * @var \iio\libmergepdf\Merger
	 */
	private Merger $merger;

	/**
	 * @var array
	 */
	private array $includedFiles = [];

	/**
	 * @var array<string>
	 */
	public array $allowedExtensions = [
		'pdf'
	];

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		// Use an app specific driver so we change implementations.
		// In other words, PHP merging PDFs support is awful.
		$driver = new AppPdfMergerDriver();

		$this->merger = new Merger($driver);
	}

	/**
	 * Set the destination filesystem for merged PDF output
	 *
	 * @param \League\Flysystem\FilesystemOperator $filesystem
	 * @return \App\Lib\PdfMergeUtility\PdfMergeUtility
	 */
	public function setDestination(FilesystemOperator $filesystem): self
	{
		$this->destination = $filesystem;

		return $this;
	}

	/**
	 * Set the destination filename for merged PDF output.
	 * Supply the file name and .pdf extension.
	 *
	 * @param string $fileName
	 * @return \App\Lib\PdfMergeUtility\PdfMergeUtility
	 */
	public function outputAs(string $fileName): self
	{
		$this->destinationPath = StorageUtility::ensureFilenameUsesExtension($fileName, '.pdf');

		return $this;
	}

	/**
	 * Return if a provided file extension can be merged into a PDF.
	 * Mostly to check if input file is PDF. Other extensions may be
	 * supported based on the PDF merge library.
	 *
	 * @param string $extension
	 * @return bool
	 */
	public function isExtensionMergeable(string $extension): bool
	{
		return in_array(strtolower($extension), $this->allowedExtensions);
	}

	/**
	 * Set the source filenames, including path, for merged PDF output.
	 * Overwrites any existing files queued to be merged.
	 *
	 * @param FilesystemOperator $source
	 * @param string $filePath
	 * @return \App\Lib\PdfMergeUtility\PdfMergeUtility
	 */
	public function includeFile(FilesystemOperator $source, string $filePath): self
	{
		$extension = StorageUtility::getFileExtensionFromName($filePath);

		if (!$this->isExtensionMergeable($extension)) {
			throw new PdfMergerException("File extension {$extension} is not supported to merge into a PDF");
		}

		$this->includedFiles[] = [
			'source' => $source,
			'path' => $filePath
		];

		return $this;
	}

	/**
	 * Execute merging the PDFs together
	 *
	 * @return \App\Lib\PdfMergeUtility\PdfMergeUtility
	 */
	public function merge()
	{
		// Read source files into memory
		$this->loadSourceFiles();

		// String contents of the newly merged pdf
		$newPdf = $this->merger->merge();

		if (empty($newPdf)) {
			throw new PdfMergerException("An error occurred merging PDF contents.");
		}

		// Write contents to destination filesystem
		// Mimetype is supplied, but destination filesystem might not do anything with it
		$this->destination->write($this->destinationPath, $newPdf, [
			'mimetype' => 'application/pdf',
		]);

		return $this;
	}

	/**
	 * Loop through source files and add to merger
	 *
	 * @todo Make this less memory intensive. Streams could be used
	 * but libpdfmerge only supports local files and strings.
	 * @return void
	 * @throws \League\Flysystem\UnableToReadFile
	 * @throws \League\Flysystem\FilesystemException
	 */
	private function loadSourceFiles(): void
	{
		$detector = new FinfoMimeTypeDetector();

		foreach ($this->includedFiles as $file) {
			/** @var FilesystemOperator */
			$source = $file['source'];
			/** @var string */
			$path = $file['path'];
			/** @var string */
			$contents = $source->read($path);

			// Ensure loaded file is PDF
			$mimeType = $detector->detectMimeType($path, $contents);
			if ($mimeType !== $this->mimePdf) {
				throw new PdfMergerException("{$path} was detected as {$mimeType} instead of {$this->mimePdf}");
			}

			$this->merger->addRaw($contents);
		}
	}
}
