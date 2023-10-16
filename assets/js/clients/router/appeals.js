import Index from "@/clients/views/Appeals/index.vue";
import IndexHome from "@/clients/views/Appeals/index_home.vue";
import IndexCancelled from "@/clients/views/Appeals/index_cancelled.vue";
import IndexClosed from "@/clients/views/Appeals/index_closed.vue";
import IndexOpen from "@/clients/views/Appeals/index_open.vue";
import IndexUTC from "@/clients/views/Appeals/index_utc.vue";
import IndexUnassigned from "@/clients/views/Appeals/index_unassigned.vue";

const baseUrl = "/appeals";

export default [
	{
		path: `${baseUrl}`,
		component: Index,
		meta: {
			title: "Appeals",
		},
		children: [
			{
				path: "",
				name: "appeals",
				component: IndexHome,
			},
			{
				path: "open",
				name: "appeals.index.open",
				component: IndexOpen,
			},
			{
				path: "utc",
				name: "appeals.index.utc",
				component: IndexUTC,
			},
			{
				path: "cancelled",
				name: "appeals.index.cancelled",
				component: IndexCancelled,
			},
			{
				path: "closed",
				name: "appeals.index.closed",
				component: IndexClosed,
			},
			{
				path: "unassigned",
				name: "appeals.index.unassigned",
				component: IndexUnassigned,
			},
		],
	},
];
