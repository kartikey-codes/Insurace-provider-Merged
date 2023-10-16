import api from "@/api";
const url = "/charts";

export default {
	namespaced: true,
	state: {},
	getters: {},
	mutations: {},
	actions: {
		async appeals({ commit, dispatch }, params) {
			const response = await api.post(`${url}/appeals`, params);
			return response.data;
		},
		async cases({ commit, dispatch }, params) {
			const response = await api.post(`${url}/cases`, params);
			return response.data;
		},
		async requests({ commit, dispatch }, params) {
			const response = await api.post(`${url}/requests`, params);
			return response.data;
		},
	},
};
