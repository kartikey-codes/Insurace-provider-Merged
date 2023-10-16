import * as Service from "@/clients/services/patients";

export default {
	namespaced: true,
	state: {
		defaultFilters: {
			date_of_birth: null,
			sex: null,
			marital_status: null,
		},
		sexes: [
			{
				value: "Male",
				name: "Male",
			},
			{
				value: "Female",
				name: "Female",
			},
			{
				value: "Other",
				name: "Other",
			},
		],
		maritalStatuses: [
			{
				value: "Single",
				name: "Single",
			},
			{
				value: "Married",
				name: "Married",
			},
			{
				value: "Widow",
				name: "Widow",
			},
			{
				value: "Divorced",
				name: "Divorced",
			},
			{
				value: "Separated",
				name: "Separated",
			},
		],
		sortAliases: {
			"facility.name": "Facilities.name",
			"patient.list_name": "Patients.last_name",
			age: "date_of_birth",
			date_of_birth: "date_of_birth",
			"insurance_provider.name": "InsuranceProviders.name",
			"insurance_type.name": "InsuranceTypes.name",
			"attending_physician.list_name": "AttendingPhysicians.last_name",
			"case_manager.full_name": "CaseManagers.last_name",
			"assigned_to_user.full_name": "AssignedToUser.first_name",
		},
	},
	getters: {
		sexes: (state) => state.sexes,
		maritalStatuses: (state) => state.maritalStatuses,
		defaultFilters: (state) => state.defaultFilters,
		sortAliases: (state) => state.sortAliases,
	},
	mutations: {
		setSexes: (state, payload) => (state.sexes = payload),
		setMaritalStatuses: (state, payload) => (state.maritalStatuses = payload),
	},
	actions: {
		async index({ commit, dispatch }, params) {
			return await Service.getIndex(params);
		},
		async search({ commit, dispatch }, params) {
			return await Service.search(params);
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
			return await Service.destroy(params.id);
		},
		async get({ commit, dispatch }, params) {
			return await Service.get(params.id);
		},
		async getSimilar({ commit, dispatch }, params) {
			return await Service.getSimilar(params.id);
		},
		async merge({ commit, dispatch }, params) {
			return await Service.merge(params.id, params);
		},
	},
};
