import * as Service from "@/clients/services/subscription";

export default {
	namespaced: true,
	state: {
		enabled: true,
		loading: null,
		subscription: {},
	},
	getters: {
		enabled: (state) => state.enabled === null || state.enabled,
		loading: (state) => state.loading === null || state.loading,
		subscription: (state) => state.subscription,
	},
	mutations: {
		setEnabled: (state, payload) => (state.enabled = payload),
		setLoading: (state, payload) => (state.loading = payload),
		setSubscription: (state, payload) => (state.subscription = payload),
	},
	actions: {
		async get({ commit, dispatch }, params) {
			try {
				commit("setLoading", true);
				const response = await Service.get(params);
				commit("setSubscription", response);
				return response;
			} finally {
				commit("setLoading", false);
			}
		},
		async update({ commit, dispatch }, params) {
			return await Service.update(params);
		},
		async cancel({ commit, dispatch }, params) {
			const response = await Service.cancel(params);

			if (response && response.cancelled) {
				window.location = "/logout?reason=subscription_cancelled";
			}

			return response;
		},
	},
};
