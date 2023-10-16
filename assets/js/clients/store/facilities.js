import * as Service from "@/clients/services/facilities";

export default {
	namespaced: true,
	state: {
		defaultFilters: {
			active: null,
			facility_type_id: null,
			state: null,
		},
		loadingAll: null,
		all: [],
		loadingActive: null,
		active: [],
	},
	getters: {
		loadingAll: (state) => state.loadingAll === null || state.loadingAll,
		all: (state) => state.all,
		loadingActive: (state) => state.loadingActive === null || state.loadingActive,
		active: (state) => state.active,
		defaultFilters: (state) => state.defaultFilters,
	},
	mutations: {
		setLoadingAll: (state, payload) => (state.loadingAll = payload),
		setAll: (state, payload) => (state.all = payload),
		setLoadingActive: (state, payload) => (state.loadingActive = payload),
		setActive: (state, payload) => (state.active = payload),
	},
	actions: {
		async index({ commit, dispatch }, params) {
			return await Service.getIndex(params);
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
		async get({ commit, dispatch }, params) {
			return await Service.get(params.id);
		},
		async getAll({ commit, dispatch }, params) {
			commit("setLoadingAll", true);
			const response = await Service.getAll(params);
			commit("setAll", response);
			commit("setLoadingAll", false);
			return response;
		},
		async getActive({ commit, dispatch }, params) {
			commit("setLoadingActive", true);
			const response = await Service.getActive(params);
			commit("setActive", response);
			commit("setLoadingActive", false);
			return response;
		},
		async npiLookup({ commit, dispatch }, params) {
			return await Service.npiLookup(params);
		},
	},
};
