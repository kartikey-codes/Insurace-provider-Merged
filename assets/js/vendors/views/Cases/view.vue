<template>
	<div>
		<page-header>
			<template #title> View Case #{{ $route.params.id }} </template>
			<template #buttons>
				<b-dropdown variant="light" right title="More Options">
					<template #button-content>
						<font-awesome-icon icon="cog" fixed-width />
					</template>

					<b-dropdown-item @click="refresh" :disabled="loading">
						<font-awesome-icon icon="sync" :spin="loading" fixed-width />
						<span>Refresh</span>
					</b-dropdown-item>

					<b-dropdown-item @click="completeAppeals()" :disabled="loading || caseClosed || completingAll">
						<font-awesome-icon icon="check-circle" fixed-width />
						<span>Complete All Appeals</span>
					</b-dropdown-item>
				</b-dropdown>
			</template>
		</page-header>

		<loading-indicator v-if="showLoading" class="my-5" />
		<b-container fluid class="my-2" v-else>
			<b-row>
				<b-col cols="12" lg="3" class="mb-4">
					<div class="rounded bg-white p-4 shadow-sm mb-2">
						<case-status-label :value="caseEntity" class="mb-2" />

						<h2
							class="h3 my-0 mb-1 font-weight-bold text-uppercase"
							v-text="caseEntity.patient.list_name"
						/>
						<h3 v-if="caseEntity.patient.date_of_birth" class="h6 mt-0 mb-1 text-muted text-uppercase">
							<span class="font-weight-bold">
								{{ $filters.formatDate(caseEntity.patient.date_of_birth) }}
							</span>
							<span v-if="caseEntity.patient.age != null && caseEntity.patient.age != undefined">
								&mdash;
								<span>
									<font-awesome-icon
										v-if="caseEntity.patient.is_birthday"
										icon="birthday-cake"
										class="text-muted-lighter"
									/>
									<span class="font-weight-bold" v-text="caseEntity.patient.age" />
								</span>
							</span>
						</h3>
						<h3 v-else class="h5 mt-0 mb-1 text-warning">(Missing DOB)</h3>

						<div v-if="caseEntity.modified" class="small text-muted mt-2 mb-2">
							<span v-if="caseEntity.modified" :title="$filters.formatTimestamp(caseEntity.modified)"
								>Last updated {{ $filters.fromNow(caseEntity.modified) }}</span
							>
							<span v-if="caseEntity.modified_by_user && caseEntity.modified_by_user.full_name"
								>by {{ caseEntity.modified_by_user.full_name }}</span
							>
						</div>

						<case-activity :case-id="$route.params.id" />
					</div>

					<case-summary :value="caseEntity" />
				</b-col>
				<b-col cols="12" lg="9">
					<b-alert :show="caseClosed" dismissible variant="warning" class="p-4">
						<font-awesome-icon icon="lock" fixed-width />
						<span
							>This case is closed. No more changes can be made to reviews unless the case is
							reopened.</span
						>
					</b-alert>
					<b-tabs
						v-if="hasAppeals"
						v-model="activeAppeal"
						class="shadow-sm mb-4"
						active-nav-item-class="font-weight-bold"
					>
						<!-- Uses Bootstrap-Vue workaround for getting reactivity in tab titles -->
						<b-tab
							v-for="appeal in caseEntity.appeals"
							:key="appeal.id"
							:title-link-class="appeal.modified ? {} : null"
						>
							<template #title>
								<div class="d-flex align-items-center">
									<div>
										<span
											v-if="appeal.appeal_level && appeal.appeal_level.name"
											v-text="appeal.appeal_level.name"
										/>
										<span v-else class="text-danger">(Missing Level)</span>
									</div>
									<div class="ml-4">
										<appeal-status-label :value="appeal" />
									</div>
								</div>
							</template>
							<appeal-view
								:case-entity="caseEntity"
								:appeal="appeal"
								:case-closed="caseClosed"
								@saved="updateAppeal"
								@completed="updateAppeal"
								@returned="updateAppeal"
								@refresh="refresh"
								@deleted="refresh"
							>
								<template #files>
									<b-row>
										<b-col cols="12" :lg="previewingFile ? 6 : 12">
											<file-list
												:loading="loadingFiles"
												:files="caseFiles"
												:prefix="caseEntity.id"
												:uploading="uploading"
												:upload-percent="uploadPercent"
												:active="previewingFile"
												@refresh="getFiles"
												@close="previewingFile = false"
												@upload="uploadFiles"
												@download="downloadFile"
												@preview="previewFile"
												@rename="renameFile"
												@delete="deleteFile"
											/>
										</b-col>
										<b-col v-if="previewingFile" cols="12" lg="6" class="text-right">
											<b-button variant="light" class="mb-2 px-4" @click="previewingFile = false">
												<font-awesome-icon icon="times" fixed-width />
												<span>Close Preview</span>
											</b-button>
											<pdf-frame :url="previewingFileUrl" />
										</b-col>
									</b-row>
								</template>
							</appeal-view>
						</b-tab>
					</b-tabs>
					<empty-result v-else class="my-4">
						No appeals were found
						<template #content>
							<p>
								<span v-if="!caseClosed"
									>This case is opened and should contain at least 1 appeal.</span
								>
								<span v-else>This case is closed and must be opened in order to add appeals.</span>
							</p>
						</template>
					</empty-result>
				</b-col>
			</b-row>
		</b-container>
	</div>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

