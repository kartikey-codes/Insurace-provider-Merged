import api from "@/api";
import { downloadResponseAsFile } from "@/shared/helpers/downloadHelper";
import { createUploadFormData, createUploadOptions } from "@/shared/helpers/uploadHelper";
import { isString } from "lodash";

const url = "/library";

export async function list(prefix, params) {
	const response = await api.get(url, {
		params: {
			path: prefix || null,
			...params,
		},
	});
	return response.data.data;
}

export async function getFolders(prefix = "") {
	const response = await api.get(url, {
		params: {
			path: prefix || null,
		},
	});

	const folders = response.data.data.filter((item) => item.type === "dir");

	return folders;
}

export async function download(prefix, params) {
	const response = await api.get(`${url}/download`, {
		params: {
			path: prefix,
			name: params.name,
		},
		responseType: "arraybuffer",
	});
	downloadResponseAsFile({
		contents: response.data,
		name: params.name,
	});
}

export function previewUrl(prefix, params) {
	const baseUrl = api.getBaseUrl();
	const token = api.getApiToken();

	var previewUrl = `${baseUrl}${url}/preview/${params.name}?token=${token}`;

	const adminClientId = api.getAdminClientId();
	if (adminClientId) {
		previewUrl += `&Admin-Client-Id=${adminClientId}`;
	}

	return previewUrl;
}

export async function upload(params, callback) {
	const data = createUploadFormData(params.files, params.path);
	const options = createUploadOptions((percentage) => callback(percentage));
	const response = await api.post(url, data, options);
	return response.data;
}

export async function destroy(prefix, params) {
	const response = await api.delete(`${url}?name=${params.name}`);
	return response.data;
}

export async function rename(prefix, params) {
	const response = await api.post(`${url}/rename`, {
		path: prefix,
		filename: params.filename,
		newname: params.newName,
	});
	return response.data;
}

export async function merge(prefix, params) {
	const response = await api.post(`${url}/merge`, {
		path: prefix,
		...params,
	});

	if (isString(response.data) && response.data.includes("TCPDI_PARSER ERROR")) {
		console.error("Error Merging Files", response.data);
		throw new Error(
			"There was an error attempting to merge documents. Please ensure only PDFs have been supplied."
		);
	}

	return response.data;
}

export async function zip(prefix, params) {
	const response = await api.post(`${url}/zip`, {
		path: prefix,
		...params,
	});

	return response.data;
}
