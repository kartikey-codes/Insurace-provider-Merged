import api from "@/api";
const url = "/roles";

export async function getAll(params) {
	const response = await api.get(`${url}/all`, { params: params });
	return response.data.data;
}

export async function getIndex(params) {
	const response = await api.get(url, { params: params });
	return response.data; // data: [], pagination: {}
}

export async function get(id, params) {
	const response = await api.get(`${url}/view/${id}`, params);
	return response.data.data;
}

export async function addUser(id, params) {
	const response = await api.post(`${url}/${id}/add-user`, params);
	return response.data.data;
}

export async function removeUser(id, params) {
	const response = await api.post(`${url}/${id}/remove-user`, params);
	return response.data.data;
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
	const response = await api.post(`${url}/edit/${id}`, params);
	return response.data.data;
}

export async function destroy(id, params) {
	const response = await api.post(`${url}/delete/${id}`, params);
	return response.data.data;
}
