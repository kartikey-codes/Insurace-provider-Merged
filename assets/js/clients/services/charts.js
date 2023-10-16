import api from "@/api";
const url = "/charts";

export async function appeals(params) {
	const response = await api.post(`${url}/appeals`, { params });
	return response.data;
}

export async function cases(params) {
	const response = await api.post(`${url}/cases`, { params });
	return response.data;
}

export async function requests(params) {
	const response = await api.post(`${url}/requests`, { params });
	return response.data;
}
