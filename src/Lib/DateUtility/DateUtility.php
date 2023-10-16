<?php

declare(strict_types=1);

namespace App\Lib\DateUtility;

use Cake\Chronos\Chronos;
use Cake\I18n\FrozenDate;
use Cake\I18n\I18nDateTimeInterface;
use DateInterval;
use DatePeriod;

/**
 * Class for handling additional date/time related features not provided by CakePHP
 */
class DateUtility
{
	/**
	 * Get an age number in years
	 *
	 * @param I18nDateTimeInterface|null $date
	 * @return int|null
	 */
	public static function getAgeInYears(?I18nDateTimeInterface $date): ?int
	{
		if (empty($date)) {
			return null;
		}

		if (!$date->isPast()) {
			return 0;
		}

		return $date->diffInYears();
	}

	/**
	 * Return true if the date is the same (excluding year)
	 *
	 * @param I18nDateTimeInterface|null $date
	 * @return null|bool
	 */
	public static function getIsAnniversary(?I18nDateTimeInterface $date): ?bool
	{
		if (empty($date)) {
			return null;
		}

		return $date->I18nFormat('MM-dd') == date('m-d');
	}

	/**
	 * Get next upcoming anniversary of a date
	 *
	 * @param I18nDateTimeInterface $date
	 * @return I18nDateTimeInterface
	 */
	public static function getNextAnniversary(I18nDateTimeInterface $date): I18nDateTimeInterface
	{
		if ($date->isFuture()) {
			return $date;
		}

		$age = self::getAgeInYears($date);
		$nextAnniversary = $date->addYears($age + 1);

		return $nextAnniversary;
	}

	/**
	 * Returns days until next anniversary, or null if unprovided
	 *
	 * @param I18nDateTimeInterface|null $date
	 * @return null|int
	 */
	public static function getDaysUntilAnniversary(?I18nDateTimeInterface $date): ?int
	{
		if (empty($date)) {
			return null;
		}

		if ($date->isFuture()) {
			return null;
		}

		if ($date->isToday()) {
			return 0;
		}

		$nextAnniversary = self::getNextAnniversary($date);

		return $nextAnniversary->diffInDays(null, true);
	}

	/**
	 * Make sure a start date is an instance of the right class
	 *
	 * @param I18nDateTimeInterface|null $startDate
	 * @return I18nDateTimeInterface
	 */
	public static function validateStartDate(?I18nDateTimeInterface $startDate): I18nDateTimeInterface
	{
		if (empty($startDate)) {
			return new FrozenDate('today');
		}

		if (!$startDate instanceof FrozenDate) {
			return new FrozenDate($startDate);
		}

		return $startDate;
	}

	/**
	 * Make sure an end date is an instance of the right class
	 *
	 * @param I18nDateTimeInterface|null $endDate
	 * @return I18nDateTimeInterface
	 */
	public static function validateEndDate(?I18nDateTimeInterface $endDate): I18nDateTimeInterface
	{
		if (empty($endDate)) {
			return new FrozenDate('today');
		}

		if (!$endDate instanceof FrozenDate) {
			return new FrozenDate($endDate);
		}

		return $endDate;
	}

	/**
	 * Return a PHP date period representing a start and end date
	 *
	 * @return \DatePeriod
	 */
	public static function getDatePeriod($startDate, $endDate = null): DatePeriod
	{
		// Validate date range
		$startDate = self::validateStartDate($startDate);
		$endDate = self::validateEndDate($endDate);

		// Get all days in the time period, so we can include zeros in our return value
		$entirePeriod = new DatePeriod(
			$startDate,
			new DateInterval('P1D'),
			$endDate
		);

		return $entirePeriod;
	}

	/**
	 * Get an array of dates between a start and end date
	 *
	 * @return array
	 */
	public static function getDatePeriodArray($startDate, $endDate = null, $format = 'Y-m-d'): array
	{
		$period = self::getDatePeriod($startDate, $endDate);

		$array = [];
		foreach ($period as $day) {
			$array[] = $day->format($format);
		}

		return $array;
	}

	/**
	 * Fill a date array with missing dates between
	 *
	 * @return array
	 */
	public static function fillDateArray($array, $startDate, $endDate = null, $format = 'Y-m-d', $fill = 0): array
	{
		// Validate date range
		$startDate = self::validateStartDate($startDate);
		$endDate = self::validateEndDate($endDate);
		$entirePeriod = self::getDatePeriod($startDate, $endDate);

		// Add zero values in the array
		foreach ($entirePeriod as $day) {
			$dayFormatted = $day->format($format);
			if (empty($array[$dayFormatted])) {
				$array[$dayFormatted] = $fill;
			}
		}

		// Resort the array by keys
		ksort($array);

		return $array;
	}

	/**
	 * Get the date this week starts on (Sunday)
	 *
	 * @return \Cake\I18n\FrozenDate
	 */
	public static function weekStartDay($date = null): FrozenDate
	{
		if (empty($date)) {
			return new FrozenDate('previous Sunday');
		}

		// Check if its sunday
		if ($date->dayOfWeek == 7) {
			return new FrozenDate($date);
		}

		return new FrozenDate($date->modify('previous Sunday'));
	}

	/**
	 * Get the date this week starts on (Sunday)
	 *
	 * @return \Cake\I18n\FrozenDate
	 */
	public static function weekEndDay($date = null): FrozenDate
	{
		if (empty($date)) {
			return new FrozenDate('this Saturday');
		}

		// Check if its saturday
		if ($date->dayOfWeek == 6) {
			return new FrozenDate($date);
		}

		return new FrozenDate($date->modify('this Saturday'));
	}
}
