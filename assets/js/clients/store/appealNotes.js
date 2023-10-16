import * as Service from "@/clients/services/appealNotes";

export default {
	namespaced: true,
	state: {},
	getters: {},
	mutations: {},
	actions: {
		async addNote({ commit, dispatch }, params) {
			return await Service.addNote(params.id, params);
		},
		async deleteNote({ commit, dispatch }, params) {
			return await Service.deleteNote(params.appeal_id, params.id);
		},
	},
};
