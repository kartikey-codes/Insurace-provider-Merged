import api from "@/api";
import { downloadResponseAsFile } from "@/shared/helpers/downloadHelper";
const url = "/cases";

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
		async upload({ commit, dispatch }, params) {
			let formData = new FormData();
			commit("setUploading", true);
			commit("setUploadPercent", 0);

			for (var i = 0, len = params.files.length; i < len; ++i) {
				formData.append("files[]", params.files[i]);
			}

			const response = await api.post(`${url}/${params.id}/files`, formData, {
				headers: {
					"Content-Type": "multipart/form-data",
				},
				onUploadProgress: function (e) {
					var percentage = parseInt((e.loaded / e.total) * 100);
					commit("setUploadPercent", percentage);
				}.bind(this),
			});

			commit("setUploading", false);
			commit("setUploadPercent", 0);

			return response.data;
		},
		async delete({ commit, dispatch }, params) {
			const response = await api.delete(`${url}/${params.id}/files?name=${params.name}`);

			return response.data;
		},
		async rename({ commit, dispatch }, params) {
			const response = await api.post(`${url}/${params.id}/files/rename`, {
				filename: params.filename,
				newname: params.newName,
			});

			return response.data;
		},
	},
};
