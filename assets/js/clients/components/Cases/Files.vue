<template>
	<FileManager
		:service="fileService"
		:prefix="id"
		accept=".pdf"
		v-bind="{
			selected,
		}"
		v-slot="{
			browse,
			canMerge,
			deleted,
			empty,
			loading,
			merge,
			merging,
			prefix,
			refresh,
			renamed,
			results,
			uploading,
			uploadPercent,
			zip,
			zipping,
		}"
		@loaded="filesLoaded"
		@merged="mergedDocuments"
		@zipped="zippedDocuments"
		@error="fileError($event)"
	>
		<b-row class="d-flex align-items-center">
			<b-col cols="4" lg="6" xl="4" class="mb-2 text-left">
				<b-button-group>
					<b-button
						variant="primary"
						@click="browse"
						:disabled="loading || uploading"
						title="Upload"
						class="px-2 px-lg-4"
					>
						<font-awesome-icon icon="cloud-upload-alt" fixed-width class="mr-2" />
						<span>Upload</span>
					</b-button>

					<b-button v-if="showRefresh" variant="light" @click="refresh" :disabled="loading" title="Refresh">
						<font-awesome-icon icon="refresh" fixed-width :spin="loading" />
						<span class="sr-only">Refresh</span>
					</b-button>
				</b-button-group>
			</b-col>
			<b-col cols="8" lg="6" xl="8" class="mb-2 text-right">
				<transition name="fade" mode="out-in">
					<div v-if="selected.length > 0">
						<b-button
							variant="info"
							@click="merge(selected)"
							:disabled="merging || !canMerge"
							title="Create a combined PDF file from multiple source files"
						>
							<font-awesome-icon icon="file-pdf" fixed-width />
							Merge PDFs
						</b-button>

						<b-button
							variant="info"
							@click="zip(selected)"
							:disabled="zipping"
							title="Create a .zip archive of selected files"
						>
							<font-awesome-icon icon="file-zipper" fixed-width />
							Zip Files
						</b-button>
					</div>
				</transition>
			</b-col>
		</b-row>
		<transition name="fade" mode="out-in">
			<b-row v-if="uploading">
				<b-col cols="12" class="my-2">
					<b-progress v-show="uploading" :max="100" height="2rem" animated striped>
						<b-progress-bar :value="uploadPercent" class="my-0">
							<h5 class="my-0">{{ uploadPercent.toFixed(0) }}%</h5>
						</b-progress-bar>
					</b-progress>
				</b-col>
			</b-row>
		</transition>
		<div>
			<loading-indicator v-if="loading && empty" class="my-5" />
			<div v-else-if="!empty">
				<b-row no-gutters>
					<b-col cols="12" :lg="previewing ? 6 : 12" :xl="previewing ? 4 : 12">
						<b-form-checkbox-group v-model="selected" name="selected">
							<b-list-group :flush="flush">
								<transition-group name="fade" mode="out-in">
									<FileManagerItem
										v-for="result in results"
										:key="result.path"
										:prefix="prefix"
										:service="fileService"
										:value="result"
										v-slot="{
											canDelete,
											canDownload,
											canPreview,
											canRename,
											deleting,
											destroy,
											download,
											extension,
											fileName,
											isDirectory,
											lastModified,
											lastModifiedTimeAgo,
											mimeType,
											name,
											previewUrl,
											rename,
											renaming,
											size,
										}"
										@deleted="deleted"
										@error="fileError($event)"
										@renamed="renamed"
									>
										<b-list-group-item :disabled="deleting" :active="previewUrl == previewingUrl">
											<b-row>
												<b-col cols="9" md="8" xl="9" class="text-left">
													<b-row>
														<b-col
															cols="12"
															:md="previewing ? 12 : 7"
															:lg="previewing ? 12 : 7"
														>
															<b-row>
																<b-col
																	:cols="previewing ? 2 : 2"
																	:lg="previewing ? 12 : 1"
																>
																	<b-form-checkbox :value="fileName" />
																</b-col>
																<b-col
																	:cols="previewing ? 10 : 10"
																	:lg="previewing ? 12 : 11"
																>
																	<div v-if="deleting">Deleting...</div>
																	<div
																		v-else-if="!isDirectory"
																		class="d-flex align-items-top"
																	>
																		<div></div>
																		<div>
																			<div>
																				<a
																					href="#"
																					v-if="canPreview"
																					@click.prevent="
																						previewFile(
																							fileName,
																							previewUrl
																						)
																					"
																					class="font-weight-bold"
																					:class="
																						previewUrl == previewingUrl
																							? 'text-white'
																							: 'text-dark'
																					"
																				>
																					{{ name }}
																				</a>
																				<span v-else>{{ name }}</span>
																			</div>
																			<div class="small text-muted">
																				<span v-if="extension">{{
																					extension
																				}}</span>
																				<span v-if="extension && size"
																					>&middot;</span
																				>
																				<span v-if="size">{{ size }}</span>
																			</div>
																		</div>
																	</div>
																	<div
																		v-else-if="isDirectory"
																		class="d-flex align-items-top"
																	>
																		<div>
																			<font-awesome-icon
																				icon="folder"
																				fixed-width
																				class="mr-4 text-muted"
																			/>
																		</div>
																		<div>
																			<div>
																				<a href="#">
																					{{ name }}
																				</a>
																			</div>
																		</div>
																	</div>
																</b-col>
															</b-row>
														</b-col>
														<b-col
															cols="12"
															:md="previewing ? 12 : 5"
															:lg="previewing ? 12 : 5"
														>
															<small class="d-none d-md-inline text-muted">
																<span>{{ lastModified.toLocaleDateString() }}</span>
																&middot;
																<span>{{ lastModifiedTimeAgo }}</span>
															</small>
														</b-col>
													</b-row>
												</b-col>
												<b-col cols="3" md="4" xl="3" class="text-right">
													<b-button-group>
														<b-dropdown right variant="light">
															<template #button-content>
																<font-awesome-icon icon="cog" fixed-width />
															</template>
															<b-dropdown-item
																@click="download"
																:disabled="deleting || !canDownload"
																>Download</b-dropdown-item
															>
															<b-dropdown-item
																:href="previewUrl"
																target="_blank"
																:disabled="deleting || !canPreview"
															>
																Preview
															</b-dropdown-item>
															<b-dropdown-item
																@click="rename"
																:disabled="renaming || !canRename"
															>
																Rename
															</b-dropdown-item>
															<b-dropdown-divider />
															<b-dropdown-item
																variant="danger"
																@click="destroy"
																:disabled="deleting || !canDelete"
															>
																<font-awesome-icon icon="trash" />
																<span>Delete</span>
															</b-dropdown-item>
														</b-dropdown>
													</b-button-group>
												</b-col>
											</b-row>
										</b-list-group-item>
									</FileManagerItem>
								</transition-group>
							</b-list-group>
						</b-form-checkbox-group>
					</b-col>
					<b-col v-if="previewing" cols="12" lg="6" xl="8" class="text-right">
						<PdfFrame :url="previewingUrl" />
					</b-col>
				</b-row>
			</div>
			<empty-result v-else icon="cloud-upload-alt">
				No documents uploaded
				<template #content>
					<p class="mb-0">Drag and drop files to attach to this case.</p>
					<p class="mb-0">Case files are available to all levels.</p>
				</template>
			</empty-result>
		</div>
	</FileManager>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import FileManager from "@/clients/components/Layout/file-manager.vue";
