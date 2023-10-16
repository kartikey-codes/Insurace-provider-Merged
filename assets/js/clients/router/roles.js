import Index from "@/clients/views/Roles/index.vue";
import Add from "@/clients/views/Roles/add.vue";
import Edit from "@/clients/views/Roles/edit.vue";
import View from "@/clients/views/Roles/view.vue";

const baseUrl = "/roles";

export default [
	{
		path: baseUrl,
		name: "roles",
		component: Index,
		meta: {
			title: "Roles",
		},
	},
	{
		path: `${baseUrl}/add`,
		name: "roles.add",
		component: Add,
		meta: {
			title: "Add Role",
		},
	},
	{
		path: `${baseUrl}/:id/edit`,
		name: "roles.edit",
		component: Edit,
		meta: {
			title: "Edit Role",
		},
	},
	{
		path: `${baseUrl}/:id`,
		name: "roles.view",
		component: View,
		meta: {
			title: "View Role",
		},
	},
];
