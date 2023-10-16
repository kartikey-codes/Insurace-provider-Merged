import CaseView from '@/vendors/views/Cases/view.vue'

const baseUrl = "/cases"

export default [
	{
		path: '/cases/:id',
		name: 'cases.view',
		component: CaseView,
		meta: {
			title: "View Case"
		}
	},
];
