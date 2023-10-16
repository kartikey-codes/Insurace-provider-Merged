import moment from "moment";

export default function (value) {
	var outputFormat = 'MM/DD/YYYY';
	var parsed = moment(value, 'YYYY-MM-DD');

	if (parsed._isValid) {
		return parsed.format(outputFormat);
	}

	if (value) {
		var timestamp = moment(String(value));
		return timestamp.format(outputFormat);
	}

	return false;
}
