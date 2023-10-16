import * as Service from "@/clients/services/roles";

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
			try {
				commit("setLoadingAll", true);
				const response = await Service.getAll(params);
				commit("setAll", response);
				return response;
			} finally {
				commit("setLoadingAll", false);
			}
		},
		async addUser({ commit, dispatch }, params) {
			const response = await Service.addUser(params.id, {
				user_id: params.user_id,
			});
			return response;
		},
		async removeUser({ commit, dispatch }, params) {
			const response = await Service.removeUser(params.id, {
				user_id: params.user_id,
			});
			return response;
		},
	},
};
