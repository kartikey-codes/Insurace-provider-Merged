import * as Service from "@/clients/services/cases";

export default {
	namespaced: true,
	state: {
		defaultFilters: {
			status: null,
			case_type_id: null,
			client_employee_id: null,
			denial_type_id: null,
			denial_reason_id: null,
			facility_id: null,
			appeal_level_id: null,
			case_outcome_id: null,
			insurance_provider_id: null,
			insurance_type_id: null,
			insurance_plan: null,
			admit_date: null,
			discharge_date: null,
			unable_to_complete: null,
			empty: null,
		},
		// Keys should match what is in src/Model/Entity/Case
		statuses: [
			{
				name: "Open",
				value: "Open",
			},
			{
				name: "Closed",
				value: "Closed",
			},
		],
		sortAliases: {
			"appeals.appeal_levels": "AppealLevels.name",
			assigned_to_user: "AssignedToUser.first_name",
			"assigned_to_user.full_name": "AssignedToUser.first_name",
			case_outcome: "CaseOutcomes.name",
			"case_outcome.name": "CaseOutcomes.name",
			case_type: "CaseTypes.name",
			"case_type.name": "CaseTypes.name",
			client: "Clients.name",
			"client.name": "Clients.name",
			client_employee: "ClientEmployees.last_name",
			"client_employee.list_name": "ClientEmployees.last_name",
			closed_by_user: "ClosedByUser.first_name",
			"closed_by_user.full_name": "ClosedByUser.first_name",
			created_by_user: "CreatedByUser.first_name",
			"created_by_user.full_name": "CreatedByUser.first_name",
			denial_type: "DenialTypes.name",
			"denial_type.name": "DenialTypes.name",
			facility: "Facilities.name",
			"facility.name": "Facilities.name",
			insurance_provider: "InsuranceProviders.name",
			"insurance_provider.name": "InsuranceProviders.name",
			insurance_type: "InsuranceTypes.name",
			"insurance_type.name": "InsuranceTypes.name",
			modified_by_user: "ModifiedByUser.first_name",
			"modified_by_user.full_name": "ModifiedByUser.first_name",
			patient: "Patients.last_name",
			"patient.list_name": "Patients.last_name",
			"patient.date_of_birth": "Patients.date_of_birth",
		},
	},
	getters: {
		defaultFilters: (state) => state.defaultFilters,
		statuses: (state) => state.statuses,
		sortAliases: (state) => state.sortAliases,
	},
	mutations: {
		setStatuses: (state, payload) => (state.statuses = payload),
	},
	actions: {
		async get({ commit, dispatch }, params) {
			return await Service.get(params);
		},
		async index({ commit, dispatch }, params) {
			return await Service.getIndex(params);
		},
		async save({ commit, dispatch }, entity) {
			if (entity.id) {
				return await dispatch("update", entity);
			} else {
				return await dispatch("create", entity);
			}
		},
		async create({ commit, dispatch }, params) {
			return await Service.create(params);
		},
		async update({ commit, dispatch }, params) {
			return await Service.update(params.id, params);
		},
		async delete({ commit, dispatch }, params) {
			return await Service.destroy(params.id, params);
		},
		async activity({ commit, dispatch }, params) {
			return await Service.activity(params.id, params);
		},
		async assign({ commit, dispatch }, params) {
			return await Service.assign(params.id, params);
		},
		async files({ commit, dispatch }, params) {
			return await Service.files(params.id, params);
		},
		async open({ commit, dispatch }, params) {
			return await Service.open(params.id, params);
		},
		async close({ commit, dispatch }, params) {
			return await Service.close(params.id, {
				id: params.id,
				case_outcome_id: params.case_outcome_id,
				settled_amount: params.settled_amount,
			});
		},
	},
};
