import moment from "moment";

export default function (value) {
	var outputFormat = 'YYYY-MM-DD';
	var parsed = moment(value);

	if (parsed.isValid()) {
		return parsed.utc().format(outputFormat);
	}

	if (value) {
		timestamp = moment(String(value));
		return timestamp.utc().format(outputFormat);
	}

	return false;
}
