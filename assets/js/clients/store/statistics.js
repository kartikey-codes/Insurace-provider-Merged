import api from "@/api";
const url = "/statistics";

export default {
	namespaced: true,
	state: {
		loading: false,
		unassigned_appeals: 0,
		appeals_by_case_type: [],
		closed_cases: 0,
		empty_cases: 0,
		closed_cases_by_outcome: [],
		open_claims_total: 0,
		reimbursed_total: 0,
	},
	getters: {
		loading: (state) => state.loading,

		unassignedAppeals: (state) => state.unassigned_appeals,
		appealsByCaseType: (state) => state.appeals_by_case_type,
		closedCases: (state) => state.closed_cases,
		emptyCases: (state) => state.empty_cases,
		closedCasesByOutcome: (state) => state.closed_cases_by_outcome,
		openClaimsTotal: (state) => state.open_claims_total,
		reimbursedTotal: (state) => state.reimbursed_total,

		// Virtual
		favorableCasesPercent: (state) => {
			const favorableResult = state.closed_cases_by_outcome.find((outcome) => outcome.name == "Favorable");
			const favorableCount = favorableResult?.count ?? 0;

			if (favorableCount <= 0) {
				return 0;
			}

			const percentage = (favorableCount / state.closed_cases) * 100;

			return Math.round(percentage);
		},
	},
	mutations: {
		loading: (state, payload) => (state.loading = payload),

		appealsByCaseType: (state, payload) => (state.appeals_by_case_type = payload),
		unassignedAppeals: (state, payload) => (state.unassigned_appeals = payload),
		appealsByCaseType: (state, payload) => (state.appeals_by_case_type = payload),
		closedCases: (state, payload) => (state.closed_cases = payload),
		emptyCases: (state, payload) => (state.unassigned_appeals = payload),
		closedCasesByOutcome: (state, payload) => (state.closed_cases_by_outcome = payload),
		openClaimsTotal: (state, payload) => (state.open_claims_total = payload),
		reimbursedTotal: (state, payload) => (state.reimbursed_total = payload),
	},
	actions: {
		async refresh({ commit, dispatch }, params) {
			try {
				commit("loading", true);
				const response = await api.get(url);

				commit("appealsByCaseType", response.data.appeals_by_case_type);
				commit("unassignedAppeals", response.data.unassigned_appeals);
				commit("closedCases", response.data.closed_cases);
				commit("emptyCases", response.data.empty_cases);
				commit("closedCasesByOutcome", response.data.closed_cases_by_outcome);
				commit("openClaimsTotal", response.data.open_claims_total);
				commit("reimbursedTotal", response.data.reimbursed_total);

				return response.data;
			} finally {
				commit("loading", false);
			}
		},
	},
};
