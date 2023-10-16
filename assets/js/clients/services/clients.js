import api from "@/api";
const url = "/clients";

/** Admin */
export async function getActive(params) {
	const response = await api.get(`${url}/active`, params);
	return response.data.data;
}
