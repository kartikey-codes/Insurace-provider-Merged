<template>
	<upload-drag-and-drop @dropped="uploadFiles">
		<b-row class="mb-4">
			<b-col cols="9" class="text-left">
				<upload-button @upload="uploadFiles" :disabled="uploading" variant="primary" accept=".pdf">
					<span v-if="uploading">
						<font-awesome-icon icon="circle-notch" spin fixed-width />
						<span>Uploading...</span>
					</span>
					<span v-else>
						<font-awesome-icon icon="file-upload" fixed-width />
						<span>Upload Documents</span>
					</span>
				</upload-button>

				<b-btn @click="getZip">
					<font-awesome-icon icon="file-zipper" fixed-width />
					<span>Zip</span>
				</b-btn>

				<b-btn @click="merge">
					<font-awesome-icon icon="file-pdf" fixed-width />
					<span>Merge</span>
				</b-btn>
			</b-col>
			<b-col cols="3" class="text-right">
				<b-button variant="light" @click="refresh" :disabled="loading" title="Refresh">
					<font-awesome-icon icon="sync" :spin="loading" fixed-width />
				</b-button>
			</b-col>
		</b-row>

		<b-row v-if="uploading" class="my-4">
			<b-col>
				<upload-progress :value="uploadPercent" />
			</b-col>
		</b-row>

		<loading-indicator v-if="loading && isEmpty" title="Loading files..." class="my-4" />
		<b-list-group v-else-if="!isEmpty">
			<transition-group mode="out-in" name="fade" :duration="{ enter: 300, leave: 300 }">
				<b-list-group-item
					v-for="file in files"
					:key="file.path"
					class="p-2 cursor-pointer"
					:active="active == file.path"
				>
					<div class="d-flex justify-content-between align-items-center">
						<div @click="view(file)" class="cursor-pointer">
							<h5 class="mb-0">{{ formatFileName(file.path) }}</h5>
							<div class="mb-0 mb-md-0 text-muted">
								<small v-if="file.last_modified">{{ $filters.fromNow(file.last_modified) }}</small>
								<span v-if="file.last_modified && file.file_size">&bull;</span>
								<small v-if="file.file_size">{{ $filters.fileSize(file.file_size) }}</small>
							</div>
						</div>
						<b-dropdown right split @click.stop.prevent="view(file)" :disabled="file.busy">
							<template #button-content>
								<span v-if="active && active == file.path">
									<font-awesome-icon icon="times" fixed-width />
									<span>Close</span>
								</span>
								<span v-else>
									<span v-if="fileCanPreview(file)">
										<font-awesome-icon icon="eye" fixed-width />
										<span>Preview</span>
									</span>
									<span v-else>
										<font-awesome-icon icon="file-download" fixed-width />
										<span>Download</span>
									</span>
								</span>
							</template>
							<b-dropdown-item @click="download(file)">
								<font-awesome-icon icon="file-download" fixed-width />
								<span>Download</span>
							</b-dropdown-item>
							<b-dropdown-item @click="renameFile(file)">
								<font-awesome-icon icon="font" fixed-width />
								<span>Rename</span>
							</b-dropdown-item>

							<b-dropdown-divider />

							<b-dropdown-item @click="deleteFile(file)">
								<font-awesome-icon icon="trash" fixed-width />
								<span>Delete</span>
							</b-dropdown-item>
						</b-dropdown>
					</div>
				</b-list-group-item>
			</transition-group>
		</b-list-group>

		<empty-result v-else icon="cloud-upload-alt">
			No files have been uploaded
			<template #content>
				<p>You can drag and drop files onto this area to start uploading.</p>
				<b-button variant="light" @click="refresh()" class="mt-2">Refresh</b-button>
			</template>
		</empty-result>
	</upload-drag-and-drop>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import UploadButton from "./UploadButton.vue";
import UploadDragAndDrop from "./UploadDragAndDrop.vue";
import UploadProgress from "./UploadProgress.vue";

export default {
	name: "FileList",
	components: {
		UploadButton,
		UploadDragAndDrop,
		UploadProgress,
	},
	props: {
		active: {
			type: [Boolean, String],
			default: false,
		},
		loading: {
			type: Boolean,
			default: false,
		},
		files: {
			type: Array,
			default: () => [],
			required: true,
		},
		prefix: {
			type: [Number, String],
			default: "",
		},
		confirmDeleteText: {
			type: String,
			default: "Are you sure you want to delete this file?",
		},
		uploading: {
			type: Boolean,
			default: false,
		},
		uploadPercent: {
			type: Number,
			default: 0,
		},
		previewableExtensions: {
			type: Array,
			default: () => ["txt", "pdf", "jpg", "jpeg", "gif"],
		},
	},
	computed: {
		isEmpty() {
			return this.files.length <= 0;
		},
	},
	data() {
		return {};
	},
	methods: {
		clickedFile(file) {
			console.log("clicked " + file.path);
		},
		refresh() {
			this.$emit("refresh");
		},
		view(file) {
			if (this.active && this.active == file.path) {
				return this.$emit("close", file);
			}

			if (this.fileCanPreview(file)) {
				return this.preview(file);
			} else {
				return this.download(file);
			}
		},
		async download(file) {
			this.$emit("download", file);
		},
		preview(file) {
			this.$emit("preview", file);
		},
		async uploadFiles(files) {
			this.$emit("upload", files);
		},
		async deleteFile(file) {
			if (!confirm(this.confirmDeleteText)) {
				return false;
			}

			this.$emit("delete", file);
		},
		async renameFile(file) {
			var fileName = this.formatFileName(file.path);
			var newName = prompt('Rename "' + fileName + '" to: ', fileName);
			var newFullName = newName;

			if (newName == null || newName == "") {
				return false;
			}

			this.$emit("rename", fileName, newFullName);
		},
		formatFileName(path) {
			return path.split(/(?:\/|\\)+/).pop();
		},
		fileCanPreview(file) {
			var filename = this.formatFileName(file.path);
			var extension = filename.split(".").pop();

			if (!extension) {
				return false;
			}

			if (this.previewableExtensions.indexOf(extension) != -1) {
				return true;
			}

			return false;
		},
		async merge() {
			this.$emit("merge");
		},
		async getZip() {
			this.$emit("zip");
		},
	},
};
</script>
