<template>
	<b-container fluid class="my-4">
		<loading-indicator v-if="loading && !this.appeal.id" class="my-5" />
		<error-alert v-else-if="error"> Error </error-alert>
		<b-container v-else-if="editing">
			<b-row>
				<b-col cols="12" class="mb-4">
					<appeal-form :appeal="appeal" @cancel="editing = false" @saved="savedEdit">
						<template #header>
							<b-card-header>
								<div class="d-flex justify-content-between align-items-center">
									<span class="font-weight-bold">Edit Appeal</span>
									<b-button
										variant="secondary"
										size="sm"
										@click="editing = false"
										title="Cancel"
										class="mb-0"
									>
										<font-awesome-icon icon="remove" fixed-width class="my-0 py-0" />
									</b-button>
								</div>
							</b-card-header>
						</template>
					</appeal-form>
				</b-col>
			</b-row>
		</b-container>
		<div v-else>
			<b-row>
				<b-col cols="6" md="3" lg="4" xl="6" order="1" order-md="1" class="text-left mb-4">
					<b-button @click="showDetails = !showDetails" variant="secondary">
						<span v-if="showDetails">Hide Details</span>
						<span v-else>Show Details</span>
					</b-button>
				</b-col>
				<b-col cols="12" md="5" lg="4" xl="3" order="3" order-md="2" class="text-left mb-4">
					<appeal-assign :appeal="appeal" />
				</b-col>
				<b-col cols="6" md="4" lg="4" xl="3" order="2" order-md="3" class="text-right text-md-right mb-4">
					<b-button
						@click="editing = true"
						:active="editing"
						:disabled="caseClosed"
						variant="secondary"
						:title="caseClosed ? 'This case is closed' : 'Edit appeal details'"
					>
						<font-awesome-icon icon="edit" fixed-width />
						<span>Edit Appeal</span>
					</b-button>

					<b-dropdown variant="secondary" right title="Appeal Options">
						<template #button-content>
							<font-awesome-icon icon="cog" fixed-width />
						</template>

						<b-dropdown-item @click="cancelAppeal" :disabled="caseClosed || !canCancel || cancelling">
							<font-awesome-icon icon="ban" fixed-width />
							<span>Cancel Appeal</span>
						</b-dropdown-item>

						<b-dropdown-item @click="reopenAppeal" :disabled="caseClosed || !canReopen || reopening">
							<font-awesome-icon icon="envelope-open" fixed-width />
							<span>Reopen Appeal</span>
						</b-dropdown-item>

						<b-dropdown-divider />

						<b-dropdown-item
							variant="danger"
							@click="deleteAppeal"
							:disabled="deleting || caseClosed || !canDelete"
						>
							<font-awesome-icon icon="trash" fixed-width />
							<span>Delete Appeal</span>
						</b-dropdown-item>
					</b-dropdown>
				</b-col>
			</b-row>
			<b-collapse v-model="showDetails">
				<appeal-summary :appeal="appeal" class="mb-4" />
			</b-collapse>
			<b-row>
				<b-col cols="12" md="12" lg="6" class="mb-2">
					<b-card no-body>
						<b-tabs card active-nav-item-class="font-weight-bold">
							<b-tab>
								<template #title>Case Documents </template>
								<CaseFiles
									ref="caseFiles"
									:id="$route.params.id"
									:flush="false"
									:selected.sync="selectedCaseFiles"
								/>
							</b-tab>
							<b-tab active>
								<template #title>Appeal Documents</template>
								<AppealFiles
									ref="appealFiles"
									:id="$route.params.appeal_id"
									:flush="false"
									:selected.sync="selectedAppealFiles"
								/>
							</b-tab>
						</b-tabs>
					</b-card>
				</b-col>
				<b-col cols="12" md="12" lg="6" class="mb-2">
					<b-card no-body>
						<b-tabs card active-nav-item-class="font-weight-bold">
							<b-tab no-body>
								<template #title>Build</template>
								<appeal-response
									:value="appeal"
									:appeal-files="selectedAppealFiles"
									:case-files="selectedCaseFiles"
									show-case-files
									@submitted="appealPacketSubmitted"
									@remove="unselectFile"
								/>
							</b-tab>
							<b-tab no-body>
								<template #title>
									<span>Create</span>
								</template>
								<b-tabs card pills>
									<b-tab no-body lazy>
										<template #title>Template</template>
										<appeal-cover-page :appeal="appeal" @saved="savedCoverPage" />
									</b-tab>

									<!-- <b-tab no-body lazy>
										<template #title>Template</template>
										<b-card-body>
											<empty-result>
												No templates
												<template #content> No appeal response templates created. </template>
											</empty-result>
										</b-card-body>
									</b-tab> -->
									<b-tab no-body lazy>
										<template #title>Forms</template>
										<b-card-body>
											<b-list-group>
												<b-list-group-item
													href="https://www.hhs.gov/sites/default/files/omha-100.pdf"
													target="_blank"
												>
													<font-awesome-icon icon="file-pdf" fixed-width />
													OMHA-100
												</b-list-group-item>
											</b-list-group>
										</b-card-body>
									</b-tab>
								</b-tabs>
							</b-tab>
							<b-tab no-body>
								<template #title>
									<span>Defendable</span>

									<font-awesome-icon
										v-if="appeal.defendable === true"
										icon="check-circle"
										class="ml-1 pr-0 text-success"
									/>
									<font-awesome-icon
										v-else-if="appeal.defendable === false"
										icon="ban"
										class="ml-1 pr-0 text-danger"
									/>
								</template>
								<appeal-defendable :appeal="appeal" @saved="updatedDefendable" disable-cancel />
							</b-tab>
							<b-tab no-body>
								<template #title>
									<span>UTC</span>

									<b-badge variant="warning" v-if="appeal.unable_to_complete === true" pill>
										<font-awesome-icon icon="exclamation-triangle" class="mx-0 px-0" />
									</b-badge>
								</template>
								<appeal-utc :appeal="appeal" @saved="updatedUtc" disable-cancel />
							</b-tab>

							<b-tab v-if="hasIncomingDocuments" no-body lazy>
								<template #title>Incoming</template>
								<b-alert show variant="info" class="rounded-0 mb-0">
									<font-awesome-icon icon="info-circle" fixed-width />
									Incoming documents are copied to this appeal's files when attaching.
								</b-alert>
								<b-tabs v-if="appeal.incoming_documents.length > 0" card pills lazy vertical>
									<b-tab v-for="document in appeal.incoming_documents" :key="document.id" no-body>
										<template #title>
											<span>{{ $filters.formatTimestamp(document.created) }}</span>
										</template>
										<pdf-frame v-if="document.preview_url" :url="document.preview_url" />
									</b-tab>
								</b-tabs>
								<empty-result v-else>
									No attached documents
									<template #content>
										<p>Documents can be attached from Incoming Documents.</p>
									</template>
								</empty-result>
							</b-tab>
							<b-tab>
								<template #title>Notes</template>

								<add-note-form ref="addNoteForm" @submit="addNote" :saving="addingNote" />

								<div v-if="hasNotes" style="max-height: 40rem" class="mt-2 overflow-y-auto">
									<transition-group name="fade">
										<div
											v-for="note in appeal.appeal_notes"
											:key="note.id"
											class="px-2 py-2 my-4 shadow-sm rounded border"
										>
											<user-note
												:note-id="note.id"
												:user="note.created_by_user"
												:body="note.notes"
												:timestamp="note.created"
												:deletable="canDeleteNote(note)"
												:deleting="deletingNote == note.id"
												@delete="deleteNote"
											/>
										</div>
									</transition-group>
								</div>
							</b-tab>
							<b-tab no-body>
								<template #title>Collaborate</template>

								<b-tabs card pills active-nav-item-class="font-weight-bold">
									<b-tab title="Guest Portal" no-body>
										<appeal-guest-portal
											:appeal="appeal"
											:case-entity="caseEntity"
											@saved="addedPortal"
										/>
									</b-tab>
									<b-tab v-if="enableVendorService">
										<template #title>
											{{ serviceName }}
											<b-badge variant="light">Pro</b-badge>
										</template>
										<appeal-vendor-stages
											:appeal="appeal"
											:case-entity="caseEntity"
											@saved="updatedAppeal"
											@submitted="submittedAppeal"
											@reopened="reopenedAppeal"
											@closed="closedAppeal"
										/>
									</b-tab>
								</b-tabs>
							</b-tab>
						</b-tabs>
					</b-card>
				</b-col>
			</b-row>
		</div>
	</b-container>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

