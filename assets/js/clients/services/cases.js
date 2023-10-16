import api from "@/api";
const url = "/cases";

export async function get(params) {
	const response = await api.get(`${url}/${params.id}`);
	return response.data.data;
}

export async function getIndex(params) {
	const response = await api.get(url, { params });
	return response.data; // { data: [], pagination: [] }
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

export async function update(id, params) {
	const response = await api.patch(`${url}/${id}`, params);
	return response.data.data;
}

export async function destroy(id, params) {
	const response = await api.delete(`${url}/${id}`);
	return response.data; // { success: boolean, data: {} }
}

export async function activity(id, params) {
	const response = await api.get(`${url}/${id}/activity`, params);
	return response.data.data; // [users]
}

export async function assign(id, params) {
	const response = await api.post(`${url}/${id}/assign`, params);
	return response.data.data;
}

export async function files(id, params) {
	const response = await api.get(`${url}/${id}/files`, params);
	return response.data.files; // [files]
}

export async function open(id, params) {
	const response = await api.post(`${url}/${id}/open`, {
		id: id,
	});
	return response.data.data;
}

export async function close(id, params) {
	const response = await api.post(`${url}/${id}/close`, {
		id: id,
		case_outcome_id: params.case_outcome_id,
		settled_amount: params.settled_amount,
	});
	return response.data.data;
}
