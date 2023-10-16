import api from "@/api";
const url = "/not-defendable-reasons";

export async function getIndex(params) {
	const response = await api.get(url, { params });
	return response.data; // { data: [], pagination: [] }
}

export async function get(id, params) {
	const response = await api.get(`${url}/${id}`, { params });
	return response.data.data;
}

export async function getAll(params) {
	const response = await api.get(`${url}/all`, { params });
	return response.data.data;
}
