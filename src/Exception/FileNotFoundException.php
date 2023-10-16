<?php

declare(strict_types=1);

namespace App\Exception;

use Cake\Core\Exception\CakeException;

class FileNotFoundException extends CakeException
{
	// Context data is interpolated into this format string.
	protected $_messageTemplate = 'Seems that %s is missing.';

	// You can set a default exception code as well.
	protected $_defaultCode = 404;
}
