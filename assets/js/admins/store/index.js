import Vue from "vue";
import Vuex from "vuex";
import api from "@/api";

import clients from "./clients";
import states from "./states";
import users from "./users";
import vendors from "./vendors";

Vue.use(Vuex);

const logoutUrl = "/logout";
const baseURL = document.head.querySelector('meta[name="api-base-url"]');

const state = {
	appName: "App",
	user: {
		id: null,
		admin: null,
		client_id: null,
		vendor_id: null
	},
	loadingStatistics: null,
	statistics: {
		app: {},
		versions: {},
		drivers: {}
	},
	assigningAppeals: null
};

const getters = {
	appName: state => state.appName,
	user: state => state.user,
	isClient: state => state.user && state.user.client_id ? true : false,
	isVendor: state => state.user && state.user.vendor_id ? true : false,
	isAdmin: state => state.admin,
	statistics: state => state.statistics,
	loadingStatistics: state => state.loadingStatistics,
	assigningAppeals: state => state.assigningAppeals
};

const mutations = {
	setAppName: (state, payload) => state.appName = payload,
	setUser: (state, payload) => state.user = payload,
	setStatistics: (state, payload) => state.statistics = payload,
	setLoadingStatistics: (state, payload) => state.loadingStatistics = payload,
	setAssigningAppeals: (state, payload) => state.assigningAppeals = payload
};

const actions = {
	async getStatistics({ commit, dispatch }, params) {
		try {
			commit('setLoadingStatistics', true);
			const response = await api.get(`statistics`, params);
			commit('setStatistics', response.data);
			return response.data;
		} finally {
			commit('setLoadingStatistics', false);
		}
	},
	async assignAppealsToVendors({ commit, dispatch }, params) {
		try {
			commit('setAssigningAppeals', true);
			const response = await api.get(`/actions/assignAppeals`, params);
			return response.data.data;
		} finally {
			commit('setAssigningAppeals', false);
		}
	},
};

export default new Vuex.Store({
	state,
	getters,
	mutations,
	actions,
	modules: {
		clients,
		states,
		users,
		vendors
	}
});
