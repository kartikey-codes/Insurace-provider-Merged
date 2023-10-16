import Index from "@/clients/views/InsuranceProviders/index.vue";
import Add from "@/clients/views/InsuranceProviders/add.vue";
import Edit from "@/clients/views/InsuranceProviders/edit.vue";
import View from "@/clients/views/InsuranceProviders/view.vue";

const baseUrl = "/insurance-providers";

export default [
	{
		path: baseUrl,
		name: "insuranceProviders",
		component: Index,
		meta: {
			title: "Insurance Providers",
		},
	},
	{
		path: `${baseUrl}/add`,
		name: "insuranceProviders.add",
		component: Add,
		meta: {
			title: "Add Insurance Provider",
		},
	},
	{
		path: `${baseUrl}/:id/edit`,
		name: "insuranceProviders.edit",
		component: Edit,
		meta: {
			title: "Edit Insurance Provider",
		},
	},
	{
		path: `${baseUrl}/:id`,
		name: "insuranceProviders.view",
		component: View,
		meta: {
			title: "View Insurance Provider",
		},
	},
];
