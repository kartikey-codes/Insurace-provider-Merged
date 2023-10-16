<?php

declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;

class PdfHelper extends Helper
{
	/**
	 * Get absolute path to a webroot asset
	 *
	 * DomPDF needs this to serve images.
	 *
	 * @param string $fileName
	 * @return string
	 */
	public function absoluteImagePath(string $fileName): string
	{
		return WWW_ROOT . 'img' . DS . $fileName;
	}
}
