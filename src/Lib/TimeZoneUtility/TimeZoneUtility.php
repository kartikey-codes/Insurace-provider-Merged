<?php

declare(strict_types=1);

namespace App\Lib\TimeZoneUtility;

/**
 * Class for handling time zones and lists
 */
class TimeZoneUtility
{
	/**
	 * Get the timezones we actually care about in this app
	 *
	 * @return array
	 */
	public static function timezones(): array
	{
		// America/Adak
		// America/Anchorage
		// America/Boise
		// America/Chicago
		// America/Denver
		// America/Detroit
		// America/Indiana/Indianapolis
		// America/Indiana/Knox
		// America/Indiana/Marengo
		// America/Indiana/Petersburg
		// America/Indiana/Tell_City
		// America/Indiana/Vevay
		// America/Indiana/Vincennes
		// America/Indiana/Winamac
		// America/Juneau
		// America/Kentucky/Louisville
		// America/Kentucky/Monticello
		// America/Los_Angeles
		// America/Menominee
		// America/Metlakatla
		// America/New_York
		// America/Nome
		// America/North_Dakota/Beulah
		// America/North_Dakota/Center
		// America/North_Dakota/New_Salem
		// America/Phoenix
		// America/Shiprock
		// America/Sitka
		// America/Yakutat
		// Pacific/Honolulu

		// 'America/Detroit'			=> __('Eastern'),
		// 'America/Chicago'			=> __('Central'),
		// 'America/Denver'				=> __('Mountain'),
		// 'America/Phoenix'			=> __('Mountain (No DST)'),
		// 'America/Los_Angeles'		=> __('Pacific'),
		// 'America/Anchorage'			=> __('Alaska'),
		// 'America/Adak'				=> __('Hawaii'),
		// 'Pacific/Honolulu'			=> __('Hawaii (No DST)')

		return [
			'US/Hawaii' 		=> '(GMT-10:00) Hawaii',
			'US/Alaska' 		=> '(GMT-09:00) Alaska',
			'US/Pacific' 		=> '(GMT-08:00) Pacific Time (US & Canada)',
			'US/Arizona' 		=> '(GMT-07:00) Arizona',
			'US/Mountain' 		=> '(GMT-07:00) Mountain Time (US & Canada)',
			'US/Central' 		=> '(GMT-06:00) Central Time (US & Canada)',
			'US/Eastern' 		=> '(GMT-05:00) Eastern Time (US & Canada)',
			'US/East-Indiana' 	=> '(GMT-05:00) Indiana (East)',
		];
	}
}
