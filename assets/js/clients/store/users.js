import api from "@/api";
const url = "/users";

export default {
	namespaced: true,
	state: {
		defaultFilters: {
			active: null,
			date_of_birth: null,
		},
		loadingActive: null,
		active: [],
		loadingAll: null,
		all: [],
		loadingOnline: null,
		online: [],
	},
	getters: {
		loadingActive: (state) => state.loadingActive === null || state.loadingActive === null || state.loadingActive,
		active: (state) => state.active,
		loadingAll: (state) => state.loadingAll === null || state.loadingAll === null || state.loadingAll,
		all: (state) => state.all,
		loadingOnline: (state) => state.loadingOnline === null || state.loadingOnline,
		online: (state) => state.online,
		anyOnline: (state) => state.online && state.online.length > 0,
		defaultFilters: (state) => state.defaultFilters,
	},
	mutations: {
		setLoadingActive: (state, payload) => (state.loadingActive = payload),
		setActive: (state, payload) => (state.active = payload),
		setLoadingAll: (state, payload) => (state.loadingAll = payload),
		setAll: (state, payload) => (state.all = payload),
		setLoadingOnline: (state, payload) => (state.loadingOnline = payload),
		setOnline: (state, payload) => (state.online = payload),
	},
	actions: {
		async find(context, params) {
			const response = await api.get(`${url}`, {
				params: params,
			});
			return response.data;
		},
		async index({ commit, dispatch }, params) {
			const response = await api.get(url, {
				params: params,
			});
			return response.data; // { data: [], pagination: [] }
		},
		async search({ commit, dispatch }, params) {
			const response = await api.get(url, {
				params: params,
			});
			return response.data.data;
		},
		async save({ commit, dispatch }, entity) {
			if (entity.id) {
				return await dispatch("update", entity);
			} else {
				return await dispatch("create", entity);
			}
		},
		async create({ commit, dispatch }, params) {
			const response = await api.post(url, params);
			return response.data.data;
		},
		async update({ commit, dispatch }, params) {
			const response = await api.patch(`${url}/${params.id}`, params);
			return response.data.data;
		},
		async delete({ commit, dispatch }, params) {
			const response = await api.delete(`${url}/${params.id}`);
			return response.data; // { success: boolean, data: {} }
		},
		async get({ commit, dispatch }, params) {
			const response = await api.get(`${url}/${params.id}`, params);
			return response.data.data;
		},
		async getAll({ commit, dispatch }, params) {
			commit("setLoadingAll", true);
			const response = await api.get(`${url}/all`, params);
			commit("setAll", response.data.data);
			commit("setLoadingAll", false);
			return response.data.data;
		},
		async getActive({ commit, dispatch }, params) {
			commit("setLoadingActive", true);
			const response = await api.get(`${url}/active`, params);
			commit("setActive", response.data.data);
			commit("setLoadingActive", false);
			return response.data.data;
		},
		async getOnline({ commit, dispatch }, params) {
			commit("setLoadingOnline", true);
			const response = await api.get(`${url}/online`, params);
			commit("setOnline", response.data.data);
			commit("setLoadingOnline", false);
			return response.data.data;
		},
		async getNew({ commit, dispatch }, params) {
			const response = await api.get(`${url}/new`, params);
			return response.data.data;
		},
		async setPassword({ commit, dispatch }, params) {
			const response = await api.post(`${url}/${params.id}/set_password`, {
				password: params.password,
				confirm_password: params.confirm_password,
			});
			return response.data.data;
		},
		async enable({ commit, dispatch }, params) {
			const response = await api.post(`${url}/${params.id}/enable`, params);
			return response.data.data;
		},
		async disable({ commit, dispatch }, params) {
			const response = await api.post(`${url}/${params.id}/disable`, params);
			return response.data.data;
		},
		async unlock({ commit, dispatch }, params) {
			const response = await api.post(`${url}/${params.id}/unlock`, params);
			return response.data.data;
		},
		async enableAll({ commit, dispatch }, params) {
			const response = await api.post(`${url}/enableAll`, {
				_ids: params.ids,
			});
			return response.data.data;
		},
		async disableAll({ commit, dispatch }, params) {
			const response = await api.post(`${url}/disableAll`, {
				_ids: params.ids,
			});
			return response.data.data;
		},
	},
};
