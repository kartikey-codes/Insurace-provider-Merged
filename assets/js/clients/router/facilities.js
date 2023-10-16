import Index from "@/clients/views/Facilities/index.vue";
import Add from "@/clients/views/Facilities/add.vue";
import AddNPI from "@/clients/views/Facilities/add_npi.vue";
import Edit from "@/clients/views/Facilities/edit.vue";
import View from "@/clients/views/Facilities/view.vue";

const baseUrl = "/facilities";

export default [
	{
		path: baseUrl,
		name: "facilities",
		component: Index,
		meta: {
			title: "Facilities",
		},
	},
	{
		path: `${baseUrl}/add/npi`,
		name: "facilities.add.npi",
		component: AddNPI,
		meta: {
			title: "Add Facility From NPI Registry",
		},
	},
	{
		path: `${baseUrl}/add`,
		name: "facilities.add",
		component: Add,
		meta: {
			title: "Add Facility",
		},
	},

	{
		path: `${baseUrl}/:id/edit`,
		name: "facilities.edit",
		component: Edit,
		meta: {
			title: "Edit Facility",
		},
	},
	{
		path: `${baseUrl}/:id`,
		name: "facilities.view",
		component: View,
		meta: {
			title: "View Facility",
		},
	},
];
