import * as Service from "@/clients/services/calendar";

export default {
	namespaced: true,
	state: {
		loading: null,
		results: [],
	},
	getters: {
		loading: (state) => state.loading === null || state.loading,
		results: (state) => state.results,
	},
	mutations: {
		setLoading: (state, payload) => (state.loading = payload),
		setResults: (state, payload) => (state.results = payload),
	},
	actions: {
		async getIndex({ commit, dispatch }, params) {
			commit("setLoading", true);
			const response = await Service.getIndex(params);
			commit("setResults", response);
			commit("setLoading", false);
			return response;
		},
		async get({ commit, dispatch }, params) {
			const response = await Service.get(params);
			return response;
		},
	},
};