import AppealStatusLabel from "@/vendors/components/Appeals/StatusLabel.vue";
import AppealView from "@/vendors/components/Appeals/View.vue";
import CaseActivity from "@/vendors/components/Cases/Activity.vue";
import CaseStatusLabel from "@/vendors/components/Cases/StatusLabel.vue";
import CaseSummary from "@/vendors/components/Cases/Summary.vue";

import FileList from "@/shared/components/FileList.vue";
import PdfFrame from "@/shared/components/PdfFrame.vue";

export default {
	name: "CaseView",
	components: {
		AppealStatusLabel,
		AppealView,
		CaseActivity,
		CaseStatusLabel,
		CaseSummary,
		FileList,
		PdfFrame,
	},
	data() {
		return {
			loading: true,
			loadingFiles: true,
			completingAll: false,
			caseEntity: {},
			caseFiles: [],
			activeAppeal: null,
			timer: null,
			previewingFile: false,
			previewingFileUrl: false,
		};
	},
	computed: {
		hasAppeals() {
			if (!this.caseEntity || !this.caseEntity.appeals) {
				return false;
			}

			return this.caseEntity.appeals.length && this.caseEntity.appeals.length > 0;
		},
		appealCount() {
			if (!this.hasAppeals) {
				return false;
			}

			return this.caseEntity.appeals.length;
		},
		showLoading() {
			return this.loading && (!this.caseEntity.id || this.caseEntity.id == null);
		},
		caseClosed() {
			return this.caseEntity.closed && this.caseEntity.closed !== null;
		},
		...mapGetters({
			apiBaseURL: "baseURL",
			apiToken: "apiToken",
			uploading: "caseFiles/uploading",
			uploadPercent: "caseFiles/uploadPercent",
		}),
	},
	beforeRouteEnter(to, from, next) {
		next((vm) => {
			vm.backUrl = from.fullPath;

			if (to.params.appeal_id) {
				vm.activeAppeal = to.params.appeal_id;
			}

			next();
		});
	},
	beforeRouteUpdate(to, from, next) {
		next((vm) => {
			if (to.params.appeal_id) {
				vm.activeAppeal = to.params.appeal_id;
			}

			next();
		});
	},
	created() {
		this.timer = setInterval(this.silentRefresh, 30000);
	},
	mounted() {
		this.refreshAll();
	},
	methods: {
		refreshAll() {
			this.refresh();
			this.getFiles();
		},
		/**
		 * Get Files
		 */
		async getFiles() {
			try {
				this.loadingFiles = true;

				const response = await this.$store.dispatch("caseFiles/list", {
					id: this.$route.params.id,
				});

				this.caseFiles = response;
			} catch (e) {
				console.error(e);
			} finally {
				this.loadingFiles = false;
			}
		},
		/**
		 * Upload Files
		 */
		async uploadFiles(files) {
			try {
				const response = await this.$store.dispatch("caseFiles/upload", {
					id: this.$route.params.id,
					files: files,
				});
			} catch (e) {
				console.error(e);

				this.$bvToast.toast(e.response.data.message || e.message || "Failed", {
					title: "Upload Failed",
					autoHideDelay: 5000,
					appendToast: true,
					solid: true,
					variant: "danger",
				});
			} finally {
				this.getFiles();
			}
		},
		/**
		 * Download File
		 */
		async downloadFile(file) {
			try {
				const fileName = file.path.split(/(?:\/|\\)+/).pop();

				const response = await this.$store.dispatch("caseFiles/download", {
					id: this.$route.params.id,
					name: fileName,
				});
			} catch (e) {
				console.error(e);

				this.$bvToast.toast(e.response.data.message || e.message || "Failed", {
					title: "Download Failed",
					autoHideDelay: 5000,
					appendToast: true,
					solid: true,
					variant: "danger",
				});
			} finally {
				this.getFiles();
			}
		},
		/**
		 * Preview File
		 */
		previewFile(file) {
			this.previewingFile = file.path;
			this.previewingFileUrl = this.previewFileUrl(file.path);
		},
		/**
		 * Preview File URL
		 */
		previewFileUrl(filePath) {
			const fileBaseName = filePath.split(/(?:\/|\\)+/).pop();
			const fileName = encodeURIComponent(fileBaseName);
			return `${this.apiBaseURL}/cases/${this.$route.params.id}/files/preview?name=${fileName}&token=${this.apiToken}`;
		},
		/**
		 * Delete Files
		 */
		async deleteFile(file) {
			try {
				const fileName = file.path.split(/(?:\/|\\)+/).pop();

				const response = await this.$store.dispatch("caseFiles/delete", {
					id: this.$route.params.id,
					name: fileName,
				});
			} catch (e) {
				console.error(e);

				this.$bvToast.toast(e.response.data.message || e.message || "Failed", {
					title: "Delete Failed",
					autoHideDelay: 5000,
					appendToast: true,
					solid: true,
					variant: "danger",
				});
			} finally {
				this.getFiles();
			}
		},
		/**
		 * Rename Files
		 */
		async renameFile(oldName, newName) {
			try {
				const response = await this.$store.dispatch("caseFiles/rename", {
					id: this.$route.params.id,
					filename: oldName,
					newName: newName,
				});
			} catch (e) {
				console.error(e);

				this.$bvToast.toast(e.response.data.message || e.message || "Failed", {
					title: "Rename Failed",
					autoHideDelay: 5000,
					appendToast: true,
					solid: true,
					variant: "danger",
				});
			} finally {
				this.getFiles();
			}
		},
		/**
		 * Get Details
		 */
		async refresh() {
			try {
				this.loading = true;

				const response = await this.$store.dispatch("cases/get", {
					id: this.$route.params.id,
				});

				this.caseEntity = response;
			} catch (e) {
				console.error(e);
			} finally {
				this.loading = false;
			}
		},
		/**
		 * Appeals
		 */
		updateAppeal(appeal) {
			var index = this.caseEntity.appeals.map((appeal) => appeal.id).indexOf(appeal.id);

			this.caseEntity.appeals[index] = appeal;
			this.refresh();
		},

		/**
		 * Delete Files
		 */
		async completeAppeals() {
			try {
				const message = "Are you sure you want to complete the appeals under this case?";
				if (!confirm(message)) {
					return false;
				}

				this.completingAll = true;

				const response = await this.$store.dispatch("cases/completeAppeals", {
					id: this.$route.params.id,
				});

				this.$bvToast.toast("Completed appeals under this case.", {
					title: "Completed Appeals",
					autoHideDelay: 5000,
					appendToast: true,
					solid: true,
					variant: "primary",
				});

				this.$router.push("/");
			} catch (e) {
				console.error(e);

				this.$bvToast.toast(e.response.data.message || e.message || "Failed", {
					title: "Complete Appeals Failed",
					autoHideDelay: 5000,
					appendToast: true,
					solid: true,
					variant: "danger",
				});
			} finally {
				this.completingAll = false;
			}
		},
	},
	watch: {
		previewingFile(val) {
			if (!val) {
				this.previewingFileUrl = false;
			}
		},
	},
	destroyed() {
		clearInterval(this.timer);
	},
};
</script>
