<template>
	<b-card no-body class="rounded-0">
		<b-tabs card pills>
			<b-tab active no-body>
				<template #title>Details</template>
				<b-container fluid class="mt-4">
					<b-row>
						<b-col cols="12" lg="6" class="text-center text-lg-left mb-4">
							<h4 class="font-weight-bold text-dark">
								<span v-if="appeal.appeal_level">{{ appeal.appeal_level.name }}</span>
							</h4>
							<div>
								<h5 v-if="appeal.appeal_type.name" class="text-muted d-inline">
									{{ appeal.appeal_type.name }}
								</h5>

								<span class="text-muted">&mdash;</span>

								<appeal-status-label :value="appeal" />
							</div>
						</b-col>
						<b-col cols="12" lg="6" class="text-center text-lg-right mb-2 mb-lg-0">
							<b-button variant="primary" @click="completeAppeal" :disabled="!canComplete || completing">
								<font-awesome-icon icon="check-circle" fixed-width />
								<span>Mark Completed</span>
							</b-button>

							<b-button-group>
								<b-dropdown variant="light" right title="More Options">
									<template #button-content>
										<font-awesome-icon icon="cog" fixed-width />
									</template>

									<b-dropdown-item @click="returnAppeal" :disabled="!canReturn || returning">
										<font-awesome-icon icon="ban" fixed-width />
										<span>Return to Client</span>
										<div class="small text-muted">Mark appeal as unable to complete.</div>
									</b-dropdown-item>

									<b-dropdown-divider />

									<b-dropdown-item
										@click="editingDefendable = !editingDefendable"
										:active="editingDefendable"
									>
										<font-awesome-icon icon="question-circle" fixed-width />
										<span>Edit Defendable Status</span>
									</b-dropdown-item>
								</b-dropdown>
							</b-button-group>
						</b-col>
					</b-row>
				</b-container>

				<b-container fluid>
					<appeal-defendable
						v-show="editingDefendable"
						:appeal="appeal"
						@saved="updatedDefendable"
						@cancel="editingDefendable = false"
						class="mb-4"
					/>

					<appeal-summary :appeal="appeal" class="mb-2" />
				</b-container>

				<hr />

				<b-container fluid>
					<b-row class="mb-4">
						<b-col cols="12" lg="2">
							<h5 class="h5 mb-4 text-muted">Notes</h5>
						</b-col>
						<b-col cols="12" lg="10">
							<add-note-form ref="addNoteForm" @submit="addNote" :saving="addingNote" />
							<div
								v-if="appeal.appeal_notes && appeal.appeal_notes.length > 0"
								style="max-height: 40rem; overflow-y: scroll"
							>
								<transition-group name="fade">
									<div
										v-for="note in appeal.appeal_notes"
										:key="note.id"
										class="px-3 py-2 mb-2 shadow-sm rounded"
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
							<empty-result v-else icon="comment-alt"> No notes have been added. </empty-result>
						</b-col>
					</b-row>
				</b-container>
			</b-tab>
			<b-tab v-if="$slots.files">
				<template #title>Files</template>
				<slot name="files"></slot>
			</b-tab>
			<b-tab no-body>
				<template #title>Documents</template>

				<b-tabs
					v-if="appeal.incoming_documents && appeal.incoming_documents.length > 0"
					card
					pills
					vertical
					lazy
				>
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
						<p>Documents can be attached by the client.</p>
					</template>
				</empty-result>
			</b-tab>
		</b-tabs>
	</b-card>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

import AppealStatusLabel from "@/vendors/components/Appeals/StatusLabel.vue";
import AppealSummary from "@/vendors/components/Appeals/Summary.vue";
import AppealDefendable from "./Defendable.vue";

import AddNoteForm from "@/shared/components/AddNoteForm.vue";
import UserNote from "@/shared/components/UserNote.vue";

