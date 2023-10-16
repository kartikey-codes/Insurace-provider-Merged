import api from "@/api";
const url = "/integrations";

// Not Paginated
export async function getAll(params) {
	const response = await api.get(url, { params });
	return response.data.data; // { data: {} }
}

export async function updateConfig(params) {
	const response = await api.post(`${url}/update`, params);
	return response.data.data; // { data: {} }
}
