import api from "@/api";
const url = "/permissions";

export async function getAll(params) {
	const response = await api.get(`${url}/all`, { params });
	return response.data.data;
}
