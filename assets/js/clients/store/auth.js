import api from "@/api";
const url = "/auth";

export default {
	namespaced: true,
	state: {

	},
	getters: {

	},
	mutations: {

	},
	actions: {
		async changePassword({ commit, dispatch }, params) {
			const response = await api.post(`${url}/changePassword`, params);
			return response.data.data;
		},
		async updateProfile({ commit, dispatch }, params) {
			const response = await api.post(`${url}/update-profile`, params);
			return response.data.data;
		},
	}
}
