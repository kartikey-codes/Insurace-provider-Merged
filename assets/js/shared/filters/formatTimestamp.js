import moment from "moment";

export default function (value) {
	var outFormat = 'MM/DD/YYYY - h:mm A';
	var parsed = moment(value);

	if (parsed._isValid) {
		return parsed.format(outFormat);
	}

	if (value) {
		var timestamp = moment(String(value));
		var now = moment('now');

		if (now.isSame(timestamp, 'd')) {
			return timestamp.format('h:m A');
		} else {
			return timestamp.format(outFormat);
		}
	}

	return false;
}
