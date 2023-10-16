import Index from "@/clients/views/IncomingDocuments/index.vue";
import IndexHome from "@/clients/views/IncomingDocuments/index_home.vue";
import IndexView from "@/clients/views/IncomingDocuments/index_view.vue";

const baseUrl = "/incoming";

export default [
	{
		path: baseUrl,
		component: Index,
		meta: {
			title: "Incoming Documents",
		},
		children: [
			{
				path: "",
				name: "incomingDocuments",
				component: IndexHome,
			},
			{
				path: "view/:id",
				name: "incomingDocuments.view",
				component: IndexView,
				meta: {
					title: "View Document",
				},
			},
		],
	},
];
