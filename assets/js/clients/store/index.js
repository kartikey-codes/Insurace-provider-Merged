import Vue from "vue";
import Vuex from "vuex";
import api from "@/api";
import router from "@/clients/router";

/**
 * App-wide Vuex Store
 */

const logoutUrl = "/logout";

/**
 * Services
 */
import { getState as GetAppState, getStartup as GetAppStartup } from "@/clients/services/state";

/**
 * Modules
 */
// Auth
import auth from "./auth";

// App
import agencies from "./agencies";
import auditReviewers from "./auditReviewers";
import appeals from "./appeals";
import appealFiles from "./appealFiles";
import appealLevels from "./appealLevels";
import appealNotes from "./appealNotes";
import appealPackets from "./appealPackets";
import appealTypes from "./appealTypes";
import calendar from "./calendar";
import caseFiles from "./caseFiles";
import caseOutcomes from "./caseOutcomes";
import caseRequests from "./caseRequests";
import caseTypes from "./caseTypes";
import cases from "./cases";
import charts from "./charts";
import clients from "./clients";
import clientEmployees from "./clientEmployees";
import clientSettings from "./clientSettings";
import clientTypes from "./clientTypes";
import dashboard from "./dashboard";
import daysToRespondFroms from "./daysToRespondFroms";
import denialReasons from "./denialReasons";
import denialTypes from "./denialTypes";
import disciplines from "./disciplines";
import facilities from "./facilities";
import facilityTypes from "./facilityTypes";
import guestPortals from "./guestPortals";
import incomingDocuments from "./incomingDocuments";
import insuranceProviders from "./insuranceProviders";
import insuranceTypes from "./insuranceTypes";
import integrations from "./integrations";
import licenses from "./licenses";
import notDefendableReasons from "./notDefendableReasons";
import outgoingDocuments from "./outgoingDocuments";
import patients from "./patients";
import permissions from "./permissions";
import referenceNumbers from "./referenceNumbers";
import roles from "./roles";
import services from "./services";
import specialties from "./specialties";
import states from "./states";
import statistics from "./statistics";
import subscription from "./subscription";
import users from "./users";
import utcReasons from "./utcReasons";
import withdrawnReasons from "./withdrawnReasons";

Vue.use(Vuex);

const state = {
	appConfig: {
		isClientOwner: null,
		subscriptionsEnabled: null,
		licensingEnabled: null,
	},
	appName: "App",
	apiToken: null,
	baseURL: api.getBaseUrl(),
	clientName: "",
	user: {
		id: null,
		client_id: null,
		first_name: null,
		last_name: null,
		full_name: null,
		initials: null,
		admin: null,
		client_admin: null,
	},
	admin: false,
	openCaseCount: 0,
	openCaseRequestCount: 0,
	openAppealCount: 0,
	adminClientId: null,
	vendorServiceName: "RevKeep",
};

const getters = {
	appName: (state) => state.appName,
	initialLoaded: (state) => (state.user.id ? true : false),
	apiToken: (state) => state.apiToken,
	baseURL: (state) => state.baseURL,
	user: (state) => state.user,
	userId: (state) => state.user.id,
	userFullName: (state) => state.user.full_name || "",
	userInitials: (state) => state.user.initials || "",
	clientId: (state) => state.adminClientId || state.user.client_id || null,
	clientName: (state) => state.clientName || "",
	isClientOwner: (state) => state.appConfig?.isClientOwner || false,
	isClientAdmin: (state) => (state.user && state.user.client_admin ? true : false),
	isVendor: (state) => (state.user && state.user.vendor_id ? true : false),
	isAdmin: (state) => (state.user && state.user.admin ? true : false),
	openCaseCount: (state) => state.openCaseCount,
	openCaseRequestCount: (state) => state.openCaseRequestCount,
	openAppealCount: (state) => state.openAppealCount,
	adminClientId: (state) => state.adminClientId,
	vendorServiceName: (state) => state.vendorServiceName,
};

const mutations = {
	setAppConfig: (state, payload) => (state.appConfig = payload),
	setAppName: (state, payload) => (state.appName = payload),
	setClientName: (state, payload) => (state.clientName = payload),
	setApiToken: (state, payload) => (state.apiToken = payload),
	setUser: (state, payload) => (state.user = payload),
	setAdmin: (state, payload) => (state.admin = payload),
	setOpenCaseCount: (state, payload) => (state.openCaseCount = payload),
	setOpenCaseRequestCount: (state, payload) => (state.openCaseRequestCount = payload),
	setOpenAppealCount: (state, payload) => (state.openAppealCount = payload),
	setAdminClientId: (state, payload) => {
		state.adminClientId = payload;
		api.defaults.headers.common["Admin-Client-Id"] = payload;
	},
};