import FileManagerItem from "@/clients/components/Layout/file-manager-item.vue";
import PdfFrame from "@/shared/components/PdfFrame.vue";

import * as CaseFileService from "@/clients/services/caseFiles";

export default {
	name: "CaseFiles",
	components: {
		FileManager,
		FileManagerItem,
		PdfFrame,
	},
	props: {
		id: {
			required: true,
			type: [Number, String],
			default: "",
		},
		flush: {
			type: Boolean,
			default: true,
		},
		showRefresh: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			fileService: CaseFileService,
			selected: [],
			previewing: false,
			previewingUrl: "",
		};
	},
	methods: {
		filesLoaded(list) {
			// Cancel previewing if going to display empty
			if (!list || !list.length > 0) {
				this.previewing = false;
			}
		},
		async previewFile(fileName, previewUrl) {
			if (previewUrl == this.previewingUrl) {
				this.previewing = false;
				this.previewingUrl = "";
			} else {
				this.previewing = true;
				this.previewingUrl = previewUrl;
			}
		},
		mergedDocuments(data) {
			const fileCount = data.data.length || 0;
			this.selected = [];

			this.$store.dispatch("notify", {
				variant: "primary",
				title: "Documents Merged",
				message: `${fileCount} file(s) merged into ${data.name}`,
			});
		},
		zippedDocuments(data) {
			const fileCount = data.data.length || 0;
			this.selected = [];

			this.$store.dispatch("notify", {
				variant: "primary",
				title: "Documents Zipped",
				message: `${fileCount} file(s) zipped into ${data.name}`,
			});
		},
		fileError(event) {
			this.$store.dispatch("apiError", {
				error: e,
				title: "File Error",
				message: event.message || "Unable to complete file operation and no message was provided.",
			});
		},
	},
	watch: {
		previewing(val) {
			if (!val) {
				this.previewingUrl = "";
			}
		},
		selected: {
			handler(val) {
				this.$emit("update:selected", val);
			},
		},
	},
};
</script>
