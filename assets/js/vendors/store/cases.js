import api from "@/api";
const url = '/cases';

export default {
  namespaced: true,
	state: {
		// Keys should match what is in src/Model/Entity/Case
		statuses: {
			'Open': 'Open',
			'Closed': 'Closed'
		}
	},
	getters: {
		statuses: state => state.statuses
	},
	mutations: {
		setStatuses: (state, payload) => state.statuses = payload
	},
  actions: {
    async get({ commit, dispatch }, params) {
      const response = await api.get(`${url}/${params.id}`, params);
      return response.data.data;
    },
    async index({ commit, dispatch }, params) {
      const response = await api.get(url, {
        params: params
      });
      return response.data; // { data: [], pagination: [] }
    },
    async search({ commit, dispatch }, params) {
      const response = await api.get(url, {
        params: params
      });
      return response.data.data;
    },
    /*
    async save({ commit, dispatch }, entity) {
      if (entity.id) {
        return await dispatch('update', entity);
      } else {
        return await dispatch('create', entity);
      }
    },
    async create({ commit, dispatch }, params) {
      const response = await api.post(`${url}`, params);
      return response.data.data;
    },
    async update({ commit, dispatch }, params) {
      const response = await api.patch(`${url}/${params.id}`, params);
      return response.data.data;
    },
    async delete({ commit, dispatch }, params) {
      const response = await api.delete(`${url}/${params.id}`);
      return response.data; // { success: boolean, data: {} }
    },
    */
    async activity({ commit, dispatch }, params) {
      const response = await api.get(`${url}/${params.id}/activity`, params);
      return response.data.data; // [users]
    },
    /*
    async assign({ commit, dispatch }, params) {
      const response = await api.get(`${url}/${params.id}/assign`, params);
      return response.data.data;
    },
    async files({ commit, dispatch }, params) {
      const response = await api.get(`${url}/${params.id}/files`, params);
      return response.data.files; // [files]
    },
    async open({ commit, dispatch }, params) {
      const response = await api.post(`${url}/${params.id}/open`, {
        id: params.id
      });
      return response.data.data;
    },
    async openAll({ commit, dispatch }, params) {
      const response = await api.post(`${url}/openAll`, {
        _ids: params.ids
      });
      return response.data.data;
    },
    async close({ commit, dispatch }, params) {
      const response = await api.post(`${url}/${params.id}/close`, {
        id: params.id,
        case_outcome_id: params.case_outcome_id,
        settled_amount: params.settled_amount
      });
      return response.data.data;
    },
    async closeAll({ commit, dispatch }, params) {
      const response = await api.post(`${url}/closeAll`, {
        _ids: params.ids
      });
      return response.data.data;
    },
    */
    async completeAppeals({ commit, dispatch }, params) {
        const response = await api.get(`${url}/${params.id}/completeAppeals`, params);
        return response.data.data;
    },
  }
}
