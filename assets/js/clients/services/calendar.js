import api from "@/api";
const url = "/calendar";

export async function getIndex(params) {
	const response = await api.get(url, { params });
	return response.data; // { data: [], start: {}, end: {} }
}

export async function get(params) {
	const response = await api.get(`${url}/${params.date}`, {});
	return response.data; // { data: [], date: {} }
}
