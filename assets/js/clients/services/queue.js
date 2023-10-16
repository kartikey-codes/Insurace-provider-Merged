import api from "@/api";
const url = "/queue";

export async function getAppealQueue(params) {
	const response = await api.get(`${url}/appeals`, { params });
	return response.data;
}

export async function getCaseRequestQueue(params) {
	const response = await api.get(`${url}/caseRequests`, { params });
	return response.data;
}
