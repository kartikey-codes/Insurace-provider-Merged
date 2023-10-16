import api from "@/api";
const url = '/days-to-respond-froms';

export default {
	namespaced: true,
	state: {
		loadingAll: null,
		all: []
	},
	getters: {
		loadingAll: state => state.loadingAll === null || state.loadingAll === null || state.loadingAll,
		all: state => state.all
	},
	mutations: {
		setLoadingAll: (state, payload) => state.loadingAll = payload,
		setAll: (state, payload) => state.all = payload
	},
	actions: {
		async find({ commit, dispatch }, params) {
			const response = await api.get(url, params);
			return response.data.data;
		},
		async get({ commit, dispatch }, params) {
			const response = await api.get(`${url}/view/${params.id}`, params);
			return response.data.data;
		},
		async getAll({ commit, dispatch }, params) {
			commit('setLoadingAll', true);
			const response = await api.get(`${url}/all`, params);
			commit('setAll', response.data.data);
			commit('setLoadingAll', false);
			return response.data.data;
		}
	}
}
