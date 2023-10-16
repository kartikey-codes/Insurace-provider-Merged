<?php

declare(strict_types=1);

namespace App\Lib\AddressUtility;

/**
 * Class for handling dealing with addresses and locations
 */
class AddressUtility
{
	/**
	 * Combine parts of a typical address into a formatted string
	 * with line breaks.
	 *
	 * @param null|string $street1
	 * @param null|string $street2
	 * @param null|string $city
	 * @param null|string $state
	 * @param null|string $zip
	 * @return string
	 */
	public static function combineToString(?string $street1, ?string $street2, ?string $city, ?string $state, ?string $zip): string
	{
		$string = "";

		if (!empty($street1)) {
			$string .= $street1;

			if (!empty($street2)) {
				$string .= "\r\n";
			}
		}

		if (!empty($street2)) {
			$string .= $street2;
		}

		if (!empty($string)) {
			if (!empty($city) || !empty($state) || !empty($zip)) {
				$string .= "\r\n";
			}
		}

		if (!empty($city)) {
			$string .= $city;
		}

		if (!empty($state)) {
			if (!empty($city)) {
				$string .= ', ';
			}

			$string .= $state;
		}

		if (!empty($zip)) {
			if (!empty($string)) {
				$string .= ' ';
			}

			$string .= $zip;
		}

		return $string;
	}
}
