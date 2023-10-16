import api from "@/api";
const url = "/appeals";

export async function getIndex(params) {
	const response = await api.get(url, { params });
	return response.data; // { data: [], pagination: [] }
}

export async function get(id, params) {
	const response = await api.get(`${url}/${id}`, { params });
	return response.data.data;
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

export async function getOpenByFacility(params) {
	const response = await api.get(`${url}/openByFacility`, params);
	return response.data.data;
}

export async function getOpenByAssignedUser(params) {
	const response = await api.get(`${url}/openByAssignedUser`, {
		params: params,
	});
	return response.data.data;
}

export async function getUnassigned(params) {
	const response = await api.get(`${url}/unassigned`, { params });
	return response.data; // { data: [], pagination: {} }
}

export async function assign(id, params) {
	const response = await api.post(`${url}/${id}/assign`, {
		user_id: params.user_id,
	});
	return response.data.data;
}

export async function assignAll(params) {
	const response = await api.post(`${url}/assignAll`, {
		assigned_to: params.user_id,
		_ids: params.ids,
	});
	return response.data.data;
}

export async function submit(id, params) {
	const response = await api.post(`${url}/${id}/submit`);
	return response.data.data;
}

export async function reopen(id, params) {
	const response = await api.post(`${url}/${id}/reopen`);
	return response.data.data;
}

export async function complete(id, params) {
	const response = await api.post(`${url}/${id}/complete`);
	return response.data.data;
}

export async function cancel(id, params) {
	const response = await api.post(`${url}/${id}/cancel`);
	return response.data.data;
}

export async function close(id, params) {
	const response = await api.post(`${url}/${id}/close`);
	return response.data.data;
}

export async function setDefendable(id, params) {
	const response = await api.post(`${url}/${id}/defendable`, params);
	return response.data.data;
}

export async function setUnableToComplete(id, params) {
	const response = await api.post(`${url}/${id}/utc`, params);
	return response.data.data;
}

export async function getCoverPage(id, params) {
	const response = await api.get(`${url}/${id}/coverPage`);
	return response.data;
}
export async function generateCoverPage(id, params) {
	const response = await api.post(`${url}/${id}/generateCoverPage`, params);
	return response.data;
}
