import moment from "moment";

export default function (value) {
	if (typeof value == "number") {
		return moment(value, 'X').fromNow();
	}

	return moment(value).fromNow();
}
