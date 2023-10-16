<template>
	<div>
		<loading-indicator v-if="loading && !document.id" class="my-5" />
		<b-row v-else no-gutters>
			<b-col cols="12" md="6" lg="6" order="2" order-md="1" class="mb-2">
				<pdf-frame
					ref="previewFrame"
					v-if="document.preview_url"
					:url="document.preview_url"
					:title="document.original_name_base"
					:loading="loading"
					top="60px"
				/>
			</b-col>
			<b-col cols="12" md="6" lg="6" order="1" order-md="2" class="mb-2 p-2">
				<b-card no-body class="p-0 m-0 mb-3">
					<b-button
						block
						variant="light"
						@click="showingDetails = !showingDetails"
						:pressed="showingDetails"
						class="text-left d-flex justify-content-between align-items-center py-3"
						title="Toggle document details and options"
					>
						<div class="text-left mr-2">
							<font-awesome-icon :icon="showingDetails ? 'chevron-up' : 'chevron-right'" fixed-width />
							<span class="font-weight-bold" v-if="document.original_name_base">
								{{ document.original_name_base }}
							</span>
							<span v-else class="text-warning font-weight-bold"> Missing Name </span>
						</div>
						<div class="text-right">
							<b-badge v-if="document.unable_to_complete" title="Unable To Complete" variant="warning">
								<font-awesome-icon icon="exclamation-triangle" />
								<span>Unable To Complete</span>
							</b-badge>
						</div>
					</b-button>

					<b-collapse id="showingDetails" v-model="showingDetails" class="my-0">
						<b-card-body>
							<b-row class="mb-2">
								<b-col cols="12">
									<p class="text-muted mb-0">
										Created {{ $filters.fromNow(document.created) }} on
										{{ $filters.formatTimestamp(document.created) }}
									</p>
								</b-col>
							</b-row>
							<b-row>
								<b-col cols="12" lg="6">
									<b-form-group
										id="assignTo"
										label="Assigned To"
										label-for="assignTo"
										label-cols="4"
										label-cols-lg="12"
										description="Who is responsible for data entry for this document"
									>
										<b-input-group>
											<b-select
												v-model="document.assigned_to"
												@change="assign"
												:disabled="loading || updating"
												:options="activeUsers"
												value-field="id"
												text-field="full_name"
											>
												<template #first>
													<option :value="null">(Unassigned)</option>
												</template>
											</b-select>
										</b-input-group>
									</b-form-group>
								</b-col>
								<b-col cols="12" lg="6">
									<b-form-group
										id="facility"
										label="Facility"
										label-for="facility"
										label-cols="4"
										label-cols-lg="12"
										description="The associated facility for this document"
									>
										<b-select
											v-model="document.facility_id"
											@change="changeFacility"
											:disabled="loading || updating"
											:options="facilities"
											value-field="id"
											text-field="name"
										>
											<template #first>
												<option :value="null">(None)</option>
											</template>
										</b-select>
									</b-form-group>
								</b-col>
							</b-row>
							<b-row>
								<b-col cols="12">
									<b-form-group
										id="utc"
										label="Unable To Complete"
										label-for="utc"
										label-cols="4"
										label-cols-lg="12"
										description="This document is unable to be progressed any further"
									>
										<b-form-checkbox
											name="utc"
											v-model="document.unable_to_complete"
											:disabled="updating"
											@change="changedUtc"
										>
											UTC
										</b-form-checkbox>
									</b-form-group>
								</b-col>
							</b-row>
						</b-card-body>
						<b-card-footer class="d-flex justify-content-between align-items-center">
							<div class="text-left mr-2">
								<b-dropdown variant="secondary" right title="Document Options">
									<template #button-content>
										<font-awesome-icon icon="cog" fixed-width />
									</template>
									<b-dropdown-item @click="reloadDocument" title="Reload document preview pane">
										<font-awesome-icon icon="sync" fixed-width />
										<span>Reload Preview</span>
									</b-dropdown-item>
								</b-dropdown>
							</div>
							<div class="text-right">
								<b-button variant="danger" @click="deleteDocument">
									<font-awesome-icon icon="trash" fixed-width />
									<span>Delete Document</span>
								</b-button>
							</div>
						</b-card-footer>
					</b-collapse>
				</b-card>

				<b-row>
					<b-col cols="12" class="mb-2">
						<patient-search
							v-if="!addingPatient"
							v-model="currentPatient"
							@add="addingPatient = true"
							@clear="resetPatient"
							placeholder="Search patients..."
						/>
									
						<patient-form v-else @cancel="addingPatient = false" @saved="addedNewPatient">
							<template #header>
								<b-card-header>
									<div class="d-flex justify-content-between align-items-center">
										<span class="font-weight-bold">Add New Patient</span>
										<b-button
											variant="secondary"
											size="sm"
											@click="addingPatient = false"
											title="Cancel"
											class="mb-0"
										>
											<font-awesome-icon icon="remove" fixed-width class="my-0 py-0" />
										</b-button>
									</div>
								</b-card-header>
							</template>
						</patient-form>
					</b-col>
				</b-row>

				<transition name="fade">
					<div v-if="hasPatient && !addingPatient" class="mb-2">
						<incoming-document-attach
							:patient="currentPatient"
							:document="document"
							@refresh="refresh()"
							@attached="attachedDocument"
						/>
					</div>
				</transition>
			</b-col>
		</b-row>
	</div>
