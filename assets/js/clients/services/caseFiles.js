import api from "@/api";
import { downloadResponseAsFile } from "@/shared/helpers/downloadHelper";
import { createUploadFormData, createUploadOptions } from "@/shared/helpers/uploadHelper";

const url = "/cases";
const subpart = "files";

export async function list(id, params) {
	const response = await api.get(`${url}/${id}/${subpart}`, params);
	return response.data.data;
}

export async function download(id, params) {
	const response = await api.get(`${url}/${id}/${subpart}/download?name=${params.name}`, {
		responseType: "arraybuffer",
	});
	downloadResponseAsFile({
		contents: response.data,
		name: params.name,
	});
}

export function previewUrl(id, params) {
	const baseUrl = api.getBaseUrl();
	const token = api.getApiToken();

	return `${baseUrl || params.baseUrl}${url}/${id}/${subpart}/preview/${params.name}?token=${token || params.token}`;
}

export async function upload(params, callback) {
	const data = createUploadFormData(params.files);
	const options = createUploadOptions((percentage) => callback(percentage));
	const response = await api.post(`${url}/${params.path || params.id}/${subpart}`, data, options);
	return response.data;
}

export async function destroy(id, params) {
	const response = await api.delete(`${url}/${id}/${subpart}?name=${params.name}`);
	return response.data;
}

export async function rename(id, params) {
	const response = await api.post(`${url}/${id}/${subpart}/rename`, {
		filename: params.filename,
		newname: params.newName,
	});
	return response.data;
}

export async function merge(id, params) {
	const response = await api.post(`${url}/${id}/${subpart}/merge`, params);
	return response.data;
}

export async function zip(id, params) {
	const response = await api.post(`${url}/${id}/${subpart}/zip`, params);
	return response.data;
}
