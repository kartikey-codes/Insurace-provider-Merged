import Index from "@/clients/views/Reports/index.vue";
import IndexHome from "@/clients/views/Reports/index_home.vue";
import IndexUsers from "@/clients/views/Reports/index_users.vue";

const baseUrl = "/reports";

export default [
	{
		path: baseUrl,
		component: Index,
		meta: {
			title: "Reports",
		},
		children: [
			{
				path: "",
				name: "reports",
				component: IndexHome,
			},
			{
				path: "users",
				name: "reports.users",
				component: IndexUsers,
			},
		],
	},
];