</template>

<script>
import { mapGetters } from "vuex";

import AppealStatusLabel from "@/clients/components/Appeals/StatusLabel.vue";
import IncomingDocumentAttach from "@/clients/components/IncomingDocuments/Attach.vue";
import PatientForm from "@/clients/components/Patients/Form.vue";
import PatientSearch from "@/clients/components/Search/Patients.vue";

import PdfFrame from "@/shared/components/PdfFrame.vue";

export default {
	name: "IndexViewIncomingDocument",
	components: {
		AppealStatusLabel,
		IncomingDocumentAttach,
		PatientForm,
		PatientSearch,
		PdfFrame,
	},
	props: {
		currentDocument: {
			type: Object,
			default: () => {
				return {
					id: null,
					created: null,
					created_by: null,
					modified: null,
					modified_by: null,
					deleted: null,
					deleted_by: null,
					facility_id: null,
					case_id: null,
					file_name: "",
					assigned: null,
					assigned_to: null,
					appeal_id: null,
					original_name: "",
					unable_to_complete: null,
				};
			},
		},
	},
	data() {
		return {
			document: this.currentDocument,
			updating: false,
			loading: true,
			error: null,
			initialLoaded: false,
			addingPatient: false,
			currentPatient: {
				id: null,
			},
			searchingPatients: false,
			showingDetails: false,
		};
	},
	computed: {
		hasPatient() {
			if (!this.currentPatient) {
				return false;
			}

			if (!this.currentPatient.id) {
				return false;
			}

			return true;
		},
		isUtc() {
			return this.document.unable_to_complete;
		},
		...mapGetters({
			loadingUsers: "users/loadingActive",
			activeUsers: "users/active",
			count: "incomingDocuments/count",
			loadingCount: "incomingDocuments/loadingCount",
			facilities: "facilities/active",
			uploading: "incomingDocuments/uploading",
			uploadPercent: "incomingDocuments/uploadPercent",
			user: "user",
		}),
	},
	mounted() {
		this.refresh();
		this.$store.dispatch("updateState");
	},
	methods: {
		async refresh() {
			try {
				this.loading = true;
				const response = await this.$store.dispatch("incomingDocuments/get", {
					id: this.$route.params.id,
				});
				this.document = response;
				this.$emit("refreshed", response);
			} finally {
				this.loading = false;
			}
		},
		async assign() {
			try {
				this.updating = true;

				const response = await this.$store.dispatch("incomingDocuments/assign", {
					id: this.$route.params.id,
					user_id: this.document.assigned_to,
				});

				this.document.assigned_to = response.assigned_to;
				this.$emit("saved", response);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Assign Failed",
					message: "Failed to assign document to user",
				});
			} finally {
				this.updating = false;
			}
		},
		async deleteDocument() {
			const message = `Are you sure you want to delete this document?`;
			if (!confirm(message)) {
				return false;
			}

			try {
				const response = await this.$store.dispatch("incomingDocuments/delete", {
					id: this.$route.params.id,
				});

				this.$store.dispatch("updateState");
				this.$emit("deleted", response);

				const dateCreated = this.$filters.formatTimestamp(response.created);
				this.$nextTick(() => {
					this.$store.dispatch("notify", {
						variant: "primary",
						title: "Document Deleted",
						message: `Document from ${dateCreated} was deleted.`,
					});
				});
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Delete Failed",
					message: "Failed to delete document",
				});
			}
		},
		addedNewPatient(patient) {
			this.addingPatient = false;
			this.selectedPatient(patient);
		},
		removedPatient() {
			this.resetPatient();
		},
		selectedPatient(patient) {
			this.currentPatient = patient;
		},
		resetPatient() {
			this.currentPatient = {
				id: null,
			};
		},
		async searchPatients(query, done) {
			try {
				this.searchingPatients = true;

				const response = await this.$store.dispatch("patients/index", {
					search: query,
				});

				this.patients = response.data;
				done(response.data);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Find Patient Failed",
					message: "An error occurred trying to find patients",
				});
			} finally {
				this.searchingPatients = false;
			}
		},
		reloadDocument() {
			if (this.$refs.previewFrame) {
				this.$refs.previewFrame.reload();
			}
		},
		attachedDocument(document) {
			const dateCreated = this.$filters.formatTimestamp(document.created);

			var message = `Document from ${dateCreated} was attached to case #${document.case_id}.`;

			if (document.appeal_id) {
				const appealLevelName = document.appeal.appeal_level.name || "#" + document.appeal_id;
				message = `Document from ${dateCreated} was attached to appeal ${appealLevelName} in case #${document.case_id}.`;
			}
			if (document.request_id) {
				message = `Document from ${dateCreated} was attached to request #${document.request_id}.`;
			}

			this.$store.dispatch("notify", {
				variant: "primary",
				title: "Document Attached",
				message: message,
			});

			this.$emit("attached", document);
			this.$store.dispatch("updateState");
			this.refresh();
		},
		async changeFacility() {
			try {
				this.updating = true;

				const response = await this.$store.dispatch("incomingDocuments/save", {
					id: this.$route.params.id,
					facility_id: this.document.facility_id,
				});

				this.$emit("saved", response);
				this.refresh();
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Assign Failed",
					message: "Failed to assign facility to document",
				});
			} finally {
				this.updating = false;
			}
		},
		changedUtc(value) {
			this.setUtc(value);
		},
		async setUtc(value = true) {
			try {
				this.updating = true;

				if (value == true) {
					const response = await this.$store.dispatch("incomingDocuments/setUnableToComplete", {
						id: this.$route.params.id,
					});

					this.$emit("saved", response);
				} else {
					const response = await this.$store.dispatch("incomingDocuments/unsetUnableToComplete", {
						id: this.$route.params.id,
					});

					this.$emit("saved", response);
				}

				this.refresh();
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Update Failed",
					message: "Failed to update UTC status on document",
				});
			} finally {
				this.updating = false;
			}
		},
	},
	watch: {
		currentPatient: {
			immediate: false,
			handler(val) {},
		},
		"$route.params.id": {
			handler(newVal) {
				this.refresh();
			},
		},
	},
};
</script>
