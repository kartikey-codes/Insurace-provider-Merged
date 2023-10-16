import Index from "@/clients/views/AuditReviewers/index.vue";
import Add from "@/clients/views/AuditReviewers/add.vue";
import Edit from "@/clients/views/AuditReviewers/edit.vue";
import View from "@/clients/views/AuditReviewers/view.vue";

const baseUrl = "/audit-reviewers";

export default [
	{
		path: baseUrl,
		name: "auditReviewers",
		component: Index,
		meta: {
			title: "Audit Reviewers",
		},
	},
	{
		path: `${baseUrl}/add`,
		name: "auditReviewers.add",
		component: Add,
		meta: {
			title: "Add Audit Reviewer",
		},
	},
	{
		path: `${baseUrl}/:id/edit`,
		name: "auditReviewers.edit",
		component: Edit,
		meta: {
			title: "Edit Audit Reviewer",
		},
	},
	{
		path: `${baseUrl}/:id`,
		name: "auditReviewers.view",
		component: View,
		meta: {
			title: "View Audit Reviewer",
		},
	},
];
