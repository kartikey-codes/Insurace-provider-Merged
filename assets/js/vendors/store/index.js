import Vue from "vue";
import Vuex from "vuex";
import api from "@/api";
import router from '@/clients/router';

const baseURL = document.head.querySelector('meta[name="api-base-url"]');
const logoutUrl = "/logout";

/**
 * Modules
 */
import appeals from "./appeals";
import appealLevels from "./appealLevels";
import appealTypes from "./appealTypes";
import caseFiles from "./caseFiles";
import cases from "./cases";
import daysToRespondFroms from "./daysToRespondFroms";
import notDefendableReasons from "./notDefendableReasons";
import vendors from "./vendors";

Vue.use(Vuex);

const state = {
	/**
	 * App
	 */
	appName: "App",
	apiToken: null,
	baseURL: baseURL.content || "/vendor/api",
	user: false,
	adminVendorId: null,

	/**
	 * Filters
	 */
	appealFilters: {
		status: null,
		appeal_level_id: null,
		appeal_type_id: null
	},

	/**
	 * Statuses
	 */
	// Copied from /src/Model/Entity/Appeal
	appealStatuses: [
		{
			value: 'Open',
			name: 'Open'
		},
		{
			value: 'Submitted',
			name: 'Submitted'
		},
		{
			value: 'Assigned',
			name: 'Assigned'
		},
		{
			value: 'Completed',
			name: 'Completed'
		},
		{
			value: 'Returned',
			name: 'Returned'
		},
		{
			value: 'Cancelled',
			name: 'Cancelled'
		},
		{
			value: 'closed',
			name: 'Closed'
		},
	],

	/**
	 * Static
	 */
	defendableOptions: [
		{
			value: true,
			name: 'Defendable'
		},
		{
			value: false,
			name: 'Not Defendable'
		}
	]
};

const getters = {
	/**
	 * App
	 */
	appName: state => state.appName,
	initialLoaded: state => state.user ? true : false,
	apiToken: state => state.apiToken,
	baseURL: state => state.baseURL,
	user: state => state.user,
	vendorId: state => state.adminVendorId || state.user.vendor_id || null,
	isAdmin: state => state.user && state.user.admin ? true : false,
	isClient: state => state.user && (state.user.client_id || state.user.admin) ? true : false,
	isVendor: state => state.user && (state.user.vendor_id || state.user.admin) ? true : false,
	adminVendorId: state => state.adminVendorId,

	/**
	 * Filters
	 */
	appealFilters: state => state.appealFilters,

	/**
	 * Statuses
	 */
	appealStatuses: state => state.appealStatuses,

	/**
	 * Static
	 */
	defendableOptions: state => state.defendableOptions
};

const mutations = {
	/**
	 * App
	 */
	setAppName: (state, payload) => state.appName = payload,
	setApiToken: (state, payload) => state.apiToken = payload,
	setUser: (state, payload) => state.user = payload,
	setAdmin: (state, payload) => state.admin = payload,
	setAdminVendorId: (state, payload) => {
		state.adminVendorId = payload;
		api.defaults.headers.common['Admin-Vendor-Id'] = payload;
	},

	/**
	 * Filters
	 */
	setAppealFilters: (state, payload) => state.appealFilters = payload,

	/**
	 * Index / Lists
	 */
	setDefendableOptions: (state, payload) => state.defendableOptions = payload,
};

