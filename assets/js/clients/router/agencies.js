import Index from "@/clients/views/Agencies/index.vue";
import Add from "@/clients/views/Agencies/add.vue";
import Edit from "@/clients/views/Agencies/edit.vue";
import View from "@/clients/views/Agencies/view.vue";

const baseUrl = "/agencies";

export default [
	{
		path: baseUrl,
		name: "agencies",
		component: Index,
		meta: {
			title: "Agencies",
		},
	},
	{
		path: `${baseUrl}/add`,
		name: "agencies.add",
		component: Add,
		meta: {
			title: "Add Agency",
		},
	},
	{
		path: `${baseUrl}/:id/edit`,
		name: "agencies.edit",
		component: Edit,
		meta: {
			title: "Edit Agency",
		},
	},
	{
		path: `${baseUrl}/:id`,
		name: "agencies.view",
		component: View,
		meta: {
			title: "View Agency",
		},
	},
];
