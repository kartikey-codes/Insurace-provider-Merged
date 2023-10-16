<?php

declare(strict_types=1);

namespace App\Exception;

use Cake\Core\Exception\CakeException;

class FileUploadException extends CakeException
{
	// Context data is interpolated into this format string.
	protected $_messageTemplate = 'There was an issue uploading your file. Please try again.';

	// You can set a default exception code as well.
	protected $_defaultCode = 500;
}
