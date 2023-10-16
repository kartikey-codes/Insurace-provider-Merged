<?php

declare(strict_types=1);

namespace App\Lib\PeopleUtility;

/**
 * Class for handling dealing with people's names, genders, marital statuses, etc...
 */
class PeopleUtility
{
	/**
	 * Available Genders
	 *
	 * value => displayName
	 */
	public const GENDERS = [
		'Male' => 'Male',
		'Female' => 'Female',
		'Other' => 'Other'
	];

	/**
	 * Available Marital Statuses
	 *
	 * value => displayName
	 */
	public const MARITAL_STATUSES = [
		'Single' => 'Single',
		'Married' => 'Married',
		'Widow' => 'Widowed',
		'Divorced' => 'Divorced',
		'Separated' => 'Separated'
	];

	/**
	 * Get available genders
	 * @return array
	 */
	public static function getGenders(): array
	{
		return self::GENDERS;
	}

	/**
	 * Get available marital statuses
	 * @return array
	 */
	public static function getMaritalStatuses(): array
	{
		return self::MARITAL_STATUSES;
	}

	/**
	 * Combine parts of a person's name into "First Middle Last" format
	 *
	 * @param null|string $first
	 * @param null|string $middle
	 * @param null|string $last
	 * @param null|string $title
	 * @return string
	 */
	public static function combineFullName(?string $first, ?string $middle, ?string $last, ?string $title = null): string
	{
		$string = "";

		if (!empty($title)) {
			$string .= $title;
		}

		if (!empty($first)) {
			if (!empty($string)) {
				$string .= ' ';
			}

			$string .= $first;
		}

		if (!empty($middle)) {
			if (!empty($string)) {
				$string .= ' ';
			}

			$string .= $middle;
		}

		if (!empty($last)) {
			if (!empty($string)) {
				$string .= ' ';
			}

			$string .= $last;
		}

		return $string;
	}

	/**
	 * Combine parts of a person's name into "Last, First Middle" format
	 *
	 * @param null|string $first
	 * @param null|string $middle
	 * @param null|string $last
	 * @param null|string $title
	 * @return string
	 */
	public static function combineListName(?string $first, ?string $middle, ?string $last, ?string $title = null): string
	{
		$string = "";

		if (!empty($title)) {
			$string .= $title;
		}

		if (!empty($last)) {
			if (!empty($string)) {
				$string .= ' ';
			}

			$string .= $last;
		}

		if (!empty($first)) {
			if (!empty($string)) {
				$string .= ', ';
			}

			$string .= $first;
		}

		if (!empty($middle)) {
			if (!empty($string)) {
				$string .= ' ';
			}

			$string .= $middle;
		}

		return $string;
	}

	/**
	 * Get initials (first letter) of a person's name
	 *
	 * @param string $name
	 * @return string
	 */
	public static function getInitials(string $name): string
	{
		return preg_filter('/[^A-Z]/', '', $name);
	}
}
