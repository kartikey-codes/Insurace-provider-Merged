/**
 * Load additional components
 *
 */
import Vue from "vue";
import VueRouter from "vue-router";

// Vue Plugins
import BootstrapVue from "bootstrap-vue";
import Multiselect from "vue-multiselect";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { ValidationProvider, ValidationObserver } from "vee-validate";
import { VueMaskDirective } from "v-mask";

// App Modules
import filters from "@/shared/filters";
import router from "@/vendors/router";
import store from "@/vendors/store";

/**
 * App Component
 */
import App from "@/vendors/views/App.vue";

// Grab user from window data (php)
Vue.prototype.$appName = window.appName || "App";
Vue.prototype.$authUser = window.authUser;
store.commit("setAppName", appName);
store.commit("setUser", authUser);

if (authUser.admin) {
	store.commit("setAdmin", true);
	store.dispatch("initAdminVendorId");
} else {
	store.commit("setAdmin", false);
	store.dispatch("setAdminVendorId", null);
}

/**
 * Font-Awesome
 */
require("./fontawesome");

/**
 * Start loading global vue components
 */
window.Vue = require("vue");
//Vue.use(filters);
Vue.use(VueRouter);
Vue.use(BootstrapVue);

// VeeValidate has an issue with other components using 'fields' as a prop
// Vue.use(VeeValidate, {
//  fieldsBagName: 'formFields',
//  mode: 'eager'
// });

// V-Mask
Vue.directive("mask", VueMaskDirective);

/**
 * Apply API token from DOM to Vuex
 */
var apiToken = document.head.querySelector('meta[name="api-token"]');
store.commit("setApiToken", apiToken.content);

store.dispatch("getStartup");

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vendor
Vue.component("font-awesome-icon", FontAwesomeIcon);
Vue.component("validation-provider", ValidationProvider);
Vue.component("validation-observer", ValidationObserver);
Vue.component("multiselect", Multiselect);

// Application

import LoadingIndicator from "@/clients/components/Layout/loading-indicator.vue";
import PageHeader from "@/vendors/components/Layout/page-header.vue";
import Pagination from "@/clients/components/Layout/pagination.vue";
import EmptyResult from "@/clients/components/Layout/empty-result.vue";
import ErrorAlert from "@/clients/components/Layout/error-alert.vue";

Vue.component("empty-result", EmptyResult);
Vue.component("error-alert", ErrorAlert);
Vue.component("loading-indicator", LoadingIndicator);
Vue.component("page-header", PageHeader);
Vue.component("pagination", Pagination);

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
