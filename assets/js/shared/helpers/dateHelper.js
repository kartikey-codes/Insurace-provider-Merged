/**
 * Get date offset by x number of days
 * @returns string
 */
export function getDateOffsetDaysString(days = 0) {
	var date = new Date();
	date.setDate(date.getDate() + days);
	const string = date.toISOString().split("T")[0];
	return string;
}

/**
 * Get date offset by x number of days
 * @returns Date
 */
export function getDateOffsetDays(days = 0) {
	var date = new Date();
	date.setDate(date.getDate() + days);
	return date;
}

/**
 * Get today's date in YYYY-MM-DD format
 * @returns string
 */
export function getTodaysDate() {
	const today = new Date();

	const date = new Date(today.getFullYear(), today.getMonth(), today.getDate());
	const string = date.toISOString().split("T")[0];

	return string;
}

/**
 * Get tomorrow's date in YYYY-MM-DD format
 * @returns string
 */
export function getTomorrowsDate() {
	var date = new Date();
	date.setDate(date.getDate() + 1);
	const string = date.toISOString().split("T")[0];

	return string;
}

/**
 * Get the furthest back date any UI element should be able to go
 * (i.e. patient birthday)
 * @returns string
 */
export function getAbsoluteMinimumDate() {
	return "1900-01-01";
}

/**
 * Please tell me this application is not being used beyond this date.
 * This is just for making sure inputs have an upper range
 * @returns string
 */
export function getAbsoluteMaximumDate() {
	return "2100-01-01";
}
