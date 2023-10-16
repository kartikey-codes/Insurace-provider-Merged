/**
 * Download file from API response contents
 * @param {*} options
 */
export function downloadResponseAsFile(options) {
	const file = new File([options.contents], options.name, {
		type: options.contentType ?? "application/octet-stream",
	});

	const url = URL.createObjectURL(file);

	const anchorElement = document.createElement("a");
	anchorElement.href = url;
	anchorElement.download = options.name ?? "";
	anchorElement.click();
	anchorElement.remove();

	URL.revokeObjectURL(url);
}
