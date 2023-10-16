export default function (value, length = 10) {
	if (value.length <= length) {
		return value;
	}

	return value.slice(0, length) + "...";
}
