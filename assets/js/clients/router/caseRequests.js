import Index from "@/clients/views/CaseRequests/index.vue";
import IndexHome from "@/clients/views/CaseRequests/index_home.vue";
import IndexCompleted from "@/clients/views/CaseRequests/index_completed.vue";
import IndexOpen from "@/clients/views/CaseRequests/index_open.vue";
import IndexUTC from "@/clients/views/CaseRequests/index_utc.vue";
import IndexUnassigned from "@/clients/views/CaseRequests/index_unassigned.vue";

const baseUrl = "/requests";

export default [
	{
		path: `${baseUrl}`,
		component: Index,
		meta: {
			title: "Requests",
		},
		children: [
			{
				path: "",
				name: "caseRequests",
				component: IndexHome,
			},
			{
				path: "open",
				name: "caseRequests.index.open",
				component: IndexOpen,
			},
			{
				path: "utc",
				name: "caseRequests.index.utc",
				component: IndexUTC,
			},
			{
				path: "completed",
				name: "caseRequests.index.completed",
				component: IndexCompleted,
			},
			{
				path: "unassigned",
				name: "caseRequests.index.unassigned",
				component: IndexUnassigned,
			},
		],
	},
];
