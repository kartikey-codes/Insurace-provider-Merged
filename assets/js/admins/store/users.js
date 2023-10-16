import api from "@/api";
const url = "/users";

export default {
	namespaced: true,
	state: {
		loadingAll: null,
		all: [],
		loadingActive: null,
		active: [],
		loadingOnline: null,
		online: [],
	},
	getters: {
		loadingAll: state => state.loadingAll === null || state.loadingAll,
		all: state => state.all,
		loadingActive: state => state.loadingActive === null || state.loadingActive,
		active: state => state.active,
		loadingOnline: state => state.loadingOnline === null || state.loadingOnline,
		online: state => state.online,
	},
	mutations: {
		setLoadingAll: (state, payload) => state.loadingAll = payload,
		setAll: (state, payload) => state.all = payload,
		setLoadingActive: (state, payload) => state.loadingActive = payload,
		setActive: (state, payload) => state.active = payload,
		setLoadingOnline: (state, payload) => state.loadingOnline = payload,
		setOnline: (state, payload) => state.online = payload
	},
	actions: {
		async index({ commit, dispatch }, params) {
			const response = await api.get(url, {
				params: params
			});
			return response.data; // { data: [], pagination: [] }
		},
		async get({ commit, dispatch }, params) {
			const response = await api.get(`${url}/view/${params.id}`, params);
			return response.data.data;
		},
		async save({ commit, dispatch }, entity) {
			if (entity.id) {
				return await dispatch('update', entity);
			} else {
				return await dispatch('create', entity);
			}
		},
		async create({ commit, dispatch }, params) {
			const response = await api.post(`${url}`, params);
			return response.data.data;
		},
		async update({ commit, dispatch }, params) {
			const response = await api.patch(`${url}/${params.id}`, params);
			return response.data.data;
		},
		async delete({ commit, dispatch }, params) {
			const response = await api.delete(`${url}/${params.id}`, {
				_method: "DELETE"
			});
			return response.data;
		},
		async getAll({ commit, dispatch }, params) {
			commit('setLoadingAll', true);
			const response = await api.get(`${url}/all`, params);
			commit('setAll', response.data.data);
			commit('setLoadingAll', false);
			return response.data.data;
		},
		async getActive({ commit, dispatch }, params) {
			commit('setLoadingActive', true);
			const response = await api.get(`${url}/active`, params);
			commit('setActive', response.data.data);
			commit('setLoadingActive', false);
			return response.data.data;
		},
		async getOnline({ commit, dispatch }, params) {
			commit('setLoadingOnline', true);
			const response = await api.get(`${url}/online`, params);
			commit('setOnline', response.data.data);
			commit('setLoadingOnline', false);
			return response.data.data;
		}
	}
}
