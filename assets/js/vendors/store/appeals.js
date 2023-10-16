import api from "@/api";
const url = "/appeals";

export default {
  namespaced: true,
  state: {
	// Keys should match what is in src/Model/Entity/Appeal
	statuses: {
		'Open': 'Open',
		'Submitted': 'Submitted',
		'Assigned': 'Assigned',
		'Completed': 'Completed',
		'Returned': 'Returned',
		'Cancelled': 'Cancelled',
		'Closed': 'Closed'
	},
	loadingUnassigned: false,
	unassigned: []
  },
  getters: {
	statuses: state => state.statuses,
	loadingUnassigned: state => state.loadingUnassigned,
	unassigned: state => state.unassigned
  },
  mutations: {
	setLoadingUnassigned: (state, payload) => state.loadingUnassigned = payload,
	setUnassigned: (state, payload) => state.unassigned = payload
  },
  actions: {
	async index({ commit, dispatch }, params) {
	  const response = await api.get(url, {
		params: params
	  });
	  return response.data; // { data: [], pagination: [] }
	},
	async update({ commit, dispatch }, params) {
	  const response = await api.patch(`${url}/${params.id}`, params);
	  return response.data.data;
	},
	async addNote({ commit, dispatch }, params) {
	  const response = await api.post(`${url}/${params.id}/notes`, {
		notes: params.notes
	  });
	  return response.data.data;
	},
	async deleteNote({ commit, dispatch }, params) {
	  const response = await api.delete(`${url}/${params.appeal_id}/notes/${params.id}`);
	  return response.data; // { success: bool, data: {} }
	},
	async return({ commit, dispatch }, params) {
	  const response = await api.post(`${url}/${params.id}/return`);
	  return response.data.data;
	},
	async complete({ commit, dispatch }, params) {
	  const response = await api.post(`${url}/${params.id}/complete`);
	  return response.data.data;
	},
	async setDefendable({ commit, dispatch }, params) {
	  const response = await api.patch(`${url}/${params.id}/defendable`, {
		defendable: params.defendable,
		not_defendable_reasons: {
		  _ids: params.defendable ? [] : params.reason_ids
		}
	  });
	  return response.data.data;
	}
  }
}
