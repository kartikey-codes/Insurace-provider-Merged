<?php

declare(strict_types=1);

namespace App\Exception;

use Cake\Http\Exception\InternalErrorException;

class FileEmptyDownloadException extends InternalErrorException
{
	// Context data is interpolated into this format string.
	protected $_messageTemplate = 'Attempting to download a file with missing file contents.';

	// You can set a default exception code as well.
	protected $_defaultCode = 500;
}
