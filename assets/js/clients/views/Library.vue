<template>
	<FileManager
		v-bind="{
			selected,
			service,
			prefix,
		}"
		v-slot="{
			browse,
			canMerge,
			deleted,
			empty,
			files,
			folders,
			loading,
			merge,
			merging,
			refresh,
			renamed,
			results,
			uploading,
			uploadPercent,
			zip,
			zipping,
		}"
		@merged="mergedDocuments"
		@zipped="zippedDocuments"
		@error="fileError($event)"
	>
		<page-header>
			<template #title>Library</template>
			<template #buttons>
				<b-button variant="primary" @click="browse" :disabled="loading || uploading" title="Upload">
					<font-awesome-icon icon="cloud-upload-alt" fixed-width />
					<span>Upload</span>
				</b-button>

				<b-button variant="light" @click="refresh" :disabled="loading" title="Refresh">
					<font-awesome-icon icon="refresh" fixed-width :spin="loading" />
					<span class="sr-only">Refresh</span>
				</b-button>
			</template>
		</page-header>

		<b-container fluid>
			<b-row class="d-flex align-items-center">
				<b-col cols="6" lg="6" xl="8" class="py-2">
					<transition name="fade" mode="out-in">
						<b-progress v-show="uploading" :max="100" height="2rem" animated striped class="my-0">
							<b-progress-bar :value="uploadPercent" class="my-0">
								<h5 class="my-0">{{ uploadPercent.toFixed(0) }}%</h5>
							</b-progress-bar>
						</b-progress>
					</transition>
				</b-col>
				<b-col cols="6" lg="6" xl="4" class="text-right py-2">
					<transition name="fade" mode="out-in">
						<div v-if="selected.length > 0">
							<b-button
								variant="secondary"
								@click="merge(selected)"
								:disabled="merging || !canMerge"
								title="Create a combined PDF file from multiple source files"
							>
								<font-awesome-icon icon="file-pdf" fixed-width />
								Merge PDFs
							</b-button>

							<b-button
								variant="secondary"
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
		</b-container>
		<div>
			<loading-indicator v-if="loading && empty" class="my-5" />
			<b-container fluid v-else-if="!empty">
				<b-row>
					<b-col cols="3">
						<b-card no-body>
							<b-list-group flush>
								<b-list-group-item button :active="prefix === ''" @click="prefix = ''">
									<font-awesome-icon
										:icon="prefix === '' ? 'folder-open' : 'folder'"
										fixed-width
										class="mr-2"
									/>
									<span>Home</span>
								</b-list-group-item>
								<b-list-group-item v-if="prefix !== '' && folders.length <= 0" button active>
									<font-awesome-icon icon="folder-open" fixed-width class="mr-2" />
									<span>{{ prefix }}</span>
								</b-list-group-item>
								<b-list-group-item
									button
									v-for="folder in folders"
									:key="folder.path"
									:active="prefix === folder.path"
									@click="prefix = folder.path"
								>
									<font-awesome-icon
										:icon="prefix === '' ? 'folder-open' : 'folder'"
										fixed-width
										class="mr-2"
									/>
									<span>{{ folder.path }}</span>
								</b-list-group-item>
							</b-list-group>
						</b-card>
					</b-col>
					<b-col cols="9">
						<b-card no-body>
							<b-form-checkbox-group id="checkbox-library" v-model="selected" name="library-selected">
								<b-list-group flush>
									<FileManagerItem
										v-for="result in results"
										:key="result.path"
										:prefix="prefix"
										:service="service"
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
										<b-list-group-item :disabled="deleting">
											<b-row>
												<b-col cols="12" md="6" xl="4" class="text-left">
													<div v-if="deleting">Deleting...</div>
													<div v-else-if="!isDirectory" class="d-flex align-items-top">
														<div>
															<b-form-checkbox :value="fileName" />
														</div>
														<div>
															<div>
																<span>{{ name }}</span>
															</div>
															<div class="small text-muted">
																<span v-if="extension">{{ extension }}</span>
																<span v-if="extension && size">&middot;</span>
																<span v-if="size">{{ size }}</span>
															</div>
														</div>
													</div>
													<div v-else-if="isDirectory" class="d-flex align-items-top">
														<div>
															<font-awesome-icon
																icon="folder"
																fixed-width
																class="mr-4 text-muted"
															/>
														</div>
														<div>
															<div>
																<a href="#" @click.prevent="prefix = result.path">
																	{{ name }}
																</a>
															</div>
														</div>
													</div>
												</b-col>
												<b-col cols="12" md="6" xl="4" class="text-left">
													<div class="text-muted">
														<span>{{ lastModified.toLocaleDateString() }}</span>
														&middot;
														<span>{{ lastModifiedTimeAgo }}</span>
													</div>
												</b-col>
												<b-col cols="12" md="12" xl="4" class="text-left text-lg-right">
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
															@click="destroy"
															:disabled="deleting || !canDelete"
														>
															<font-awesome-icon icon="trash" />
															<span>Delete</span>
														</b-dropdown-item>
													</b-dropdown>
												</b-col>
											</b-row>
										</b-list-group-item>
									</FileManagerItem>
								</b-list-group>
							</b-form-checkbox-group>
						</b-card>
					</b-col>
				</b-row>
			</b-container>
			<b-container v-else>
				<empty-result>
					No files uploaded
					<template #content> Files uploaded here are available to everyone in your organization. </template>
				</empty-result>
			</b-container>
		</div>
	</FileManager>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import FileManager from "@/clients/components/Layout/file-manager.vue";
import FileManagerItem from "@/clients/components/Layout/file-manager-item.vue";

import * as LibraryService from "@/clients/services/libraryFiles";

export default {
	name: "Library",
	components: {
		FileManager,
		FileManagerItem,
	},
	data() {
		return {
			service: LibraryService,
			selected: [],
			prefix: "",
		};
	},
	methods: {
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
};
</script>
