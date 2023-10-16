<?php

declare(strict_types=1);

namespace App\Lib\TimeUtility;

use Cake\I18n\I18nDateTimeInterface;

/**
 * Class for dealing with time based data
 */
class TimeUtility
{
	/**
	 * Return time ago in seconds
	 *
	 * @param null|I18nDateTimeInterface $time
	 * @return null|int
	 */
	public static function getTimeAgoInSeconds(?I18nDateTimeInterface $time): ?int
	{
		if (is_null($time) || empty($time)) {
			return null;
		}

		if (!$time instanceof I18nDateTimeInterface) {
			return null;
		}

		return $time->diffInSeconds();
	}
}
