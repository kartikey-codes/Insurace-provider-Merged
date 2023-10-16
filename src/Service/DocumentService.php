<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\PdfGenerationException;
use App\Lib\FileZipUtility\FileZipUtility;
use App\Lib\PdfMergeUtility\PdfMergeUtility;
use CakePdf\Pdf\CakePdf;
use Exception;
use League\Flysystem\Filesystem;

/**
 * Document Service Provider
 *
 * Primarily used for compiling documents together, such as merging PDF files or zipping
 * multiple files together.
 *
 * This implementation is for PHP based PDF interaction.
 * Another service provided might be used for Adobe APIs.
 */
class DocumentService implements DocumentServiceInterface
{
	/** @inheritDoc */
	public function renderViewAsPdf(mixed $template, array $viewVars = [], ?string $layout = null): string
	{
		if (empty($template)) {
			throw new PdfGenerationException(__('No view template name was provided.'));
		}

		$CakePdf = new CakePdf();
		$CakePdf->template($template, $layout);
		$CakePdf->viewVars($viewVars);

		return $CakePdf->output();
	}

	/** @inheritDoc */
	public function mergePdfs(Filesystem $source, array $files, Filesystem $destination, string $name): void
	{
		if (empty($files)) {
			throw new Exception(__('No file names provided for merging'));
		}

		if (empty($name)) {
			throw new Exception(__('Mising output file path and name'));
		}

		$merger = new PdfMergeUtility();

		$merger
			->setDestination($destination)
			->outputAs($name);

		foreach ($files as $file) {
			$merger->includeFile($source, $file);
		}

		$merger->merge();
	}

	/** @inheritDoc */
	public function mergeMultipleSourcePdfs(array $files, Filesystem $destination, string $name): void
	{
		$merger = new PdfMergeUtility();

		$merger
			->setDestination($destination)
			->outputAs($name);

		foreach ($files as $file) {
			/**
			 * @var \League\Flysystem\FilesystemOperator $source
			 */
			$source = $file['source'];

			/**
			 * @var string $path
			 */
			$path = $file['path'];

			$merger->includeFile($source, $path);
		}

		$merger->merge();
	}

	/** @inheritDoc */
	public function zipFiles(Filesystem $source, array $files, Filesystem $destination, string $name): void
	{
		$merger = new FileZipUtility();

		$merger
			->includeFiles($files)
			->fromSource($source)
			->toDestination($destination)
			->outputAs($name)
			->zip();
	}
}
