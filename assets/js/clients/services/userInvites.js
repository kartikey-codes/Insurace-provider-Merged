import api from "@/api";
const url = "/user-invites";

export async function sendInvite(params) {
	const response = await api.post(url, params);
	return response.data.data;
}
