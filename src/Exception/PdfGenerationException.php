<?php

declare(strict_types=1);

namespace App\Exception;

use Cake\Core\Exception\CakeException;

class PdfGenerationException extends CakeException
{
	// Context data is interpolated into this format string.
	protected $_messageTemplate = 'There was an error attempting to generate a PDF document.';

	// You can set a default exception code as well.
	protected $_defaultCode = 500;
}
