import * as Service from "@/clients/services/clientSettings";

export default {
	namespaced: true,
	state: {
		loading: false,
		data: {
			id: null,
			created: null,
			modified: null,
			name: null,
			email: null,
			active: null,
			phone: null,
			fax: null,
			street_address_1: null,
			street_address_2: null,
			city: null,
			state: null,
			zip: null,
			contact_first_name: null,
			contact_last_name: null,
			contact_department: null,
			contact_title: null,
			contact_phone: null,
			contact_fax: null,
			contact_email: null,
			status: null,
			npi_number: null,
			subscription_active: null,
			tos_date: null,
			primary_taxonomy: null,
			full_address: null,
		},
	},
	getters: {
		loading: (state) => state.loading === null || state.loading,
		data: (state) => state.data,
	},
	mutations: {
		setLoading: (state, payload) => (state.loading = payload),
		setData: (state, payload) => (state.data = payload),
	},
	actions: {
		async get({ commit, dispatch }, params) {
			commit("setLoading", true);
			const response = await Service.get(params);
			commit("setData", response);
			commit("setLoading", false);
			return response;
		},
	},
};
