import api from "@/api";
import { downloadResponseAsFile } from "@/shared/helpers/downloadHelper";
const url = "/outgoing-documents";

export function previewUrl(id, params) {
	const baseUrl = api.getBaseUrl();
	const token = api.getApiToken();

	return `${baseUrl}${url}/${id}/preview?token=${token}&view=#FitH`;
}

export async function getIndex(params) {
	const response = await api.get(url, { params });
	return response.data; // { data: [], pagination: [] }
}

export async function count(params) {
	const response = await api.get(`${url}/count`, { params });
	return response.data;
}

export async function download(id, params) {
	const response = await api.get(`${url}/${id}/download`, {
		responseType: "arraybuffer",
	});

	let fileName = params?.name || response.headers["x-filename"] || null;

	downloadResponseAsFile({
		contents: response.data,
		name: fileName,
	});
}

export async function cancel(id, params) {
	const response = await api.post(`${url}/${id}/cancel`, params);
	return response.data;
}

export async function delivered(id, params) {
	const response = await api.post(`${url}/${id}/delivered`, params);
	return response.data;
}

export async function retry(id, params) {
	const response = await api.post(`${url}/${id}/retry`, params);
	return response.data;
}
