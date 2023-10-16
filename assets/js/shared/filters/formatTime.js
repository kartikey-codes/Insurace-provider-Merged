import moment from "moment";

export default function (value) {
	var outputFormat = 'hh:mm A';
	var parsed = moment(value, 'HH:mm:ss');

	if (parsed._isValid) {
		return parsed.format(outputFormat);
	}

	if (value) {
		var timestamp = moment(String(value));
		return timestamp.format(outputFormat);
	}

	return false;
}