import AppealAssign from "@/clients/components/Appeals/Assign.vue";
import AppealCoverPage from "@/clients/components/Appeals/CoverPage.vue";
import AppealDefendable from "@/clients/components/Appeals/Defendable.vue";
import AppealFiles from "@/clients/components/Appeals/Files.vue";
import AppealForm from "@/clients/components/Appeals/Form.vue";
import AppealGuestPortal from "@/clients/components/Appeals/GuestPortal.vue";
import AppealResponse from "@/clients/components/Appeals/Response.vue";
import AppealStatusLabel from "@/clients/components/Appeals/StatusLabel.vue";
import AppealSummary from "@/clients/components/Appeals/Summary.vue";
import AppealUtc from "@/clients/components/Appeals/UnableToComplete.vue";
import AppealVendorStages from "@/clients/components/Appeals/VendorStages.vue";
import CaseIncomingDocument from "@/clients/components/Cases/IncomingDocument.vue";
import CaseFiles from "@/clients/components/Cases/Files.vue";

import AddNoteForm from "@/shared/components/AddNoteForm.vue";
import UserNote from "@/shared/components/UserNote.vue";
import PdfFrame from "@/shared/components/PdfFrame.vue";

export default {
	name: "ViewAppeal",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					id: null,
					incoming_documents: [],
					appeal_reference_numbers: [],
					appeal_notes: [],
					appeal_type: {
						id: null,
						name: null,
					},
					guest_portals: [],
					unable_to_complete: null,
					can_cancel: false,
					can_delete: false,
				};
			},
		},
		caseEntity: {
			type: Object,
			default: () => {
				return {
					id: null,
					closed: null,
					insurance_provider: null,
					insurance_type: null,
				};
			},
		},
		enableVendorService: {
			type: Boolean,
			default: true,
		},
	},
	components: {
		AppealAssign,
		AppealCoverPage,
		AppealDefendable,
		AppealFiles,
		AppealForm,
		AppealGuestPortal,
		AppealResponse,
		AppealStatusLabel,
		AppealSummary,
		AppealUtc,
		AppealVendorStages,
		CaseIncomingDocument,
		CaseFiles,
		AddNoteForm,
		UserNote,
		PdfFrame,
	},
	computed: {
		caseClosed() {
			return this.caseEntity.closed && this.caseEntity.closed !== null;
		},
		insuranceProvider() {
			if (!this.caseEntity || !this.caseEntity.insurance_provider) {
				return false;
			}

			return this.caseEntity.insurance_provider;
		},
		insuranceType() {
			if (!this.caseEntity || !this.caseEntity.insurance_type) {
				return false;
			}

			return this.caseEntity.insurance_type;
		},
		hasIncomingDocuments() {
			return this.appeal.incoming_documents && this.appeal.incoming_documents.length > 0;
		},
		hasReferenceNumbers() {
			return this.appeal.appeal_reference_numbers && this.appeal.appeal_reference_numbers.length > 0;
		},
		hasNotes() {
			return this.appeal.appeal_notes && this.appeal.appeal_notes.length > 0;
		},
		canCancel() {
			return this.appeal.can_cancel || false;
		},
		canDelete() {
			return this.appeal.can_delete || false;
		},
		canReopen() {
			return this.appeal.can_reopen || false;
		},
		...mapGetters({
			user: "user",
			serviceName: "vendorServiceName",
		}),
	},
	data() {
		return {
			appeal: this.value,
			loading: true,
			error: null,
			editing: false,
			cancelling: false,
			deleting: false,
			reopening: false,
			addingNote: null,
			deletingNote: null,
			showDetails: false,
			selectedCaseFiles: [],
			selectedAppealFiles: [],
		};
	},
	mounted() {
		this.refresh();
	},
	methods: {
		/**
		 * Get Details
		 */
		async refresh() {
			try {
				this.loading = true;
				this.error = false;

				const response = await this.$store.dispatch("appeals/get", {
					id: this.$route.params.appeal_id,
				});

				this.appeal = response;
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Unable to load appeal details",
				});

				this.error = e.response.data.message || "Unable to load appeal details";
			} finally {
				this.loading = false;
			}
		},
		updatedAppeal(appeal, action = "") {
			if (appeal && appeal.id) {
				//this.$emit('update:appeal', appeal);
				this.$emit("saved", appeal);
			}

			this.$store.dispatch("notify", {
				variant: "primary",
				title: "Appeal Updated",
				message: "Appeal updated successfully.",
			});

			this.refresh();
		},
		submittedAppeal(appeal) {
			if (appeal && appeal.id) {
				//this.$emit('update:appeal', appeal);
				this.$emit("saved", appeal);
			}

			this.$store.dispatch("notify", {
				variant: "primary",
				title: "Appeal Submitted",
				message: "Appeal submitted successfully.",
			});

			this.refresh();
		},
		reopenedAppeal(appeal) {
			if (appeal && appeal.id) {
				//this.$emit('update:appeal', appeal);
				this.$emit("saved", appeal);
			}

			this.$emit("reopened", appeal);

			this.$store.dispatch("notify", {
				variant: "primary",
				title: "Appeal Reopened",
				message: "Appeal reopened successfully.",
			});

			this.refresh();
		},
		closedAppeal(appeal) {
			if (appeal && appeal.id) {
				//this.$emit('update:appeal', appeal);
				this.$emit("saved", appeal);
			}

			this.$store.dispatch("notify", {
				variant: "primary",
				title: "Appeal Closed",
				message: "Appeal closed successfully.",
			});

			this.refresh();
		},
		appealPacketSubmitted(response) {
			this.refresh();
		},
		updatedDefendable(appeal) {
			this.$store.dispatch("notify", {
				variant: "primary",
				title: "Appeal Defendability Updated",
				message: "Appeal defendable status updated.",
			});

			this.refresh();
		},
		updatedUtc(appeal) {
			this.$store.dispatch("notify", {
				variant: "primary",
				title: "Appeal UTC Updated",
				message: "Appeal UTC status updated.",
			});

			this.refresh();
		},
		assignedAppeal(response) {
			var message = "Appeal assigned to open queue.";
			if (response.assigned_to_user && response.assigned_to_user.full_name) {
				message = `Appeal assigned to ${response.assigned_to_user.full_name}.`;
			}

			this.$store.dispatch("notify", {
				variant: "primary",
				title: "Appeal Assigned",
				message: message,
			});

			this.$emit("update:appeal", response);
		},
		async cancelAppeal() {
			const message = "Are you sure you want to cancel this appeal?";
			if (!confirm(message)) {
				return false;
			}

			try {
				this.cancelling = true;

				const response = await this.$store.dispatch("appeals/cancel", {
					id: this.appeal.id,
				});

				this.$store.dispatch("notify", {
					variant: "primary",
					title: "Appeal Cancelled",
					message: `Appeal level ${this.appeal.appeal_level.name || "(Unknown)"} was cancelled.`,
				});

				this.$emit("cancelled", response.data);
				this.refresh();
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Cancel Failed",
					message: "Failed to cancel appeal",
				});
			} finally {
				this.cancelling = false;
			}
		},
		async reopenAppeal() {
			const message = `Are you sure? This will reset the status to 'Open' and cancel any progression.`;
			if (!confirm(message)) {
				return false;
			}

			try {
				this.reopening = true;

				const response = await this.$store.dispatch("appeals/reopen", {
					id: this.appeal.id,
				});

				this.$store.dispatch("notify", {
					variant: "primary",
					title: "Appeal Reopened",
					message: `Appeal level ${this.appeal.appeal_level.name || "(Unknown)"} was reopened.`,
				});

				this.$emit("update:appeal", response);
				this.$emit("reopened", response);
				this.refresh();
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Reopen Failed",
					message: "Failed to reopen appeal",
				});
			} finally {
				this.reopening = false;
			}
		},
		async deleteAppeal() {
			const message = "Are you sure you want to delete this appeal?";
			if (!confirm(message)) {
				return false;
			}

			try {
				this.deleting = true;

				const response = await this.$store.dispatch("appeals/delete", {
					id: this.appeal.id,
				});

				const appealLevel = (response.data.appeal_level && response.data.appeal_level.name) || "(Missing)";

				this.$emit("deleted", response.data);

				this.$router.push({
					name: "cases.view",
					params: {
						id: this.$route.params.id,
					},
				});

				this.$nextTick(() => {
					this.$store.dispatch("notify", {
						variant: "primary",
						title: "Appeal Deleted",
						message: `Appeal level ${appealLevel} was deleted.`,
					});
				});
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Delete Failed",
					message: "Failed to delete appeal",
				});
			} finally {
				this.deleting = false;
			}
		},
		/**
		 * Edit
		 */
		savedEdit(appeal) {
			this.editing = false;
			this.refresh();
			this.$emit("updated", appeal);
		},
		/**
		 * Notes
		 */
		async addNote(note) {
			try {
				this.addingNote = true;

				const response = await this.$store.dispatch("appealNotes/addNote", {
					id: this.appeal.id,
					notes: note.notes,
				});

				this.appeal.appeal_notes.unshift(response);

				if (this.$refs.addNoteForm) {
					this.$refs.addNoteForm.reset();
				}
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Add Note Failed",
					message: "Failed to add appeal note",
				});
			} finally {
				this.addingNote = false;
				this.refresh();
			}
		},
		canDeleteNote(note) {
			if (!note || !note.id) return false;
			if (this.user.admin) return true;

			return note.created_by == this.user.id;
		},
		async deleteNote(noteId) {
			try {
				this.deletingNote = noteId;

				const response = await this.$store.dispatch("appealNotes/deleteNote", {
					appeal_id: this.appeal.id,
					id: noteId,
				});

				const index = this.appeal.appeal_notes.findIndex((note) => {
					return note.id == response.data.id;
				});

				if (index !== -1) {
					this.appeal.appeal_notes.splice(index, 1);
				}
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Delete Note Failed",
					message: "Failed to delete appeal note",
				});
			} finally {
				this.deletingNote = null;
				this.refresh();
			}
		},
		/**
		 * Files
		 */
		unselectFile(selectedFile) {
			switch (selectedFile.source) {
				case "Appeal":
					const appealFileIdx = this.selectedAppealFiles.findIndex((file) => file == selectedFile.file);
					if (appealFileIdx !== -1) {
						this.selectedAppealFiles.splice(appealFileIdx, 1);
					}
					break;
				case "Case":
					const caseFileIdx = this.selectedCaseFiles.findIndex((file) => file == selectedFile.file);
					if (caseFileIdx !== -1) {
						this.selectedCaseFiles.splice(caseFileIdx, 1);
					}
					break;
				default:
					this.$store.dispatch("notify", {
						variant: "warning",
						title: "Invalid File Source",
						message: `File ${selectedFile.basename} from ${selectedFile.source} was unable to be unselected.`,
					});
					break;
			}
		},
		/**
		 * Portals
		 */
		addedPortal(portal) {
			console.log("New Guest Portal", portal);
			this.refresh();
		},
		/**
		 * Cover Page
		 */
		savedCoverPage(response) {
			this.$refs.appealFiles.refresh();
			this.refresh();
		},
	},
};
</script>
