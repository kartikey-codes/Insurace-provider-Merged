/**
 * Load additional components
 */
import Vue from "vue";
import VueRouter from "vue-router";

import store from "@/admins/store";
import BootstrapVue from "bootstrap-vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { ValidationProvider, ValidationObserver, extend, localize } from "vee-validate";
import en from "vee-validate/dist/locale/en.json";
import * as rules from "vee-validate/dist/rules";
import { VueMaskDirective } from "v-mask";

// App Modules
import filters from "@/shared/filters";
import router from "@/admins/router";

require("./fontawesome");

// Install VeeValidate rules and localization
Object.keys(rules).forEach((rule) => {
	extend(rule, rules[rule]);
});

localize("en", en);

Vue.component("font-awesome-icon", FontAwesomeIcon);
Vue.component("validation-provider", ValidationProvider);
Vue.component("validation-observer", ValidationObserver);

Vue.directive("mask", VueMaskDirective);

//Vue.use(filters);
Vue.use(BootstrapVue);
Vue.use(VueRouter);

/**
 * App Component
 */
import App from "@/admins/views/App.vue";

// Grab user from window data (php)
Vue.prototype.$appName = window.appName || "App";
Vue.prototype.$authUser = window.authUser;
store.commit("setAppName", appName);
store.commit("setUser", authUser);

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
