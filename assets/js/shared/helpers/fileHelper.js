/**
 * Return file extension from full file name / path
 * @param {string} filename
 */
export function getExtension(filename) {
	const extension = filename.split(".").pop();
	return extension == filename ? false : extension;
}

/**
 * Return base filename from filename string
 * @param {string} filename
 */
export function getBasename(filename) {
	return filename.substring(0, filename.lastIndexOf(".")) || filename;
}

/**
 * Return if file extension can be merged into a PDF
 * @param {string} extension
 */
export function extensionMergesIntoPdf(extension) {
	if (extension.toLowerCase() == "pdf") {
		return true;
	}

	return false;
}
