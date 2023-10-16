import api from "@/api";
const url = "/incoming-documents";

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

export async function save(params) {
	if (params.id) {
		const response = await api.post(`${url}/edit/${params.id}`, params);
		return response.data.data;
	} else {
		const response = await api.post(url, params);
		return response.data.data;
	}
}

export async function create(params) {
	const response = await api.post(url, params);
	return response.data.data;
}

export async function get(id) {
	const response = await api.get(`${url}/view/${id}`);
	return response.data.data;
}

export async function update(id, params) {
	const response = await api.post(`${url}/edit/${id}`, params);
	return response.data.data;
}

export async function destroy(id, params) {
	const response = await api.post(`${url}/delete/${id}`, params);
	return response.data.data;
}

export async function download(id, params) {
	const response = await api.get(`${url}/${id}/download`, {
		responseType: "arraybuffer",
	});
	return response.data;
}

export async function upload(data, options) {
	const response = await api.post(url, data, options);
	return response.data;
}

export async function rename(id, fileName, newName) {
	const response = await api.post(`${url}/${id}/rename`, {
		filename: fileName,
		newname: newName,
	});
	return response.data;
}

export async function attachCase(id, caseId) {
	const response = await api.post(`${url}/${id}/attach_case`, {
		case_id: caseId,
	});
	return response.data.data;
}

export async function detachCase(id, params) {
	const response = await api.post(`${url}/${id}/detach_case`, params);
	return response.data.data;
}

export async function attachAppeal(id, appealId) {
	const response = await api.post(`${url}/${id}/attach_appeal`, {
		appeal_id: appealId,
	});
	return response.data.data;
}

export async function detachAppeal(id, params) {
	const response = await api.post(`${url}/${id}/detach_appeal`, params);
	return response.data.data;
}

export async function assign(id, userId) {
	const response = await api.post(`${url}/${id}/assign`, {
		user_id: userId,
	});
	return response.data.data;
}

export async function setUnableToComplete(id) {
	const response = await api.post(`${url}/${id}/edit`, {
		unable_to_complete: true,
	});
	return response.data.data;
}

export async function unsetUnableToComplete(id) {
	const response = await api.post(`${url}/${id}/edit`, {
		unable_to_complete: false,
	});
	return response.data.data;
}

export async function bulkAssign(user_id, document_ids = []) {
	const response = await api.post(`${url}/bulk_assign`, {
		user_id: user_id,
		document_ids: document_ids,
	});
	return response.data.data;
}
