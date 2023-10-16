import Vue from "vue";
import Router from "vue-router";

// Top Level Pages
import Dashboard from "@/vendors/views/Dashboard.vue";

// CRUD Objects
import Cases from "./cases";

// Various Areas
import Error404 from "@/vendors/views/Error/Error404.vue";

Vue.use(Router);

const router = new Router({
	mode: "history",
	base: "/vendor/",
	routes: [
		{
			path: "/",
			name: "dashboard",
			component: Dashboard,
		},
		...Cases,
		{
			path: "*",
			component: Error404,
		},
	],
});

export default router;
