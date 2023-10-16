<template>
	<div>
		<page-header class="align-items-center">
			<template #beforeTitle>
				<b-button
					v-if="!isEmpty"
					v-b-toggle.sidebar-list
					title="Hide or display the incoming document list"
					variant="secondary"
					class="d-inline-block d-xl-none mr-2 mr-lg-3"
				>
					<font-awesome-layers full-width>
						<font-awesome-icon icon="bars" />
						<font-awesome-layers-text counter class="fa-2x" :value="count" position="top-right" />
					</font-awesome-layers>
				</b-button>
			</template>
			<template #title>Incoming test </template>
			<template #buttons>
				<upload-button
					ref="uploadButton"
					@upload="uploadFiles"
					:disabled="uploading"
					variant="primary"
					accept=".pdf"
				>
					<span v-if="uploading">
						<font-awesome-icon icon="circle-notch" spin fixed-width />
						<span>Uploading...</span>
					</span>
					<span v-else>
						<font-awesome-icon icon="file-upload" fixed-width />
						<span>Upload <span class="d-none d-lg-inline">Documents</span></span>
					</span>
				</upload-button>
			</template>
		</page-header>

		<div v-if="uploading" class="bg-dark text-light">
			<b-container>
				<b-row class="py-4">
					<b-col class="text-center">
						<h3 class="mt-0 mb-2">Uploading documents...</h3>
						<upload-progress :value="uploadPercent" />
					</b-col>
				</b-row>
			</b-container>
		</div>

		<upload-drag-and-drop @dropped="uploadFiles">
			<loading-indicator v-if="!initialLoaded" class="my-5" />
			<error-alert v-else-if="error" class="my-5" v-text="error" />
			<b-container v-else-if="isEmpty">
				<b-card class="bg-white rounded shadow-lg mb-5 mt-5 p-2">
					<b-row class="d-flex align-items-center">
						<b-col cols="12" lg="9" class="text-center text-lg-left">
							<b-row class="d-flex align-items-center mb-4">
								<b-col cols="12" lg="2" xl="1" class="mb-4 mb-lg-0">
									<font-awesome-icon
										fixed-width
										icon="cloud-upload-alt"
										size="2x"
										class="text-muted"
									/>
								</b-col>
								<b-col cols="12" lg="10" xl="11">
									<h4 class="mb-0 text-dark font-weight-bold">
										<span>Upload documents to get started.</span>
									</h4>
								</b-col>
							</b-row>
							<b-row>
								<b-col cols="12" class="mb-4 mb-lg-0">
									<p class="mb-2 text-dark">
										Upload a PDF of your denial letter or relevant documentation to start building a
										case.
									</p>
									<p class="mb-1 small text-muted">
										Drag and drop here, or press the upload button to browse for files.
									</p>
								</b-col>
							</b-row>
						</b-col>
						<b-col cols="12" lg="3" class="text-center text-lg-right">
							<upload-button
								ref="uploadButton"
								@upload="uploadFiles"
								:disabled="uploading"
								variant="primary"
								accept=".pdf"
								size="lg"
								:class="isEmpty ? 'mr-0' : 'mr-4'"
							>
								<span v-if="uploading">
									<font-awesome-icon icon="circle-notch" spin fixed-width />
									<span>Uploading...</span>
								</span>
								<span v-else>
									<font-awesome-icon icon="file-upload" fixed-width />
									<span>Upload Documents</span>
								</span>
							</upload-button>
						</b-col>
					</b-row>
				</b-card>
			</b-container>

		
			<b-container fluid  class="h-100 px-0 overflow-hidden">
				<b-row no-gutters class="justify-content-center h-100">
					<b-col cols="12" xl="2" class="d-none d-xl-block">
						<b-form-checkbox-group v-model="selectedDocuments" name="selectedDocuments">
							<!-- for making the document section Collapsible -->
							<div id="app">
								<button class="btn btn-primary" @click="toggleCollapse"><strong> {{showList ? '<' : '>' }}</strong></button>
								<!-- <button class="btn btn-primary" @click="toggleCollapse">
									<img v-if="showList" :src="collapseImageUrl" alt="<">
									<img v-else :src="expandImageUrl" alt=">">
								</button> -->

								<br>
								<div v-if="showList">
									<b-list-group flush class="rounded-0 overflow-y-auto" style="max-height: 55rem">
									<b-list-group-item
										v-for="document in incomingDocuments"
										:key="document.id"
										class="cursor-pointer px-2 py-2 rounded-0"
										:active="document.id == $route.params.id"
									>
									<div class="d-flex align-items-start">
										<b-form-checkbox
											name="selectedDocuments"
											:value="document.id"
											class="mr-0 pr-0"
										></b-form-checkbox>
										<b-container   fluid @click="viewDocument(document)">
											<b-row  >
												<b-col cols="10">
													<p class="font-weight-bold mb-0">
														{{ document.original_name_base }} 
													</p>
												</b-col>
												<b-col cols="2" class="text-right">
													<font-awesome-icon
														v-if="document.unable_to_complete"
														icon="exclamation-triangle"
														class="text-warning"
														title="Unable To Complete"
													/>
												</b-col>
											</b-row>
											<b-row class="d-flex justify-content-between align-items-center">
												<b-col cols="6" md="12">
													<div v-if="document.created" class="small text-muted">
														{{ $filters.formatTimestamp(document.created) }}
													</div>
												</b-col>
												<b-col cols="6" md="12" class="text-right text-md-left">
													<div
														v-if="
															document.assigned_to_user &&
															document.assigned_to_user.full_name
														"
														class="small text-accent"
													>
														{{ document.assigned_to_user.full_name }} 
													</div>
													<div v-else class="small text-muted font-italic">Unassigned</div>
												</b-col>
											</b-row>
											<b-row>
												<b-col
													v-if="document.facility && document.facility.name"
													cols="12"
													class="small text-muted"
												>
													{{ document.facility.name }}
												</b-col>
											</b-row>
										</b-container>
									</div>
									</b-list-group-item>
									</b-list-group>
								</div>
							</div>
						</b-form-checkbox-group>

						<transition name="fade">
							<b-container fluid v-if="selectedDocuments && selectedDocuments.length > 0" class="my-3">
								<b-card no-body class="shadow">
									<template #header>
										<b-badge pill variant="primary" class="mb-0">{{
											selectedDocuments.length
										}}</b-badge>
										Selected
									</template>
									<b-card-body>
										<b-form-group
											id="assignTo"
											label="Assign To"
											label-for="assignTo"
											label-cols="12"
										>
											<b-input-group>
												<b-select
													v-model="bulkAssignTo"
													@change="bulkAssign"
													:disabled="loading || bulkUpdating"
													:options="users"
													value-field="id"
													text-field="full_name"
												>
													<template #first>
														<option :value="null"></option>
														<option :value="false">(Unassign)</option>
													</template>
												</b-select>
											</b-input-group>
										</b-form-group>
									</b-card-body>
								</b-card>
							</b-container>
						</transition>
					</b-col>
					
					<b-col cols="12" xl="10" >
						<router-view
							:id="$route.params.id"
							@deleted="deletedDocument"
							@saved="updatedDocument"
							@attached="attachedDocument"
						/>
					</b-col>
			
				
				</b-row>
			</b-container>


			<!-- List Sidebar -->
			<b-sidebar id="sidebar-list" backdrop shadow>
				<template #title> Incoming </template>
				<b-form-checkbox-group v-model="selectedDocuments" name="selectedDocuments">
					<b-list-group flush class="rounded-0 shadow-sm overflow-y-auto" style="max-height: 45rem">
						<b-list-group-item
							v-for="document in incomingDocuments"
							:key="document.id"
							class="cursor-pointer px-2 py-2 rounded-0"
							:active="document.id == $route.params.id"
						>
							<div class="d-flex align-items-start">
								<b-form-checkbox
									name="selectedDocuments"
									:value="document.id"
									class="mr-0 pr-0"
								></b-form-checkbox>

								<b-container fluid @click="viewDocument(document)">
									<b-row v-if="checkPermission" >
										<b-col cols="10">
											<p  class="font-weight-bold mb-0">{{ document.original_name_base }}</p>
										</b-col>
										<b-col cols="2" class="text-right">
											<font-awesome-icon
												v-if="document.unable_to_complete"
												icon="exclamation-triangle"
												class="text-warning"
												title="Unable To Complete"
											/>
										</b-col>
									</b-row>
									<b-row class="d-flex justify-content-between align-items-center">
										<b-col cols="6" md="12">
											<div v-if="document.created" class="small text-muted">
												{{ $filters.formatTimestamp(document.created) }}
											</div>
										</b-col>
										<b-col cols="6" md="12" class="text-right text-md-left">
											<div
												v-if="document.assigned_to_user && document.assigned_to_user.full_name"
												class="small text-accent"
											>
												{{ document.assigned_to_user.full_name }} 
											</div>
											<div v-else class="small text-muted font-italic">Unassigned</div>
										</b-col>
									</b-row>
									<b-row>
										<b-col
											v-if="document.facility && document.facility.name"
											cols="12"
											class="small text-muted"
										>
											{{ document.facility.name }}
										</b-col>
									</b-row>
								</b-container>
							</div>
						</b-list-group-item>
					</b-list-group>
				</b-form-checkbox-group>
			</b-sidebar>
		</upload-drag-and-drop>
	</div>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

