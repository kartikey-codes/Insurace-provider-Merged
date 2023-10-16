import moment from "moment";

export default function (value, options = { dateOnly: false }) {
	var expectedFormat = 'X';
	var outputFormat = options.dateOnly ? 'MM/DD/YYYY' : 'MM/DD/YYYY - h:mm A';
	var parsed = moment(value, expectedFormat);

	if (parsed._isValid) {
		return parsed.format(outputFormat);
	}

	if (value) {
		var timestamp = moment(String(value));
		var now = moment();

		if (now.isSame(timestamp, 'd')) {
			return timestamp.format('h:mm A');
		} else {
			return timestamp.format(outputFormat);
		}
	}

	return false;
}
