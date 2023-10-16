import Index from "@/clients/views/Cases/index.vue";
import IndexHome from "@/clients/views/Cases/index_home.vue";
import IndexOpen from "@/clients/views/Cases/index_open.vue";
import IndexUtc from "@/clients/views/Cases/index_utc.vue";
import IndexClosed from "@/clients/views/Cases/index_closed.vue";
import IndexEmpty from "@/clients/views/Cases/index_empty.vue";

import Add from "@/clients/views/Cases/add.vue";
import Edit from "@/clients/views/Cases/edit.vue";
import View from "@/clients/views/Cases/view.vue";

import ViewHome from "@/clients/views/Cases/view_home.vue";
import ViewAppeal from "@/clients/views/Cases/view_appeal.vue";
import ViewRequest from "@/clients/views/Cases/view_request.vue";

const baseUrl = "/cases";

export default [
	{
		path: `${baseUrl}`,
		component: Index,
		meta: {
			title: "Cases",
		},
		children: [
			{
				path: "",
				name: "cases",
				component: IndexHome,
			},
			{
				path: "open",
				name: "cases.index.open",
				component: IndexOpen,
			},
			{
				path: "utc",
				name: "cases.index.utc",
				component: IndexUtc,
			},
			{
				path: "closed",
				name: "cases.index.closed",
				component: IndexClosed,
			},
			{
				path: "empty",
				name: "cases.index.empty",
				component: IndexEmpty,
			},
		],
	},
	{
		path: `${baseUrl}/add`,
		name: "cases.add",
		component: Add,
		meta: {
			title: "Add Case",
		},
	},
	{
		path: `${baseUrl}/:id/edit`,
		name: "cases.edit",
		component: Edit,
		meta: {
			title: "Edit Case",
		},
	},
	{
		path: "/cases/:id",
		component: View,
		meta: {
			title: "View Case",
		},
		children: [
			{
				path: "",
				name: "cases.view",
				component: ViewHome,
			},
			{
				path: "appeals/:appeal_id",
				name: "appeals.view",
				component: ViewAppeal,
				meta: {
					title: "View Appeal",
				},
			},
			{
				path: "requests/:case_request_id",
				name: "caseRequests.view",
				component: ViewRequest,
				meta: {
					title: "View Request",
				},
			},
		],
	},
];
