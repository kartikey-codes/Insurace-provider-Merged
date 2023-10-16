import api from "@/api";
const url = "/dashboard";

export default {
	namespaced: true,
	state: {
		loading: false,
		assigned_appeals: 0,
		assigned_cases: 0,
		assigned_documents: 0,
		completed_appeals: [],
		returned_appeals: [],
		show: {
			calendar: false,
			notes: false,
			myAppeals: true,
			myRequests: true,
		},
	},
	getters: {
		loading: (state) => state.loading,
		assignedCases: (state) => state.assigned_cases,
		assignedDocuments: (state) => state.assigned_documents,
		assignedAppeals: (state) => state.assigned_appeals,
		completedAppeals: (state) => state.completed_appeals,
		returnedAppeals: (state) => state.returned_appeals,
		showCalendar: (state) => state.show.calendar,
		showNotes: (state) => state.show.notes,
		showMyAppeals: (state) => state.show.myAppeals,
		showMyRequests: (state) => state.show.myRequests,
	},
	mutations: {
		loading: (state, payload) => (state.loading = payload),
		assignedAppeals: (state, payload) => (state.assigned_appeals = payload),
		assignedCases: (state, payload) => (state.assigned_cases = payload),
		assignedDocuments: (state, payload) => (state.assigned_documents = payload),
		completedAppeals: (state, payload) => (state.completed_appeals = payload),
		returnedAppeals: (state, payload) => (state.returned_appeals = payload),
		showCalendar: (state, payload) => (state.show.calendar = payload),
		showNotes: (state, payload) => (state.show.notes = payload),
		showMyAppeals: (state, payload) => (state.show.myAppeals = payload),
		showMyRequests: (state, payload) => (state.show.myRequests = payload),
	},
	actions: {
		async refresh({ commit, dispatch }, params) {
			try {
				commit("loading", true);
				const response = await api.get(url);

				commit("assignedAppeals", response.data.assigned_appeals);
				commit("assignedCases", response.data.assigned_cases);
				commit("assignedDocuments", response.data.assigned_documents);
				commit("completedAppeals", response.data.completed_appeals);
				commit("returnedAppeals", response.data.returned_appeals);

				return response.data;
			} finally {
				commit("loading", false);
			}
		},
		async recentNotes({ commit, dispatch }, params) {
			const response = await api.get(`${url}/recentNotes`, { params });
			return response.data;
		},
	},
};
