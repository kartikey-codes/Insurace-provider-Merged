import * as Service from "@/clients/services/appeals";
import { getAppealQueue } from "@/clients/services/queue";

export default {
	namespaced: true,
	state: {
		defaultFilters: {
			status: null,
			denial_type_id: null,
			facility_id: null,
			visit_number: null,
			insurance_provider_id: null,
			insurance_type_id: null,
			insurance_plan: null,
			insurance_number: null,
			appeal_level_id: null,
			appeal_type_id: null,
			case_outcome_id: null,
			assigned_to: null,
			admit_date: null,
			discharge_date: null,
			defendable: null,
			unable_to_complete: null,
		},
		// Keys should match what is in src/Model/Entity/Appeal
		statuses: [
			{
				name: "Open",
				value: "Open",
			},
			{
				name: "UTC",
				value: "Unable To Complete",
			},
			{
				name: "Submitted to RevKeep",
				value: "Submitted",
			},
			{
				name: "Assigned to RevKeep Pro",
				value: "Assigned",
			},
			{
				name: "Completed",
				value: "Completed",
			},
			{
				name: "Returned",
				value: "Returned",
			},
			{
				name: "Cancelled",
				value: "Cancelled",
			},
			{
				name: "Closed",
				value: "Closed",
			},
		],
		loadingUnassigned: false,
		unassigned: [],
		sortAliases: {
			"case.case_type": "CaseTypes.name",
			"case.case_type.name": "CaseTypes.name",
			appeal_level: "AppealLevels.name",
			"appeal_level.name": "AppealLevels.name",
			appeal_type: "AppealTypes.name",
			"appeal_type.name": "AppealTypes.name",
			client: "Clients.name",
			"client.name": "Clients.name",
			"case.patient": "Patients.last_name",
			"case.patient.list_name": "Patients.last_name",
			"case.patient.date_of_birth": "Patients.date_of_birth",
			"case.case_type.name": "CaseTypes.name",
			"case.facility": "Facilities.name",
			"case.facility.name": "Facilities.name",
			"case.denial_type": "DenialTypes.name",
			"case.denial_type.name": "DenialTypes.name",
			"case.case_outcome": "CaseOutcomes.name",
			"case.case_outcome.name": "CaseOutcomes.name",
			"case.visit_number": "Cases.visit_number",
			"case.insurance_provider": "InsuranceProviders.name",
			"case.insurance_provider.name": "InsuranceProviders.name",
			"case.insurance_type": "InsuranceTypes.name",
			"case.insurance_type.name": "InsuranceTypes.name",
			completed_by_user: "CompletedByUser.first_name",
			"completed_by_user.full_name": "CompletedByUser.first_name",
			assigned_to_user: "AssignedToUser.first_name",
			"assigned_to_user.full_name": "AssignedToUser.first_name",
			created_by_user: "CreatedByUser.first_name",
			"created_by_user.full_name": "CreatedByUser.first_name",
			modified_by_user: "ModifiedByUser.first_name",
			"modified_by_user.full_name": "ModifiedByUser.first_name",
		},
		defendableOptions: [
			{
				value: true,
				name: "Defendable",
			},
			{
				value: false,
				name: "Not Defendable",
			},
		],
		loadingOpenByAssignedUser: false,
		openByAssignedUser: [],
	},
	getters: {
		defaultFilters: (state) => state.defaultFilters,
		statuses: (state) => state.statuses,
		loadingUnassigned: (state) => state.loadingUnassigned,
		unassigned: (state) => state.unassigned,
		sortAliases: (state) => state.sortAliases,
		defendableOptions: (state) => state.defendableOptions,
		loadingOpenByAssignedUser: (state) => state.loadingOpenByAssignedUser,
		openByAssignedUser: (state) => state.openByAssignedUser,
	},
	mutations: {
		setLoadingUnassigned: (state, payload) => (state.loadingUnassigned = payload),
		setUnassigned: (state, payload) => (state.unassigned = payload),
		setDefendableOptions: (state, payload) => (state.defendableOptions = payload),
		setLoadingOpenByAssignedUser: (state, payload) => (state.loadingOpenByAssignedUser = payload),
		setOpenByAssignedUser: (state, payload) => (state.openByAssignedUser = payload),
	},
	actions: {
		async get({ commit, dispatch }, params) {
			return await Service.get(params.id);
		},
		async index({ commit, dispatch }, params) {
			return await Service.getIndex(params);
		},
		async queue({ commit, dispatch }, params) {
			return await getAppealQueue(params);
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
		async getUnassigned({ commit, dispatch }, params) {
			commit("setLoadingUnassigned", true);
			const response = await Service.getUnassigned(params);
			commit("setUnassigned", response.data);
			commit("setLoadingUnassigned", false);
			return response.data;
		},
		async assign({ commit, dispatch }, params) {
			return await Service.assign(params.id, params);
		},
		async assignAll({ commit, dispatch }, params) {
			return await Service.assignAll(params.id, params);
		},
		async submit({ commit, dispatch }, params) {
			return await Service.submit(params.id, params);
		},
		async reopen({ commit, dispatch }, params) {
			return await Service.reopen(params.id, params);
		},
		async complete({ commit, dispatch }, params) {
			return await Service.complete(params.id, params);
		},
		async cancel({ commit, dispatch }, params) {
			return await Service.cancel(params.id, params);
		},
		async close({ commit, dispatch }, params) {
			return await Service.close(params.id, params);
		},
		async setDefendable({ commit, dispatch }, params) {
			return await Service.setDefendable(params.id, {
				defendable: params.defendable,
				not_defendable_reasons: {
					_ids: params.defendable ? [] : params.reason_ids,
				},
			});
		},
		async setUnableToComplete({ commit, dispatch }, params) {
			return await Service.setUnableToComplete(params.id, {
				unable_to_complete: params.unable_to_complete,
				utc_reasons: {
					_ids: params.unable_to_complete ? params.reason_ids : [],
				},
			});
		},
		async openByFacility({ commit, dispatch }, params) {
			return await Service.getOpenByFacility(params);
		},
		async openByAssignedUser({ commit, dispatch }, params) {
			commit("setLoadingOpenByAssignedUser", true);
			const response = await Service.getOpenByAssignedUser(params);
			commit("setOpenByAssignedUser", response);
			commit("setLoadingOpenByAssignedUser", false);
			return response;
		},
		async getCoverPage({ commit, dispatch }, params) {
			return await Service.getCoverPage(params.id, params);
		},
		async generateCoverPage({ commit, dispatch }, params) {
			return await Service.generateCoverPage(params.id, params);
		},
	},
};
