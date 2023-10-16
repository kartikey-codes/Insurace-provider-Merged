<?php

declare(strict_types=1);

namespace App\Service;

use League\Flysystem\Filesystem;

/**
 * Document Service Provider Interface
 */
interface DocumentServiceInterface
{
	/**
	 * Render a view as a PDF file
	 *
	 * @param mixed $template CakePHP view template name
	 * @param array $viewVars Variables available to the view template
	 * @param ?string $layout The layout to use
	 * @throws \Exception When an input argument is empty
	 * @throws \App\Exception\PdfGenerationException When an error occurs
	 * @return string
	 */
	public function renderViewAsPdf(mixed $template, array $viewVars = [], ?string $layout = null): string;

	/**
	 * Merge multiple PDFs into a single one.
	 *
	 * @param \League\Flysystem\Filesystem $source Container where the input files are located
	 * @param array<string> $paths Full paths inside the container to the files to be merged
	 * @param \League\Flysystem\Filesystem $destination Container where the output will be placed
	 * @param string $name The output filename (.pdf will be added automatically)
	 * @throws \Exception When an input argument is empty
	 * @throws \App\Exception\PdfMergerException When an error occurs in the PDF merger driver
	 * @return void
	 */
	public function mergePdfs(Filesystem $source, array $paths, Filesystem $destination, string $name): void;

	/**
	 * Merge multiple PDFs from differing filesystems into a single one.
	 * Used for ordered PDF merging from multiple different sources.
	 *
	 * @param array<string> $files Requires a 'source' Filesystem key and a 'path' to the file inside
	 * @param \League\Flysystem\Filesystem $destination Container where the output will be placed
	 * @param string $name The output filename (.pdf will be added automatically)
	 * @throws \Exception When an input argument is empty
	 * @throws \App\Exception\PdfMergerException When an error occurs in the PDF merger driver
	 * @return void
	 */
	public function mergeMultipleSourcePdfs(array $files, Filesystem $destination, string $name): void;

	/**
	 * Combine multiple files into a .zip file.
	 *
	 * @param \League\Flysystem\Filesystem $source Container where the input files are located
	 * @param array<string> $files Full paths inside the container to the files to be merged
	 * @param \League\Flysystem\Filesystem $destination Container where the output will be placed
	 * @param string $name The output filename (.zip will be added automatically)
	 * @throws \Exception When an input argument is empty
	 * @throws \App\Exception\FileZipException When an error occurs zipping files
	 * @return void
	 */
	public function zipFiles(Filesystem $source, array $files, Filesystem $destination, string $name): void;
}
