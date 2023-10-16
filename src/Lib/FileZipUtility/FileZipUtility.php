<?php

declare(strict_types=1);

namespace App\Lib\FileZipUtility;

use App\Lib\StorageUtility\StorageUtility;
use League\Flysystem\FilesystemOperator;
use ZipArchive;

/**
 * File Zipping Utility
 *
 * Not a static utility class and must be instantiated.
 * Uses Flysystem filesystem interfaces for accessing files.
 *
 * Used to combine multiple files from a flysystem filesystem into
 * a .zip file for easy downloading or storage.
 */
class FileZipUtility
{
	/**
	 * @var \League\Flysystem\FilesystemOperator
	 */
	public FilesystemOperator $destination;

	/**
	 * @var string
	 */
	public string $destinationPath;

	/**
	 * @var \League\Flysystem\FilesystemOperator
	 */
	public FilesystemOperator $source;

	/**
	 * @var array<string>
	 */
	public array $sourcePaths = [];

	/**
	 * @var \ZipArchive
	 */
	private ZipArchive $zip;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->zip = new ZipArchive();
	}

	/**
	 * Set the destination filesystem for merged PDF output
	 *
	 * @param \League\Flysystem\FilesystemOperator $filesystem
	 * @return \App\Lib\FileZipUtility\FileZipUtility
	 */
	public function toDestination(FilesystemOperator $filesystem): self
	{
		$this->destination = $filesystem;

		return $this;
	}

	/**
	 * Set the source filesystem for reading PDFs to be merged
	 *
	 * @param \League\Flysystem\FilesystemOperator $filesystem
	 * @return \App\Lib\FileZipUtility\FileZipUtility
	 */
	public function fromSource(FilesystemOperator $filesystem): self
	{
		$this->source = $filesystem;

		return $this;
	}

	/**
	 * Set the destination filename/path for the zip file
	 * Include the .zip extension in $fileName
	 *
	 * @param string $fileName
	 * @return \App\Lib\FileZipUtility\FileZipUtility
	 */
	public function outputAs(string $fileName): self
	{
		$this->destinationPath = StorageUtility::ensureFilenameUsesExtension($fileName, '.zip');

		return $this;
	}

	/**
	 * Set the source filenames, including path, for .zip output.
	 * Overwrites any existing files queued to be merged.
	 *
	 * @param string $files
	 * @return \App\Lib\FileZipUtility\FileZipUtility
	 */
	public function includeFiles(array $files): self
	{
		$this->sourcePaths = $files;

		return $this;
	}

	/**
	 * Execute zipping up files
	 *
	 * @return \App\Lib\FileZipUtility\FileZipUtility
	 */
	public function zip()
	{
		// Generate temporary file to work in
		$name = tempnam(sys_get_temp_dir(), 'rkz');

		// Open zip file for writing
		$this->zip->open($name, ZipArchive::OVERWRITE);

		// Read source files and add to zip archive
		foreach ($this->sourcePaths as $file) {
			$contents = $this->source->read($file);
			$this->zip->addFromString(basename($file), $contents);
		}

		// Close zip file
		$this->zip->close();

		// Open temp file as resource/stream for writing to flysystem
		$fileStream = fopen($name, 'r');

		// Write contents to destination filesystem
		// Mimetype is supplied, but destination filesystem might not do anything with it
		$this->destination->writeStream($this->destinationPath, $fileStream, [
			'mimetype' => 'application/x-zip',
		]);

		return $this;
	}
}
