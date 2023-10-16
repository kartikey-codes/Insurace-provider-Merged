<template>
	<div
		@dragstart="startDrag"
		@drop.prevent="dropped"
		@dragover.prevent="dragging = true"
		@dragenter.prevent="dragging = true"
		@dragleave.prevent="dragging = false"
		@dragend.prevent="dragging = false"
	>
		<form ref="uploadForm" role="form" enctype="multipart/form-data" class="form-inline d-inline">
			<input
				ref="uploadInput"
				@change="uploaded"
				type="file"
				multiple="true"
				style="display: none"
				:accept="accept"
			/>
		</form>
		<slot
			v-bind="{
				accept,
				browse,
				canMerge,
				deleted,
				dragging,
				empty,
				folders,
				files,
				loading,
				merge,
				merging,
				prefix,
				refresh,
				renamed,
				results,
				selected,
				service,
				uploading,
				uploadPercent,
				zip,
				zipping,
			}"
		></slot>
	</div>
</template>

<script type="text/javascript">
export default {
	name: "FileManager",
	props: {
		service: {
			type: null,
			default: () => {
				return {
					list: async () => {},
					upload: async () => {},
					merge: async () => {},
					zip: async () => {},
				};
			},
			required: true,
		},
		prefix: {
			type: [Number, String],
			default: "",
		},
		folderPrefix: {
			type: [Number, String],
			default: "",
		},
		accept: {
			type: [String, Array],
			default: "",
		},
		selected: {
			type: Array,
			default: () => [
				// Selected filenames
			],
		},
	},
	mounted() {
		this.refresh();
	},
	computed: {
		canMerge() {
			const mergeableExtensions = ["pdf"];

			return this.selected.every((selected) => {
				const extension = selected.split(".").pop();
				return mergeableExtensions.includes(extension);
			});
		},
		empty() {
			return this.results.length <= 0;
		},
		files() {
			return this.results.filter((result) => {
				return result.type == "file";
			});
		},
		folders() {
			return this.results.filter((result) => {
				return result.type == "dir";
			});
		},
	},
	data() {
		return {
			dragging: false,
			loading: false,
			merging: false,
			results: [],
			uploadPercent: 0,
			uploading: false,
			zipping: false,
		};
	},
	methods: {
		browse(event) {
			if (event) {
				event.preventDefault();
			}

			this.$refs.uploadInput.click();
		},
		uploaded(event) {
			if (event.target != undefined && event.target.files.length > 0) {
				this.upload({
					files: event.target.files,
				});
			}

			this.$refs.uploadForm.reset();
		},
		startDrag(event) {
			event.dataTransfer.dropEffect = "move";
			event.dataTransfer.effectAllowed = "move";
		},
		dropped(event) {
			event.preventDefault();
			event.stopPropagation();

			this.dragging = false;

			if (event.dataTransfer == undefined) {
				return false;
			}

			if (event.dataTransfer.files == undefined || event.dataTransfer.files.length == undefined) {
				return false;
			}

			if (event.dataTransfer.files.length == 0) {
				return false;
			}

			this.$emit("dropped", event.dataTransfer.files);

			this.upload({
				files: event.dataTransfer.files,
			});
		},
		async refresh() {
			try {
				this.loading = true;
				const response = await this.service.list(this.prefix);
				this.results = response;
				this.$emit("loaded", response);
			} finally {
				this.loading = false;
			}
		},
		async upload(params) {
			try {
				this.uploading = true;
				this.uploadPercent = 0;

				const response = await this.service.upload(
					{
						path: this.prefix,
						...params,
					},
					(percentage) => {
						this.uploadPercent = percentage;
					}
				);

				await this.refresh();
				this.$emit("uploaded", response);

				return response;
			} catch (e) {
				this.$emit("error", {
					action: "upload",
					message: e.response.data.message || e.message,
				});
			} finally {
				this.uploading = false;
				this.uploadPercent = 0;
			}
		},
		deleted(result) {
			if (result.filename) {
				//@todo Splice deleted file from results
				console.log("Splice", this.results, result);
				//this.results = this.results.filter((file) => file.path !== result.filename);

				// Unselect file if it was selected
				const selectedIndex = this.selected.indexOf(result.filename);
				if (selectedIndex !== -1) {
					this.$emit("update:selected", this.selected.splice(selectedIndex, 1));
				}

				this.refresh();
			} else {
				console.warn("Invalid file manager delete event", result);
			}
		},
		renamed(result) {
			if (result.newName) {
				//@todo Find file in results and update name
				//this.results = this.results.filter((file) => file.path !== result.filename);

				// Unselect file if it was selected
				const selectedIndex = this.selected.indexOf(result.oldName);
				if (selectedIndex !== -1) {
					this.$emit("update:selected", this.selected.splice(selectedIndex, 1));
				}

				this.refresh();
			} else {
				console.warn("Invalid file manager rename event", result);
			}
		},
		async merge(files, name) {
			if (!name) {
				name = prompt(
					"Enter a name for the merged PDF document. The .pdf file extension will be added for you.",
					"Combined"
				);
				if (name == null || name == "") return;
			}

			try {
				this.merging = true;

				const response = await this.service.merge(this.prefix, {
					files,
					name,
				});

				this.$emit("merged", response);
				await this.refresh();
				return response;
			} catch (e) {
				this.$emit("error", {
					action: "merge",
					message: e.response?.data?.message || e.message,
				});
			} finally {
				this.merging = false;
			}
		},
		async zip(files, name) {
			if (!name) {
				name = prompt("Enter a name for the Zip archive. The .zip extension will be added for you.", "Archive");
				if (name == null || name == "") return;
			}

			try {
				this.zipping = true;

				const response = await this.service.zip(this.prefix, {
					files,
					name,
				});

				this.$emit("zipped", response);
				await this.refresh();
				return response;
			} catch (e) {
				this.$emit("error", {
					action: "zip",
					message: e.response?.data?.message || e.message,
				});
			} finally {
				this.zipping = false;
			}
		},
	},
	watch: {
		prefix: {
			handler(val) {
				this.refresh();
			},
		},
	},
};
</script>
