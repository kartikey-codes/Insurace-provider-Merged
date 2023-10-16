import api from "@/api";
import { downloadResponseAsFile } from "@/shared/helpers/downloadHelper";
const url = "/insurance-providers";

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
			const response = await api.get(`${url}/${params.id}/files`, params);
			return response.data.data;
		},
		async download({ commit, dispatch }, params) {
			const response = await api.get(`${url}/${params.id}/files/download?name=${params.name}`, {
				responseType: "arraybuffer",
			});

			downloadResponseAsFile({
				contents: response.data,
				name: params.name,
			});
		},
		previewUrl({ commit, dispatch }, params) {
			return `${params.baseUrl}${url}/${params.id}/files/preview?name=${params.name}&token=${params.token}`;
		},
	},
};
