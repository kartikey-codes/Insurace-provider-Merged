import api from "@/api";

const url = "/cases";
const subpart = "requests";

const indexUrl = "/case-requests";

export async function getIndex(params) {
	const response = await api.get(indexUrl, { params });
	return response.data;
}

export async function get(params) {
	const response = await api.get(`${url}/${params.case_id}/${subpart}/${params.id}`);
	return response.data.data;
}

export async function save(params) {
	if (params.id) {
		const response = await api.patch(`${url}/${params.case_id}/${subpart}/${params.id}`, params);
		return response.data.data;
	} else {
		const response = await api.post(`${url}/${params.case_id}/${subpart}`, params);
		return response.data.data;
	}
}

export async function create(params) {
	const response = await api.post(`${url}/${params.case_id}/${subpart}`, params);
	return response.data.data;
}

export async function update(params) {
	const response = await api.patch(`${url}/${params.case_id}/${subpart}/${params.id}`, params);
	return response.data.data;
}

export async function destroy(params) {
	const response = await api.delete(`${url}/${params.case_id}/${subpart}/${params.id}`, {
		_method: "DELETE",
	});
	return response.data;
}

export async function close(params) {
	const response = await api.post(`${url}/${params.case_id}/${subpart}/${params.id}/close`, params);
	return response.data.data;
}

export async function reopen(params) {
	const response = await api.post(`${url}/${params.case_id}/${subpart}/${params.id}/reopen`, params);
	return response.data.data;
}

export async function setUtc(params) {
	const response = await api.post(`${url}/${params.case_id}/${subpart}/${params.id}/utc`, {
		unable_to_complete: params.unable_to_complete,
	});
	return response.data.data;
}

export async function assign(params) {
	const response = await api.post(`${url}/${params.case_id}/${subpart}/${params.id}/assign`, {
		user_id: params.user_id,
	});

	return response.data.data;
}
