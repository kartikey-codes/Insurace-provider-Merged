import * as Service from "@/clients/services/appealPackets";

export default {
	namespaced: true,
	state: {},
	getters: {},
	mutations: {},
	actions: {
		async download({ commit, dispatch }, params) {
			return await Service.download(params.id, params);
		},
		async exists({ commit, dispatch }, params) {
			return await Service.exists(params.id);
		},
		async generate({ commit, dispatch }, params) {
			return await Service.generate(params.id, params);
		},
		async submit({ commit, dispatch }, params) {
			alert('inside appealPackets.js async submit method')
			return await Service.submit(params.id, params);
		},
	},
};
