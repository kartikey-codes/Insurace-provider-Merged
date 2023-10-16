<?php

declare(strict_types=1);

namespace App\Exception;

use Cake\Core\Exception\CakeException;

class FileServiceException extends CakeException
{
	// Context data is interpolated into this format string.
	protected $_messageTemplate = 'There was an error attempting to connect to the file provider. This might be a temporary issue. Please try again after a few moments.';

	// You can set a default exception code as well.
	protected $_defaultCode = 500;
}
