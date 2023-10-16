import * as Service from "@/clients/services/caseRequests";
import { getCaseRequestQueue } from "@/clients/services/queue";

export default {
	namespaced: true,
	state: {
		statuses: [
			{
				name: "Open",
				value: "Open",
			},
			{
				name: "Unable To Complete",
				value: "UTC",
			},
			{
				name: "Completed",
				value: "Completed",
			},
		],
		defaultType: "DOCUMENTATION",
		types: [
			{
				name: "Documentation",
				value: "DOCUMENTATION",
			},
			{
				name: "Hearing",
				value: "HEARING",
			},
		],
		sortAliases: {},
	},
	getters: {
		defaultType: (state) => state.defaultType,
		sortAliases: (state) => state.sortAliases,
		statuses: (state) => state.statuses,
		types: (state) => state.types,
	},
	mutations: {},
	actions: {
		async index({ commit, dispatch }, params) {
			return await Service.getIndex(params);
		},
		async queue({ commit, dispatch }, params) {
			return await getCaseRequestQueue(params);
		},
		async get({ commit, dispatch }, params) {
			return await Service.get(params);
		},
		async save({ commit, dispatch }, entity) {
			if (entity.id) {
				return await dispatch("update", entity);
			} else {
				return await dispatch("create", entity);
			}
		},
		async create({ commit, dispatch }, params) {
			return await Service.create(params);
		},
		async update({ commit, dispatch }, params) {
			return await Service.update(params);
		},
		async delete({ commit, dispatch }, params) {
			return await Service.destroy(params);
		},
		async close({ commit, dispatch }, params) {
			return await Service.close(params);
		},
		async reopen({ commit, dispatch }, params) {
			return await Service.reopen(params);
		},
		async setUtc({ commit, dispatch }, params) {
			return await Service.setUtc(params);
		},
		async assign({ commit, dispatch }, params) {
			return await Service.assign(params);
		},
	},
};
