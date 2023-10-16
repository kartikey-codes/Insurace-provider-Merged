import api from "@/api";
const url = "/subscriptions";

export async function get(params) {
	const response = await api.get(url, { params });
	return response.data.data;
}

export async function update(params) {
	const response = await api.post(`${url}/update`, params);
	return response.data.data;
}

export async function cancel(params) {
	const response = await api.post(`${url}/cancel`, params);
	return response.data.data;
}
