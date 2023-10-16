import api from "@/api";
const url = "/settings";

export async function get(params) {
	const response = await api.get(url, { params });
	return response.data.data;
}
