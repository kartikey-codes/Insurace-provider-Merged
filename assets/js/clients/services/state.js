import api from "@/api";
const url = "/state";

export async function getState() {
	const response = await api.get(url);
	return response.data;
}

export async function getStartup() {
	const response = await api.get(`${url}/startup`);
	return response.data;
}
