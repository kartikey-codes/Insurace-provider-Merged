<?php

declare(strict_types=1);

namespace App\Exception;

use Cake\Core\Exception\CakeException;

class PermissionDeniedException extends CakeException
{
	// Context data is interpolated into this format string.
	protected $_messageTemplate = 'You do not have the required permissions to complete your request';

	// You can set a default exception code as well.
	protected $_defaultCode = 500;
}
