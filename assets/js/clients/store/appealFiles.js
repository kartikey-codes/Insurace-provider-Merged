import * as Service from "@/clients/services/appealFiles";

export default {
	namespaced: true,
	state: {
		uploading: false,
		uploadPercent: 0,
	},
	getters: {
		uploading: (state) => state.uploading,
		uploadPercent: (state) => state.uploadPercent,
	},
	mutations: {
		setUploading: (state, payload) => (state.uploading = payload),
		setUploadPercent: (state, payload) => (state.uploadPercent = payload),
	},
	actions: {
		async list({ commit, dispatch }, params) {
			return await Service.list(params.id);
		},
		async download({ commit, dispatch }, params) {
			return await Service.download(params.id, params);
		},
		previewUrl({ commit, dispatch }, params) {
			return Service.previewUrl(params.id, { name: params.name });
		},
		async upload({ commit, dispatch }, params) {
			try {
				commit("setUploading", true);
				commit("setUploadPercent", 0);
				const response = await Service.upload(params, (percentage) => commit("setUploadPercent", percentage));
				return response.data;
			} finally {
				commit("setUploading", false);
				commit("setUploadPercent", 0);
			}
		},
		async delete({ commit, dispatch }, params) {
			return await Service.destroy(params.id, params);
		},
		async rename({ commit, dispatch }, params) {
			return await Service.rename(params.id, params);
		},
		async merge({ commit, dispatch }, params) {
			return await Service.merge(params.id, params);
		},
		async zip({ commit, dispatch }, params) {
			return await Service.zip(params.id, params);
		},
	},
};