const actions = {
	logOut({ commit, dispatch }, params) {
		if (confirm("Are you sure you want to log out?")) {
			window.location = logoutUrl;
		}
	},
	async initAdminVendorId({ commit, dispatch, getters }, params) {
		let currentAdminVendorId = await getters.adminVendorId;
		
		if (currentAdminVendorId != null) {
			console.log("current adminVendorId is " + currentAdminVendorId + ", skip initAdminVendorId");
			return Promise.resolve();
		}

		if (router.currentRoute.query.vendorId) {
			let adminVendorId = parseInt(router.currentRoute.query.vendorId);
			console.log('initAdminVendorId: set adminVendorId to vendor id from url: ' + adminVendorId);
			return dispatch('setAdminVendorId', adminVendorId);
		}

		let vendors = await dispatch("vendors/getActive");
		if (vendors.length > 0) {
			let adminVendorId = vendors[0].id;
			console.log('initAdminVendorId: set adminVendorId to first active vendor\'s id ' + adminVendorId);
			return dispatch('setAdminVendorId', adminVendorId);
		}
		else {
			console.log('ERROR: no vendors returned from API, unable to set admin vendor id for admin user.');
			console.log(vendors);
			return Promise.resolve();
		}
	},
	async initAllStates({ commit, dispatch }, params) {
		let actions = [
			// Global / User / Auth
			"getStartup"
		];

		var promises = [];
		actions.forEach(function (action, index) {
			promises.push(dispatch(action));
		});
		return Promise.all(promises);
	},
	async setAdminVendorId({ commit, dispatch, getters }, params) {
		console.log('setAdminVendorId action params=' + params);
		let currentAdminVendorId = await getters.adminVendorId;
		let newAdminVendorId = params;
		if (newAdminVendorId != null && currentAdminVendorId == newAdminVendorId) {
			console.log('new AdminVendorId is the same as current one, do nothing');
			return Promise.resolve();
		}
		await commit('setAdminVendorId', newAdminVendorId); // can't call action from mutation, but can call mutation from action
		return dispatch("initAllStates").then(() => {
			console.log('setAdminVendorId action finished');
		});
	},
	async getStartup({ commit, dispatch }, params) {
		// Get lists needed for Vuex from API:
		// /src/Api/DashboardController -> startup()
		const response = await api.get('/dashboard/startup');
		const data = response.data;

		// Application
		/*
			commit("clients/setAll", data.clients);
			commit("clients/setLoadingAll", false);

			commit("clientTypes/setAll", data.clientTypes);

			commit("facilities/setAll", data.facilities);
			commit("facilities/setLoadingAll", false);

			commit("facilityTypes/setAll", data.facilityTypes);
			commit("facilityTypes/setLoadingAll", false);

			commit("insuranceProviders/setAll", data.insuranceProviders);
			commit("insuranceProviders/setLoadingAll", false);

			commit("insuranceTypes/setAll", data.insuranceTypes);
			commit("insuranceTypes/setLoadingAll", false);

			commit("referenceNumbers/setAll", data.referenceNumbers);
			commit("referenceNumbers/setLoadingAll", false);

			// Cases
			commit("caseOutcomes/setAll", data.caseOutcomes);
			commit("caseOutcomes/setLoadingAll", false);

			commit("caseTypes/setAll", data.caseTypes);
			commit("caseTypes/setLoadingAll", false);

			commit("denialTypes/setAll", data.denialTypes);
			commit("denialTypes/setLoadingAll", false);

			commit("withdrawnReasons/setAll", data.withdrawnReasons);
			commit("withdrawnReasons/setLoadingAll", false);
		*/

		// Appeals
		commit("appealLevels/setAll", data.appealLevels);
		commit("appealLevels/setLoadingAll", false);

		commit("appealTypes/setAll", data.appealTypes);
		commit("appealTypes/setLoadingAll", false);

		commit("daysToRespondFroms/setAll", data.daysToRespondFroms);
		commit("daysToRespondFroms/setLoadingAll", false);

		commit("notDefendableReasons/setAll", data.notDefendableReasons);
		commit("notDefendableReasons/setLoadingAll", false);
	},
};

export default new Vuex.Store({
	state,
	getters,
	mutations,
	actions,
	modules: {
		appeals,
		appealLevels,
		appealTypes,
		cases,
		caseFiles,
		daysToRespondFroms,
		notDefendableReasons,
		vendors
	}
});
