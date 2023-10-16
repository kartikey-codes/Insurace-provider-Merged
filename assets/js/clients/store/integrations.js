import * as Service from "@/clients/services/integrations";

export default {
	namespaced: true,
	state: {
		loadingAll: null,
		all: {
			mirthConnect: {
				enabled: false,
				connected: false,
			},
		},
		updating: false,
	},
	getters: {
		loadingAll: (state) => state.loadingAll === null || state.loadingAll,
		all: (state) => state.all,
		updating: (state) => state.updating,
		mirthConnect: (state) => state.all.mirthConnect,
	},
	mutations: {
		setLoadingAll: (state, payload) => (state.loadingAll = payload),
		setAll: (state, payload) => (state.all = payload),
		setUpdating: (state, payload) => (state.updating = payload),
		setMirthConnect: (state, payload) => (state.all.mirthConnect = payload),
	},
	actions: {
		async getAll({ commit, dispatch }, params) {
			try {
				commit("setLoadingAll", true);
				const response = await Service.getAll(params);
				commit("setAll", response);
			} finally {
				commit("setLoadingAll", false);
			}
		},
		async updateConfig({ commit, dispatch }, params) {
			try {
				commit("setUpdating", true);
				const response = await Service.updateConfig(params);
				return response;
			} finally {
				commit("setUpdating", false);
			}
		},
	},
};
