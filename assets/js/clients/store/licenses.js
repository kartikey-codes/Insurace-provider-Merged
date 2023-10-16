import api from "@/api";
const url = "/licenses";

export default {
	namespaced: true,
	state: {
		enabled: true,
		loading: null,
		licenses: {
			total: null,
			available: null,
			used: null,
		},
		warnThreshold: 2,
	},
	getters: {
		enabled: (state) => state.enabled,
		loading: (state) => state.loading === null || state.loading,
		licenses: (state) => state.licenses,
		total: (state) => state.licenses.total,
		available: (state) => state.licenses.available,
		used: (state) => state.licenses.used,
		warnThreshold: (state) => state.warnThreshold,
	},
	mutations: {
		setEnabled: (state, payload) => (state.enabled = payload),
		setLoading: (state, payload) => (state.loading = payload),
		setLicenses: (state, payload) => (state.licenses = payload),
	},
	actions: {
		async get({ commit, dispatch }, params) {
			commit("setLoading", true);

			const response = await api.get(url, {
				params: params,
			});

			commit("setEnabled", response.data.data.enabled);
			commit("setLicenses", response.data.data);
			commit("setLoading", false);

			return response.data.data;
		},
	},
};
