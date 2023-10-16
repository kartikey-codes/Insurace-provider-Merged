import api from "@/api";
const url = "/auth";

export async function changePassword(params) {
	const response = await api.post(`${url}/changePassword`, params);
	return response.data.data;
}

export async function updateProfile(params) {
	const response = await api.post(`${url}/update-profile`, params);
	return response.data.data;
}
