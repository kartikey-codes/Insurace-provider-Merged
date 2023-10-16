import api from "@/api";
const url = "/days-to-respond-froms";

export async function getAll(params) {
	const response = await api.get(`${url}/all`, { params });
	return response.data.data;
}
