/**
 * Turn multiple file uploads into a FormData object
 * @param {*} files
 * @param {string} path
 * @returns FormData
 */
export function createUploadFormData(files, path = "") {
	let formData = new FormData();

	for (var i = 0, len = files.length; i < len; ++i) {
		formData.append("files[]", files[i]);
	}

	if (path) {
		formData.append("path", path);
	}

	return formData;
}

/**
 * Return consistent upload options object with
 * callback for setting upload progress percentage
 * @param {function} onProgress
 * @returns {object}
 */
export function createUploadOptions(onProgress) {
	return {
		headers: {
			"Content-Type": "multipart/form-data",
		},
		onUploadProgress: function (e) {
			let percentage = parseInt((e.loaded / e.total) * 100);
			onProgress(percentage);
		},
	};
}
