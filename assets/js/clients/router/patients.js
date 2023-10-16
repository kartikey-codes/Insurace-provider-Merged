import Index from "@/clients/views/Patients/index.vue";
import Add from "@/clients/views/Patients/add.vue";
import Edit from "@/clients/views/Patients/edit.vue";
import View from "@/clients/views/Patients/view.vue";

const baseUrl = "/patients";

export default [
	{
		path: baseUrl,
		name: "patients",
		component: Index,
		meta: {
			title: "Patients",
		},
	},
	{
		path: `${baseUrl}/add`,
		name: "patients.add",
		component: Add,
		meta: {
			title: "Add Patient",
		},
	},
	{
		path: `${baseUrl}/:id/edit`,
		name: "patients.edit",
		component: Edit,
		meta: {
			title: "Edit Patient",
		},
	},
	{
		path: `${baseUrl}/:id`,
		name: "patients.view",
		component: View,
		meta: {
			title: "View Patient",
		},
	},
];
