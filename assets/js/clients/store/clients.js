import * as Service from "@/clients/services/clients";

export default {
	namespaced: true,
	state: {
		loadingActive: null,
		active: [],
		statuses: [
			{
				value: "Active",
				name: "Active",
			},
			{
				value: "Inactive",
				name: "Inactive",
			},
			{
				value: "Pending",
				name: "Pending",
			},
			{
				value: "On-Hold",
				name: "On-Hold",
			},
		],
	},
	getters: {
		loadingActive: (state) => (state) => state.loadingActive === null || state.loadingActive,
		active: (state) => state.active,
		statuses: (state) => state.statuses,
	},
	mutations: {
		setLoadingActive: (state, payload) => (state.loadingActive = payload),
		setActive: (state, payload) => (state.active = payload),
	},
	actions: {
		/** Admin */
		async getActive({ commit, dispatch }, params) {
			commit("setLoadingActive", true);
			const response = await Service.getActive(params);
			commit("setActive", response);
			commit("setLoadingActive", false);
			return response;
		},
	},
};