import UploadButton from "@/shared/components/UploadButton.vue";
import UploadDragAndDrop from "@/shared/components/UploadDragAndDrop.vue";
import UploadProgress from "@/shared/components/UploadProgress.vue";
import axios from 'axios';

export default {
	name: "IncomingDocumentIndex",
	components: {
		UploadButton,
		UploadDragAndDrop,
		UploadProgress,
	},
	data() {
		return {
			showList: true,
			initialLoaded: false,
			loading: true,
			error: null,
			incomingDocuments: [],
			pagination: {},
			page: 1,
			filters: {},
			sort: null,
			sortDesc: true,
			redirectingDocument: false,
			selectedDocuments: [],
			bulkAssignTo: null,
			bulkUpdating: false,
			showList: true,
			collapseImageUrl: "webroot/img/collapse.jpg",
      		expandImageUrl: "webroot/img/expand.jpg",
			permission:null,
			client_admin:null,
		};
	},
	computed: {
		isEmpty() {
			return this.incomingDocuments.length <= 0;
		},
		...mapGetters({
			users: "users/active",
			loadingUsers: "users/loadingActive",
			count: "incomingDocuments/count",
			loadingCount: "incomingDocuments/loadingCount",
			uploading: "incomingDocuments/uploading",
			uploadPercent: "incomingDocuments/uploadPercent",
		}),
	},
	mounted() {
		this.refresh();
		this.checkPermission();
	},
	methods: {
		viewFirstDocument() {
			// Prevents snapping to first document when already left route
			if (!this.redirectingDocument) {
				return false;
			}

			if (this.incomingDocuments[0] && this.incomingDocuments[0].id) {
				this.$router.push({
					name: "incomingDocuments.view",
					params: {
						id: this.incomingDocuments[0].id,
					},
				});
			}

			this.redirectingDocument = false;
		},
		async viewDocument(document) {
			console.log('users = ',this.users);
			this.$router.push({
				name: "incomingDocuments.view",
				params: {
					id: document.id,
				},
			});

			//below code snipper is for sending the document id to the flask app
			const dataToSend = { 'id': document.id }; // Replace with your actual data
      
			try {
				const response = await axios.post('/client/documents', dataToSend);
			} 
			catch (error) {
				console.error('Error sending POST request:', error);
			}

		},
		async refresh() {
			try {
				this.loading = true;
				this.redirectingDocument = true;

				const response = await this.$store.dispatch("incomingDocuments/index", {
					page: this.page,
					sort: this.sort,
					order: this.sortDesc === true ? "desc" : "asc",
					...this.filters,
				});

				this.incomingDocuments = response.data;
				this.pagination = response.pagination;
				this.page = response.pagination.page;
				this.initialLoaded = true;

				if (!this.$route.params.id) {
					this.viewFirstDocument();
				}
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Error Finding Documents",
					message:
						"An error occurred trying to find documents. Please contact support if the issue persists.",
				});

				if (e.response.data.message) {
					this.error = e.response.data.message;
				}
			} finally {
				this.loading = false;
			}
		},
		async uploadFiles(files) {
			try {
				const response = await this.$store.dispatch("incomingDocuments/upload", {
					files: files,
				});

				if (response.data[0] && response.data[0].id) {
					this.$router.replace({
						name: "incomingDocuments.view",
						params: {
							id: response.data[0].id,
						},
					});
				}

				this.$store.dispatch("updateState");
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Upload Failed",
					message:
						"Failed to upload document. Please check if the file is too large or is in a supported format.",
				});
			} finally {
				this.refresh();
			}
		},
		attachedDocument(document) {
			const index = this.incomingDocuments.findIndex((i) => i.id == document.id);
			if (index > -1) {
				this.incomingDocuments.splice(index, 1);
			}

			this.refresh();
			this.viewFirstDocument();
		},
		deletedDocument(document) {
			const index = this.incomingDocuments.findIndex((i) => i.id == document.id);
			if (index > -1) {
				this.incomingDocuments.splice(index, 1);
			}

			this.$router.replace({
				name: "incomingDocuments",
			});

			this.refresh();
		},
		updatedDocument(document) {
			const index = this.incomingDocuments.findIndex((i) => i.id == document.id);
			if (index > -1) {
				this.incomingDocuments[index] = document;
			}
			this.refresh();
		},
		async bulkAssign(userId) {
			const documentCount = this.selectedDocuments.length;

			if (!userId) {
				if (!confirm(`Unassign ${documentCount} document(s)?`)) {
					return false;
				}
			} else {
				const user = this.users.find((user) => user.id == userId);

				if (!confirm(`Reassign ${documentCount} document(s) to ${user.full_name}?`)) {
					return false;
				}
			}

			try {
				this.bulkUpdating = true;

				const response = await this.$store.dispatch("incomingDocuments/bulkAssign", {
					user_id: userId,
					document_ids: this.selectedDocuments,
				});

				const data = {'id':userId}

				// Use Axios to make the request to the controller for sending user_id 
				console.log(data);
				const resp = await axios.post('/client/sendemail', data);
				console.log(resp);
				this.$store.dispatch("notify", {
					variant: "primary",
					title: "Bulk Assign Completed",
					message: "Selected documents successfully assigned.",
				});

				this.selectedDocuments = [];

				this.refresh();
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Bulk Assign Failed",
					message: "Failed to bulk assign incoming documents.",
				});
			} finally {
				this.bulkUpdating = false;
			}
		},
		toggleCollapse() {
      this.showList = !this.showList;
    	},

		// for checking if the user has permission to view a document or not
		async checkPermission(){
			try {
				const response = await axios.get('/client/documentspermisson');
				this.permission = response.data['success'];
				this.client_admin = response.data['client_admin']
			} 
			catch (error) {
				console.error('Error sending POST request:', error);
				return true;
			}
			console.log("permission = ", this.permission);
			console.log("documents = ", this.incomingDocuments);

			
		}


	},
	destroyed() {
		this.redirectingDocument = false;
	},
	// Prevents snapping to first document when already left route
	beforeRouteLeave(to, from, next) {
		this.redirectingDocument = false;
		next();
	},
};
</script>
