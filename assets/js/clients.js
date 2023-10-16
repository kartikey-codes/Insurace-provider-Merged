/**
 * Load additional components
 *
 */
import Vue from "vue";
import VueRouter from "vue-router";

// Vue Plugins
import BootstrapVue from "bootstrap-vue";
import Multiselect from "vue-multiselect";
import { FontAwesomeIcon, FontAwesomeLayers, FontAwesomeLayersText } from "@fortawesome/vue-fontawesome";
import { ValidationProvider, ValidationObserver, extend, localize } from "vee-validate";
import en from "vee-validate/dist/locale/en.json";
import * as rules from "vee-validate/dist/rules";
import { VueMaskDirective } from "v-mask";

// App Modules
import filters from "@/shared/filters";
import router from "@/clients/router";
import store from "@/clients/store";

/**
 * App Component
 */
import App from "@/clients/views/App.vue";

// Grab user from window data (php)
Vue.prototype.$appConfig = window.appConfig || {};
Vue.prototype.$appName = window.appName || "App";
Vue.prototype.$authUser = window.authUser || {};
const clientId = window.authUser?.client_id || null;

store.commit("setAppConfig", appConfig);
store.commit("setAppName", appName);
store.commit("setUser", authUser);

if (authUser.admin) {
	store.commit("setAdmin", true);
	store.dispatch("initAdminClientId", {
		default: clientId,
	});
} else {
	store.commit("setAdmin", false);
	store.dispatch("setAdminClientId", null);
}

/**
 * Font-Awesome
 */
require("./fontawesome");

// Install VeeValidate rules and localization
Object.keys(rules).forEach((rule) => {
	extend(rule, rules[rule]);
});

localize("en", en);

/**
 * Start loading global vue components
 */
window.Vue = require("vue");
//Vue.use(filters);
Vue.use(VueRouter);
Vue.use(BootstrapVue);

// VeeValidate has an issue with other components using 'fields' as a prop
// Vue.use(VeeValidate, {
// 	fieldsBagName: 'formFields',
// 	mode: 'eager'
// });

// V-Mask
Vue.directive("mask", VueMaskDirective);

/**
 * Apply API token from DOM to Vuex
 */
var apiToken = document.head.querySelector('meta[name="api-token"]');
store.commit("setApiToken", apiToken.content);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vendor
Vue.component("font-awesome-icon", FontAwesomeIcon);
Vue.component("font-awesome-layers", FontAwesomeLayers);
Vue.component("font-awesome-layers-text", FontAwesomeLayersText);
Vue.component("validation-provider", ValidationProvider);
Vue.component("validation-observer", ValidationObserver);
Vue.component("multiselect", Multiselect);

// Application
import EmptyResult from "@/clients/components/Layout/empty-result.vue";
import EntityTypeahead from "@/clients/components/Layout/entity-typeahead.vue";
import ErrorAlert from "@/clients/components/Layout/error-alert.vue";
import FormGroup from "@/clients/components/Layout/form-group.vue";
import LoadingIndicator from "@/clients/components/Layout/loading-indicator.vue";
import ManageViewHeader from "@/clients/components/Layout/manage-view-header.vue";
import PageHeader from "@/clients/components/Layout/page-header.vue";
import Pagination from "@/clients/components/Layout/pagination.vue";
import PaginatedResults from "@/clients/components/Layout/paginated-results.vue";
import SearchInput from "@/clients/components/Layout/search-input.vue";
import SimplePagination from "@/clients/components/Layout/simple-pagination.vue";
import ValidationErrors from "@/clients/components/Layout/validation-errors.vue";

Vue.component("empty-result", EmptyResult);
Vue.component("entity-typeahead", EntityTypeahead);
Vue.component("error-alert", ErrorAlert);
Vue.component("form-group", FormGroup);
Vue.component("loading-indicator", LoadingIndicator);
Vue.component("manage-view-header", ManageViewHeader);
Vue.component("page-header", PageHeader);
Vue.component("pagination", Pagination);
Vue.component("paginated-results", PaginatedResults);
Vue.component("search-input", SearchInput);
Vue.component("simple-pagination", SimplePagination);
Vue.component("validation-errors", ValidationErrors);

// Load filters globally on the prototype
// This is for preparing to move to Vue 3
Vue.prototype.$filters = filters;

// Bootstrap application
const app = new Vue({
	el: "#app",
	components: {
		App,
	},
	created() {
		console.log("Application started. Built with ❤️ by Kyle Weishaupt.");
	},
	router,
	store,
	render: (h) => h(App),
});

// Export
export default app;
