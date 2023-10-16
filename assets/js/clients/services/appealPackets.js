import api from "@/api";
import { downloadResponseAsFile } from "@/shared/helpers/downloadHelper";

const url = "/appeals";
const subpart = "packet";

export async function download(id, params) {
	const response = await api.get(`${url}/${id}/${subpart}/download`, {
		responseType: "arraybuffer",
	});
	downloadResponseAsFile({
		contents: response.data,
		name: params.name || `${id}.pdf`,
	});
}

export async function exists(id, params) {
	const response = await api.get(`${url}/${id}/${subpart}/exists`, { params });
	return response.data;
}

export async function generate(id, params) {
	const response = await api.post(`${url}/${id}/${subpart}/generate`, params);
	return response.data;
}

export async function submit(id, params) {
	alert('inside submit function on appealPackets.js page')
	// /appeals/appealPackets/submit/packet/submit
	const response = await api.post(`${url}/${id}/${subpart}/submit`, params);
	return response.data;
}
