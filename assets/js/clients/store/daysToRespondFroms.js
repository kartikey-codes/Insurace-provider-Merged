import * as Service from "@/clients/services/daysToRespondFroms";

export default {
	namespaced: true,
	state: {
		loadingAll: null,
		all: [],
	},
	getters: {
		loadingAll: (state) => state.loadingAll === null || state.loadingAll,
		all: (state) => state.all,
	},
	mutations: {
		setLoadingAll: (state, payload) => (state.loadingAll = payload),
		setAll: (state, payload) => (state.all = payload),
	},
	actions: {
		async getAll({ commit, dispatch }, params) {
			commit("setLoadingAll", true);
			const response = await Service.getAll(params);
			commit("setAll", response);
			commit("setLoadingAll", false);
			return response;
		},
	},
};
