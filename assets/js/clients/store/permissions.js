import * as Service from "@/clients/services/permissions";
import { groupBy } from "lodash";

export default {
	namespaced: true,
	state: {
		loadingAll: null,
		all: [],
		currentUser: [],
	},
	getters: {
		loadingAll: (state) => state.loadingAll === null || state.loadingAll,
		all: (state) => state.all,
		allGrouped: (state) => groupBy(state.all, ({ category }) => category),
		allIsEmpty: (state) => state.all.length <= 0,
		allTotal: (state) => state.all.length,
		currentUser: (state) => state.currentUser,
	},
	mutations: {
		setLoadingAll: (state, payload) => (state.loadingAll = payload),
		setAll: (state, payload) => (state.all = payload),
		setCurrentUser: (state, payload) => (state.currentUser = payload),
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
	},
};
