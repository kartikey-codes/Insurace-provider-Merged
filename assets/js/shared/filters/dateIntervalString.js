export default function (value) {
	var string = null;

	if (value.d > 0) {
		string = string + value.d + ' day' + (value.d != 1 ? 's' : '');
	}

	if (value.h > 0) {
		if (value.d && value.d != 'null') {
			string = string + ', ';
		}

		string = string + value.h + ' hour' + (value.h != 1 ? 's' : '');
	}

	if (value.i > 0) {
		if ((value.h > 0 || value.i > 0) && string) {
			string = string + ', ';
		}

		string = string + value.i + ' minute' + (value.i != 1 ? 's' : '');
	}

	return string;
}
