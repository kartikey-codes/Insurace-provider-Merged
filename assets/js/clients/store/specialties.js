import * as Service from "@/clients/services/specialties";

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
		async index({ commit, dispatch }, params) {
			return await Service.getIndex(params);
		},
		async get({ commit, dispatch }, params) {
			return await Service.get(params.id);
		},
		async getAll({ commit, dispatch }, params) {
			try {
				commit("setLoadingAll", true);
				const response = await Service.getAll(params);
				commit("setAll", response);
				return response;
			} finally {
				commit("setLoadingAll", false);
			}
		},
	},
};
