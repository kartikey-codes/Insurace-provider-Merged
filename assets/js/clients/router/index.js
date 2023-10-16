import Router from "vue-router";

// Top Level Pages
import Dashboard from "@/clients/views/Dashboard.vue";
import Calendar from "@/clients/views/Calendar.vue";
import Library from "@/clients/views/Library.vue";
import Manage from "@/clients/views/Manage.vue";

// Client / Auth / Settings
import Settings from "@/clients/views/Settings.vue";
import Account from "@/clients/views/Settings/Account.vue";
import Licenses from "@/clients/views/Settings/Licenses.vue";
import Subscription from "@/clients/views/Settings/Subscription.vue";
import Organization from "@/clients/views/Settings/Organization.vue";
import Integrations from "@/clients/views/Settings/Integrations.vue";

// CRUD Objects
import Agencies from "./agencies";
import Appeals from "./appeals";
import AuditReviewers from "./auditReviewers";
import CaseRequests from "./caseRequests";
import Cases from "./cases";
import ClientEmployees from "./clientEmployees";
import Facilities from "./facilities";
import IncomingDocuments from "./incomingDocuments";
import InsuranceProviders from "./insuranceProviders";
import OutgoingDocuments from "./outgoingDocuments";
import Patients from "./patients";
import Reports from "./reports";
import Roles from "./roles";
import Services from "./services";
import Users from "./users";

// 404 Error
import Error404 from "@/clients/views/Error/Error404.vue";

const router = new Router({
	mode: "history",
	base: "/client/",
	routes: [
		{
			path: "/",
			name: "dashboard",
			component: Dashboard,
		},
		{
			path: "/calendar",
			name: "calendar",
			component: Calendar,
		},
		{
			path: "/library",
			name: "library",
			component: Library,
		},
		{
			path: "/manage",
			name: "manage",
			component: Manage,
			children: [
				...Agencies,
				...AuditReviewers,
				...ClientEmployees,
				...Facilities,
				...InsuranceProviders,
				...Services,
			],
		},
		{
			path: "/settings",
			name: "settings",
			component: Settings,
			children: [
				...Roles,
				...Users,
				{
					path: "/account",
					name: "account",
					component: Account,
				},
				{
					path: "/organization",
					name: "organization",
					component: Organization,
				},
				{
					path: "/licenses",
					name: "licenses",
					component: Licenses,
				},
				{
					path: "/subscription",
					name: "subscription",
					component: Subscription,
				},
				{
					path: "integrations",
					name: "settings.integrations",
					component: Integrations,
					meta: {
						title: "Integrations",
					},
				},
			],
		},
		...Appeals,
		...CaseRequests,
		...Cases,
		...IncomingDocuments,
		...OutgoingDocuments,
		...Patients,
		...Reports,
		{
			path: "*",
			component: Error404,
		},
	],
});

export default router;
