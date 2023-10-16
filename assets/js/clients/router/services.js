import Index from "@/clients/views/Services/index.vue";
import Add from "@/clients/views/Services/add.vue";
import Edit from "@/clients/views/Services/edit.vue";
import View from "@/clients/views/Services/view.vue";

const baseUrl = "/services";

export default [
	{
		path: baseUrl,
		name: "services",
		component: Index,
		meta: {
			title: "Services",
		},
	},
	{
		path: `${baseUrl}/add`,
		name: "services.add",
		component: Add,
		meta: {
			title: "Add Service",
		},
	},
	{
		path: `${baseUrl}/:id/edit`,
		name: "services.edit",
		component: Edit,
		meta: {
			title: "Edit Service",
		},
	},
	{
		path: `${baseUrl}/:id`,
		name: "services.view",
		component: View,
		meta: {
			title: "View Service",
		},
	},
];