const actions = {
	/**
	 * Confirm log out
	 */
	logOut({ commit, dispatch }, params) {
		if (confirm("Are you sure you want to log out?")) {
			window.location = logoutUrl;
		}
	},
	/**
	 * Initialize admin client ID
	 * (Written by Logic)
	 */
	async initAdminClientId({ commit, dispatch, getters }, params) {
		if (params.default) {
			return dispatch("setAdminClientId", params.default);
		}

		let currentAdminClientId = await getters.adminClientId;
		if (currentAdminClientId != null) {
			console.log("current adminClientId is " + currentAdminClientId + ", skip initAdminClientId");
			return Promise.resolve();
		}

		if (router.currentRoute.query.clientId) {
			let adminClientId = parseInt(router.currentRoute.query.clientId);
			console.log("initAdminClientId: set adminClientId to client id from url: " + adminClientId);
			return dispatch("setAdminClientId", adminClientId);
		}

		let clients = await dispatch("clients/getActive");
		if (clients.length > 0) {
			let adminClientId = clients[0].id;
			console.log("initAdminClientId: set adminClientId to first active client's id " + adminClientId);
			return dispatch("setAdminClientId", adminClientId);
		} else {
			console.log("ERROR: no clients returned from API, unable to set admin client id for admin user.");
			console.log(clients);
			return Promise.resolve();
		}
	},
	/**
	 * Run multiple vuex actions when starting up or
	 * switching client
	 * (Written by Logic)
	 */
	async initAllStates({ commit, dispatch }, params) {
		let actions = [
			// Global / User / Auth
			"getStartup",
			"updateState",
			"users/getActive",
			"licenses/get",
		];

		var promises = [];
		actions.forEach(function (action, index) {
			promises.push(dispatch(action));
		});
		return Promise.all(promises);
	},
	/**
	 * Admin Client Switcher
	 * (Written by Logic)
	 */
	async setAdminClientId({ commit, dispatch, getters }, params) {
		console.log("setAdminClientId action params=" + params);
		let currentAdminClientId = await getters.adminClientId;
		let newAdminClientId = params;
		if (newAdminClientId != null && currentAdminClientId == newAdminClientId) {
			console.log("new AdminClientId is the same as current one, do nothing");
			return Promise.resolve();
		}
		await commit("setAdminClientId", newAdminClientId); // can't call action from mutation, but can call mutation from action
		return dispatch("initAllStates").then(() => {
			console.log("setAdminClientId action finished");
		});
	},
	/**
	 * Get all lists of things we need when starting the SPA
	 */
	async getStartup({ commit, dispatch }, params) {
		const response = await GetAppStartup();

		// Client Name
		commit("setClientName", response.clientName ?? "");

		// Permissions
		commit("permissions/setCurrentUser", response.myPermissions || []);

		// Auth
		commit("roles/setAll", response.roles);
		commit("roles/setLoadingAll", false);

		// Application
		commit("agencies/setActive", response.agencies);
		commit("agencies/setLoadingActive", false);

		commit("auditReviewers/setActive", response.auditReviewers);
		commit("auditReviewers/setLoadingActive", false);

		commit("clientEmployees/setActive", response.clientEmployees);
		commit("clientEmployees/setLoadingActive", false);

		commit("disciplines/setAll", response.disciplines);
		commit("disciplines/setLoadingAll", false);

		commit("facilities/setActive", response.facilities);
		commit("facilities/setLoadingActive", false);

		commit("facilityTypes/setAll", response.facilityTypes);
		commit("facilityTypes/setLoadingAll", false);

		commit("insuranceProviders/setActive", response.insuranceProviders);
		commit("insuranceProviders/setLoadingActive", false);

		commit("insuranceTypes/setAll", response.insuranceTypes);
		commit("insuranceTypes/setLoadingAll", false);

		commit("referenceNumbers/setAll", response.referenceNumbers);
		commit("referenceNumbers/setLoadingAll", false);

		// Cases
		commit("caseOutcomes/setAll", response.caseOutcomes);
		commit("caseOutcomes/setLoadingAll", false);

		commit("caseTypes/setAll", response.caseTypes);
		commit("caseTypes/setLoadingAll", false);

		commit("denialTypes/setAll", response.denialTypes);
		commit("denialTypes/setLoadingAll", false);

		commit("denialReasons/setAll", response.denialReasons);
		commit("denialReasons/setLoadingAll", false);

		commit("notDefendableReasons/setAll", response.notDefendableReasons);
		commit("notDefendableReasons/setLoadingAll", false);

		commit("withdrawnReasons/setAll", response.withdrawnReasons);
		commit("withdrawnReasons/setLoadingAll", false);

		// Appeals
		commit("appealLevels/setAll", response.appealLevels);
		commit("appealLevels/setLoadingAll", false);

		commit("appealTypes/setAll", response.appealTypes);
		commit("appealTypes/setLoadingAll", false);

		commit("daysToRespondFroms/setAll", response.daysToRespondFroms);
		commit("daysToRespondFroms/setLoadingAll", false);

		commit("utcReasons/setAll", response.utcReasons);
		commit("utcReasons/setLoadingAll", false);
	},
	/**
	 * Periodically update various things on a timer / polling for new data
	 */
	async updateState({ commit, dispatch }, params) {
		try {
			const response = await GetAppState();
			commit("incomingDocuments/setCount", response.incomingDocumentCount);
			commit("outgoingDocuments/setCountNew", response.outgoingDocumentCount);
			commit("setOpenCaseCount", response.openCases);
			commit("setOpenCaseRequestCount", response.openCaseRequests);
			commit("setOpenAppealCount", response.openAppeals);
			commit("users/setOnline", response.onlineUsers);
			return response;
		} catch (e) {
			console.error("Update State Error", e);

			// dispatch("apiError", {
			// 	error: e,
			// 	message: "Failed to update state",
			// });

			if (e.response.status && e.response.status == 401) {
				//@DISABLED
				//window.location = '/logout?reason=state';
			}

			throw e;
		} finally {
			// Nothing
		}
	},
	/**
	 * Error Handling
	 */
	async apiError({ commit, dispatch }, params) {
		const errorCode = params.error?.code || "";
		const errorResponse = params.error?.response || null;
		const responseStatus = errorResponse.status || 500;
		const responseStatusText = errorResponse.statusText || "Internal Server Error";
		const responseMessage = errorResponse.data?.message || "";

		const title = params.title || "Error";
		const message = params.message || "No message provided";
		const variant = params.variant || "danger";
		const notify = params.notify !== undefined ? params.notify : true;
		const log = params.log !== undefined ? params.log : true;

		if (log) {
			console.error("API Error", {
				errorCode,
				responseStatus,
				responseStatusText,
				responseMessage,
				title,
				message,
				variant,
				params,
			});
		}

		if (notify) {
			// Workaround to proxying to global bootstrap-vue instance
			const vm = new Vue();

			vm.$bvToast.toast(message, {
				title: title,
				autoHideDelay: 5000,
				appendToast: true,
				solid: true,
				variant: variant,
			});
		}
	},

	/**
	 * Notifications
	 */
	async notify({ commit, dispatch }, params) {
		const variant = params.variant || "primary";
		const title = params.title || "Notification";
		const message = params.message || "No message provided";
		const notify = params.notify !== undefined ? params.notify : true;

		if (notify) {
			// Workaround to proxying to global bootstrap-vue instance
			const vm = new Vue();

			vm.$bvToast.toast(message, {
				title: title,
				autoHideDelay: 5000,
				appendToast: true,
				solid: true,
				variant: variant,
			});
		}
	},
};

const store = new Vuex.Store({
	state,
	getters,
	mutations,
	actions,
	modules: {
		agencies,
		clients,
		clientTypes,
		appeals,
		appealFiles,
		appealLevels,
		appealNotes,
		appealPackets,
		appealTypes,
		auditReviewers,
		auth,
		calendar,
		caseFiles,
		caseOutcomes,
		caseRequests,
		caseTypes,
		cases,
		charts,
		clientEmployees,
		clientSettings,
		dashboard,
		daysToRespondFroms,
		denialReasons,
		denialTypes,
		disciplines,
		facilities,
		facilityTypes,
		guestPortals,
		incomingDocuments,
		insuranceProviders,
		insuranceTypes,
		integrations,
		licenses,
		notDefendableReasons,
		outgoingDocuments,
		patients,
		permissions,
		referenceNumbers,
		roles,
		services,
		specialties,
		states,
		statistics,
		subscription,
		users,
		utcReasons,
		withdrawnReasons,
	},
});

export default store;
export const useStore = () => store;
