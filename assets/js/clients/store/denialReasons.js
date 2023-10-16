import * as Service from "@/clients/services/denialReasons";

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
		async index({ commit, dispatch }, params) {
			return await Service.getIndex(params);
		},
		async get({ commit, dispatch }, params) {
			return await Service.get(params.id);
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
			return await Service.update(params.id, params);
		},
		async delete({ commit, dispatch }, params) {
			return await Service.destroy(params.id);
		},
		async getAll({ commit, dispatch }, params) {
			commit("setLoadingAll", true);
			const response = await Service.getAll(params);
			commit("setAll", response);
			commit("setLoadingAll", false);
			return response;
		},
	},
};
