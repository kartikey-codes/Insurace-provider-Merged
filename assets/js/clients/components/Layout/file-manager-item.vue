<script type="text/javascript">
import fileSize from "@/shared/filters/fileSize";
import moment from "moment";

export default {
	name: "FileManagerItem",
	props: {
		prefix: {
			type: [String, Number],
			default: "",
		},
		service: {
			type: null,
			default: () => {
				return {
					download: async () => {},
					previewUrl: () => {},
					destroy: async () => {},
					rename: async () => {},
				};
			},
			required: true,
		},
		value: {
			type: Object,
			default: () => {
				return {
					type: "",
					path: "",
					file_size: 0,
					visibility: "",
					last_modified: 0,
					mime_type: null,
					extra_metadata: [],
				};
			},
			required: true,
		},
	},
	computed: {
		isDirectory() {
			return this.value.type == "dir";
		},
		canDelete() {
			return true;
		},
		canDownload() {
			return this.value.type == "file";
		},
		canPreview() {
			if (this.isDirectory) {
				return false;
			}

			const previewableExtensions = ["pdf"];
			return previewableExtensions.includes(this.extension);
		},
		canRename() {
			return true;
		},
		fileName() {
			return this.value.path.split(/(?:\/|\\)+/).pop();
		},
		extension() {
			if (this.isDirectory) {
				return "";
			}

			return this.value.path.split(".").pop();
		},
		lastModified() {
			return moment(this.value.last_modified, "X").toDate();
		},
		lastModifiedTimeAgo() {
			return moment(this.value.last_modified, "X").fromNow();
		},
		mimeType() {
			if (this.isDirectory) {
				return "";
			}

			return this.value.mime_type;
		},
		name() {
			return this.fileName.split(".").shift();
		},
		size() {
			if (this.isDirectory) {
				return "";
			}

			return fileSize(this.value.file_size);
		},
		previewUrl() {
			if (this.isDirectory) {
				return "";
			}

			return this.service.previewUrl(this.prefix, {
				name: this.fileName,
			});
		},
	},
	data() {
		return {
			deleting: false,
			downloading: false,
			renaming: false,
		};
	},
	methods: {
		async download() {
			try {
				this.downloading = true;

				return await this.service.download(this.prefix, {
					name: this.fileName,
				});
			} finally {
				this.downloading = false;
			}
		},
		async destroy() {
			if (!confirm("Are you sure?")) {
				return false;
			}

			try {
				this.deleting = true;

				const response = await this.service.destroy(this.prefix, {
					name: this.fileName,
				});

				this.$emit("deleted", response);

				return response;
			} catch (e) {
				this.$emit("error", {
					action: "delete",
					message: e.response?.data?.message || e.message,
				});
			} finally {
				this.deleting = false;
			}
		},
		async rename() {
			let promptedName = prompt("Rename file", this.name);
			if (promptedName == null || promptedName == "") return;

			try {
				this.renaming = true;

				const newName = this.extension ? `${promptedName}.${this.extension}` : promptedName;

				const response = await this.service.rename(this.prefix, {
					filename: this.fileName,
					newName: newName,
				});

				this.$emit("renamed", {
					oldName: this.fileName,
					newName: response.filename,
				});

				return response;
			} catch (e) {
				this.$emit("error", {
					action: "rename",
					message: e.response?.data?.message || e.message,
				});
			} finally {
				this.renaming = false;
			}
		},
	},
	render() {
		return this.$scopedSlots.default({
			value: this.value,
			canDelete: this.canDelete,
			canDownload: this.canDownload,
			canPreview: this.canPreview,
			canRename: this.canRename,
			deleting: this.deleting,
			destroy: this.destroy,
			download: this.download,
			downloading: this.downloading,
			extension: this.extension,
			fileName: this.fileName,
			isDirectory: this.isDirectory,
			lastModified: this.lastModified,
			lastModifiedTimeAgo: this.lastModifiedTimeAgo,
			mimeType: this.mimeType,
			name: this.name,
			prefix: this.prefix,
			previewUrl: this.previewUrl,
			renaming: this.renaming,
			rename: this.rename,
			size: this.size,
		});
	},
};
</script>