export default {
	name: "AppealView",
	components: {
		AppealStatusLabel,
		AppealSummary,
		AppealDefendable,
		AddNoteForm,
		UserNote,
	},
	props: {
		caseEntity: {
			required: true,
			type: Object,
			default: () => {
				return {
					id: null,
				};
			},
		},
		appeal: {
			required: true,
			type: Object,
			default: () => {
				return {
					id: null,
					defendable: null,
					appeal_status: null,
				};
			},
		},
		caseClosed: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		canComplete() {
			switch (this.appeal.appeal_status) {
				case "Assigned":
				case "Returned":
					return true;
					break;
				default:
					return false;
			}
		},
		canReturn() {
			switch (this.appeal.appeal_status) {
				case "Assigned":
				case "Completed":
					return true;
					break;
				default:
					return false;
			}
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
		...mapGetters({
			user: "user",
		}),
	},
	data() {
		return {
			addingNote: null,
			deletingNote: null,
			editingDefendable: false,
			completing: null,
			returning: null,
		};
	},
	methods: {
		refresh() {
			this.$emit("refresh");
		},
		updatedDefendable(appeal) {
			this.editingDefendable = false;
			this.$emit("saved", appeal);
			this.refresh();
		},
		/**
		 * Stages
		 */
		async completeAppeal() {
			const message =
				"Are you sure you want to mark this appeal as completed? The client will be notified, so please ensure all relevant files have been attached.";
			if (!confirm(message)) {
				return false;
			}

			try {
				this.completing = true;

				const response = await this.$store.dispatch("appeals/complete", {
					id: this.appeal.id,
				});

				this.$emit("completed", response);
				this.$emit("update:appeal", response);

				this.$bvToast.toast("Appeal completed.", {
					title: "Appeal Completed",
					autoHideDelay: 5000,
					appendToast: true,
					solid: true,
					variant: "primary",
				});
			} catch (e) {
				console.error(e);

				this.$bvToast.toast(e.response.data.message || "Failed to complete appeal.", {
					title: "Complete Failed",
					autoHideDelay: 5000,
					appendToast: true,
					solid: true,
					variant: "danger",
				});
			} finally {
				this.completing = false;
			}
		},
		async returnAppeal() {
			const message =
				"Are you sure you want to return this appeal as unable to complete? The client will be notified.";
			if (!confirm(message)) {
				return false;
			}

			try {
				this.returning = true;

				const response = await this.$store.dispatch("appeals/return", {
					id: this.appeal.id,
				});

				this.$emit("returned", response);
				this.$emit("update:appeal", response);

				this.$bvToast.toast("Appeal returned to client.", {
					title: "Appeal Returned",
					autoHideDelay: 5000,
					appendToast: true,
					solid: true,
					variant: "primary",
				});
			} catch (e) {
				console.error(e);

				this.$bvToast.toast(e.response.data.message || "Failed to return appeal.", {
					title: "Return Failed",
					autoHideDelay: 5000,
					appendToast: true,
					solid: true,
					variant: "danger",
				});
			} finally {
				this.returning = false;
			}
		},
		/**
		 * Notes
		 */
		async addNote(note) {
			try {
				this.addingNote = true;

				const response = await this.$store.dispatch("appeals/addNote", {
					id: this.appeal.id,
					notes: note.notes,
				});

				this.appeal.appeal_notes.unshift(response);

				if (this.$refs.addNoteForm) {
					this.$refs.addNoteForm.reset();
				}
			} catch (e) {
				console.error(e);

				this.$bvToast.toast(e.response.data.message || "Failed to add note.", {
					title: "Add Note Failed",
					autoHideDelay: 5000,
					appendToast: true,
					solid: true,
					variant: "danger",
				});
			} finally {
				this.addingNote = false;
				this.refresh();
			}
		},
		canDeleteNote(note) {
			if (!note || !note.id) return false;
			if (this.user.admin) return true;

			return note.created_by_user.id == this.user.id;
		},
		async deleteNote(noteId) {
			try {
				this.deletingNote = noteId;

				const response = await this.$store.dispatch("appeals/deleteNote", {
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
				console.error(e);

				this.$bvToast.toast(e.response.data.message || "Failed to delete note.", {
					title: "Delete Note Failed",
					autoHideDelay: 5000,
					appendToast: true,
					solid: true,
					variant: "danger",
				});
			} finally {
				this.deletingNote = null;
				this.refresh();
			}
		},
	},
};
</script>
