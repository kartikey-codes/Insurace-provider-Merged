import api from "@/api";
const url = "/facility-types";

export async function getIndex(params) {
	const response = await api.get(url, { params });
	return response.data.data;
}

export async function get(id, params) {
	const response = await api.get(`${url}/${id}`, params);
	return response.data.data;
}

export async function getAll(params) {
	const response = await api.get(`${url}/all`, { params });
	return response.data.data;
}
