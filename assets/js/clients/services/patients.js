import api from "@/api";
const url = "/patients";

export async function getIndex(params) {
	const response = await api.get(url, { params });
	return response.data; // { data: [], pagination: [] }
}

export async function get(id, params) {
	const response = await api.get(`${url}/${id}`, { params });
	return response.data.data;
}

export async function search(params) {
	const response = await api.get(url, { params });
	return response.data.data;
}

export async function save(params) {
	if (params.id) {
		const response = await api.patch(`${url}/${id}`, params);
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

export async function destroy(id) {
	const response = await api.delete(`${url}/${id}`, {
		_method: "DELETE",
	});
	return response.data.data;
}

export async function getSimilar(id, params) {
	const response = await api.get(`${url}/${id}/similar`, params);
	return response.data.data;
}

export async function merge(id, params) {
	const response = await api.post(`${url}/${id}/merge`, {
		_ids: params.ids,
	});
	return response.data.data;
}
