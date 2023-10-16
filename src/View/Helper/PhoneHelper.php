<?php

declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;

class PhoneHelper extends Helper
{
	/**
	 * Format a phone number
     *
     * @param string $data
	 * @return string
	 */
	public function format(string $data): string
	{
		return '(' . substr($data, 0, 3) . ') ' . substr($data, 3, 3) . '-' . substr($data, 6);
	}
}
