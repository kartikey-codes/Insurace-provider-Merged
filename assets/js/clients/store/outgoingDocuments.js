import * as Service from "@/clients/services/outgoingDocuments";

export default {
	namespaced: true,
	state: {
		loadingCount: false,
		count: {
			new: 0,
		},
		availableMethods: [
			{
				name: "Email",
				value: "EMAIL",
			},
			{
				name: "Fax",
				value: "FAX",
			},
			{
				name: "Website",
				value: "WEBSITE",
			},
			{
				name: "Mail",
				value: "MAIL",
			},
			{
				name: "Manual",
				value: "MANUAL",
			},
		],
	},
	getters: {
		loadingCount: (state) => state.loadingCount,
		countNew: (state) => state.count.new,
		availableMethods: (state) => state.availableMethods,
	},
	mutations: {
		setLoadingCount: (state, payload) => (state.loadingCount = payload),
		setCountNew: (state, payload) => (state.count.new = payload),
	},
	actions: {
		async index({ commit, dispatch }, params) {
			return await Service.getIndex(params);
		},
		async count({ commit, dispatch }, params) {
			try {
				commit("setLoadingCount", true);
				const response = await Service.count(params);
				commit("setCountNew", response.new);
				return response;
			} finally {
				commit("setLoadingCount", false);
			}
		},
		async cancel({ commit, dispatch }, params) {
			return await Service.cancel(params.id);
		},
		async download({ commit, dispatch }, params) {
			return await Service.download(params.id);
		},
		previewUrl({ commit, dispatch }, params) {
			return Service.previewUrl(params.id);
		},
		async delivered({ commit, dispatch }, params) {
			return await Service.delivered(params.id, params);
		},
		async retry({ commit, dispatch }, params) {
			return await Service.retry(params.id);
		},
	},
};
