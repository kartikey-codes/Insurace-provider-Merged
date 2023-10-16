import Index from "@/clients/views/OutgoingDocuments/index.vue";
import IndexPending from "@/clients/views/OutgoingDocuments/index_pending.vue";
import IndexDelivered from "@/clients/views/OutgoingDocuments/index_delivered.vue";
import IndexFailed from "@/clients/views/OutgoingDocuments/index_failed.vue";
import IndexCancelled from "@/clients/views/OutgoingDocuments/index_cancelled.vue";

const baseUrl = "/outgoing";

export default [
	{
		path: baseUrl,
		component: Index,
		meta: {
			title: "Outgoing Documents",
		},
		children: [
			{
				path: "",
				name: "outgoingDocuments",
				component: IndexPending,
			},
			{
				path: "delivered",
				name: "outgoingDocuments.delivered",
				component: IndexDelivered,
			},
			{
				path: "failed",
				name: "outgoingDocuments.failed",
				component: IndexFailed,
			},
			{
				path: "cancelled",
				name: "outgoingDocuments.cancelled",
				component: IndexCancelled,
			},
		],
	},
];
