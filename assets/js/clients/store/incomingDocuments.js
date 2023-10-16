import * as Service from "@/clients/services/incomingDocuments";
import { downloadResponseAsFile } from "@/shared/helpers/downloadHelper";
import { createUploadFormData, createUploadOptions } from "@/shared/helpers/uploadHelper";

export default {
	namespaced: true,
	state: {
		loadingCount: false,
		count: -1, // Init as -1 to keep number type but indicate not loaded
		uploading: false,
		uploadPercent: 0,
	},
	getters: {
		loadingCount: (state) => state.loadingCount,
		count: (state) => state.count,
		uploading: (state) => state.uploading,
		uploadPercent: (state) => state.uploadPercent,
	},
	mutations: {
		setLoadingCount: (state, payload) => (state.loadingCount = payload),
		setCount: (state, payload) => (state.count = payload),
		setUploading: (state, payload) => (state.uploading = payload),
		setUploadPercent: (state, payload) => (state.uploadPercent = payload),
	},
	actions: {
		async count({ commit, dispatch }, params) {
			try {
				commit("setLoadingCount", true);
				const response = await Service.count(params);
				commit("setCount", response.new);
				return response.new;
			} finally {
				commit("setLoadingCount", false);
			}
		},
		async index({ commit, dispatch }, params) {
			return await Service.getIndex(params);
		},
		async get({ commit, dispatch }, params) {
			return await Service.get(params.id);
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
		async download({ commit, dispatch }, params) {
			const response = await Service.download(params.id, {
				name: params.name,
			});

			downloadResponseAsFile({
				contents: response,
				name: params.name,
			});
		},
		previewUrl({ commit, dispatch }, params) {
			return Service.previewUrl(params.id);
		},
		async upload({ commit, dispatch }, params) {
			try {
				commit("setUploading", true);
				commit("setUploadPercent", 0);

				const data = createUploadFormData(params.files);
				const options = createUploadOptions((percentage) => commit("setUploadPercent", percentage));
				const response = await Service.upload(data, options);

				return response;
			} finally {
				commit("setUploading", false);
				commit("setUploadPercent", 0);
			}
		},
		async rename({ commit, dispatch }, params) {
			return await Service.rename(params.id, params.filename, params.newName);
		},
		async attachCase({ commit, dispatch }, params) {
			return await Service.attachCase(params.id, params.case_id);
		},
		async detachCase({ commit, dispatch }, params) {
			return await Service.attachCase(params.id);
		},
		async attachAppeal({ commit, dispatch }, params) {
			return await Service.attachAppeal(params.id, params.appeal_id);
		},
		async detachAppeal({ commit, dispatch }, params) {
			return await Service.detachAppeal(params.id);
		},
		async assign({ commit, dispatch }, params) {
			return await Service.assign(params.id, params.user_id);
		},
		async setUnableToComplete({ commit, dispatch }, params) {
			return await Service.setUnableToComplete(params.id);
		},
		async unsetUnableToComplete({ commit, dispatch }, params) {
			return await Service.unsetUnableToComplete(params.id);
		},
		async bulkAssign({ commit, dispatch }, params) {
			return await Service.bulkAssign(params.user_id, params.document_ids);
		},
	},
};
