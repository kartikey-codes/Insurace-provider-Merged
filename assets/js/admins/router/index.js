import Router from "vue-router";

import Dashboard from "@/admins/views/Dashboard.vue";

import Clients from "@/admins/views/Clients.vue";

import Vendors from "@/admins/views/Vendors.vue";

import UsersLayout from "@/admins/views/UsersLayout.vue";
import UsersIndex from "@/admins/views/UsersIndex.vue";
import UserView from "@/admins/views/UserView.vue";

import Error404 from "@/admins/views/Error/Error404.vue";

const router = new Router({
	mode: "history",
	base: "/admin/",
	routes: [
		{
			path: "/",
			name: "dashboard",
			component: Dashboard,
		},
		{
			path: "/clients",
			name: "clients",
			component: Clients,
			meta: {
				title: "Clients",
			},
		},
		{
			path: "/vendors",
			name: "vendors",
			component: Vendors,
			meta: {
				title: "Vendors",
			},
		},
		{
			path: "/users",
			component: UsersLayout,
			children: [
				{
					path: "",
					name: "users",
					component: UsersIndex,
					meta: {
						title: "Users",
					},
				},
				{
					path: ":id/view",
					name: "users.view",
					component: UserView,
					meta: {
						title: "View User",
					},
				},
			],
		},
		{
			path: "*",
			component: Error404,
		},
	],
});

export default router;
