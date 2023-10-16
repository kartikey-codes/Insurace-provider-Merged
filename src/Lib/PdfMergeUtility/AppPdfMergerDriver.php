<?php

declare(strict_types=1);

namespace App\Lib\PdfMergeUtility;

use iio\libmergepdf\Driver\DriverInterface;
use iio\libmergepdf\Source\SourceInterface;
use Exception;
use \TCPDI;

/**
 * Application specific driver for using libmergepdf.
 *
 * TCPDF is outdated using this library and all workarounds
 * seem to have a ton of pitfalls.
 *
 * TCPDF mostly works but will use die() to output an error
 * instead of an exception (in the libmergepdf locked version)
 *
 * Using register_shutdown_function to catch the output seems to
 * break CakePHP from providing a response or handling an
 * exception thrown here.
 *
 * @package App\Lib\PdfMergeUtility
 */
class AppPdfMergerDriver implements DriverInterface
{
	/**
	 * @var TCPDI
	 */
	private $parser;

	/**
	 * Constructor
	 * @param TCPDI|null $parser
	 * @return void
	 */
	public function __construct(TCPDI $parser = null)
	{
		$this->parser = $parser ?: new TCPDI;
	}

	/** @inheritDoc */
	public function merge(SourceInterface ...$sources): string
	{
		// Conform to driver interface and hide any general output errors
		// such as array offsets in TCPDI.

		return @$this->executeMerge(...$sources);
	}

	/**
	 * Execute the actual merging of PDFs
	 * @param SourceInterface $sources
	 * @return string
	 */
	private function executeMerge(SourceInterface ...$sources): string
	{
		$sourceName = '';

		try {
			$parser = clone $this->parser;

			foreach ($sources as $source) {
				$sourceName = $source->getName();

				// FPDI Method
				//$pageCount = $parser->setSourceFile(StreamReader::createByString($source->getContents()));

				// TCPDI Method
				$pageCount = $parser->setSourceData($source->getContents());

				$pageNumbers = $source->getPages()->getPageNumbers() ?: range(1, $pageCount);

				foreach ($pageNumbers as $pageNr) {
					$template = $parser->importPage($pageNr);
					$size = $parser->getTemplateSize($template);

					// Handle both size formats from FPDI/TCPDI
					if (!empty($size[0]) && empty($size['w'])) {
						$size['w'] = $size[0];
					}
					if (!empty($size[1]) && empty($size['h'])) {
						$size['h'] = $size[1];
					}

					$parser->SetPrintHeader(false);
					$parser->SetPrintFooter(false);
					$parser->AddPage(
						$size['w'] > $size['h'] ? 'L' : 'P',
						[$size['w'], $size['h']]
					);
					$parser->useTemplate($template);
				}
			}

			return $parser->Output('', 'S');
		} catch (\Exception $e) {
			throw new Exception("'{$e->getMessage()}' in '$sourceName'", 0, $e);
		}
	}
}
