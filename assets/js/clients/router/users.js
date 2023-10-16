import Index from "@/clients/views/Users/index.vue";
import Invite from "@/clients/views/Users/invite.vue";
import Add from "@/clients/views/Users/add.vue";
import Edit from "@/clients/views/Users/edit.vue";
import View from "@/clients/views/Users/view.vue";

const baseUrl = "/users";

export default [
	{
		path: baseUrl,
		name: "users",
		component: Index,
		meta: {
			title: "Users",
		},
	},
	{
		path: `${baseUrl}/invite`,
		name: "users.invite",
		component: Invite,
		meta: {
			title: "Invite User",
		},
	},
	{
		path: `${baseUrl}/add`,
		name: "users.add",
		component: Add,
		meta: {
			title: "Add User",
		},
	},
	{
		path: `${baseUrl}/:id/edit`,
		name: "users.edit",
		component: Edit,
		meta: {
			title: "Edit User",
		},
	},
	{
		path: `${baseUrl}/:id`,
		name: "users.view",
		component: View,
		meta: {
			title: "View User",
		},
	},
];
