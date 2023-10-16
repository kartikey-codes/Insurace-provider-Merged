<?php

declare(strict_types=1);

namespace App\Exception;

use Cake\Core\Exception\CakeException;

class StorageServiceInvalidDriverException extends CakeException
{
	// Context data is interpolated into this format string.
	protected $_messageTemplate = 'Storage service provider `%s` was not found.';

	// You can set a default exception code as well.
	protected $_defaultCode = 500;
}
