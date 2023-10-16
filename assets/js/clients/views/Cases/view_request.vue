<template>
	<div>
		<b-container v-if="loading && !entity.id" fluid class="my-4">
			<loading-indicator class="my-5" />
		</b-container>
		<b-container v-else-if="error" fluid class="my-4">
			<error-alert> Error </error-alert>
		</b-container>
		<div v-else-if="editing">
			<case-request-form
				flush
				:id="entity.id"
				:case-entity="caseEntity"
				@cancel="editing = false"
				@saved="updatedRequest"
			/>
		</div>
		<b-container v-else fluid class="my-4">
			<b-row>
				<b-col cols="12">
					<b-row>
						<b-col cols="12" sm="6" md="8" lg="6" xl="3" class="text-left mb-4">
							<case-request-assign :case-request="entity" />
						</b-col>
						<b-col cols="12" sm="6" md="4" lg="6" xl="9" class="text-left text-sm-right mb-4">
							<b-button
								v-if="!entity.completed"
								@click="close"
								:disabled="deleting || updating || entity.unable_to_complete"
								variant="primary"
							>
								<font-awesome-icon icon="check-circle" fixed-width />
								Complete Request
							</b-button>
							<b-button v-else @click="reopen" :disabled="deleting || updating" variant="secondary">
								<font-awesome-icon icon="envelope-open" fixed-width />
								Reopen Request
							</b-button>

							<b-dropdown variant="secondary" right title="Request Options">
								<template #button-content>
									<font-awesome-icon icon="cog" fixed-width />
								</template>
								<b-dropdown-item @click="editing = !editing" :disabled="deleting">
									<font-awesome-icon icon="edit" fixed-width />
									Edit Request
								</b-dropdown-item>

								<b-dropdown-item @click="setUTC" :disabled="deleting || updating || entity.completed">
									<font-awesome-icon icon="exclamation-triangle" fixed-width />
									<span v-if="entity.unable_to_complete">Remove Unable To Complete</span>
									<span v-else>Mark Unable To Complete</span>
								</b-dropdown-item>

								<b-dropdown-divider />
								<b-dropdown-item @click="destroy" :disabled="deleting" variant="danger">
									<font-awesome-icon icon="trash" fixed-width />
									Delete Request
								</b-dropdown-item>
							</b-dropdown>
						</b-col>
					</b-row>
					<b-row>
						<b-col cols="12" xl="6" order="2" order-xl="1" class="mb-4">
							<h5 class="h6 my-2 font-weight-bold text-muted text-uppercase">Case Documents</h5>
							<case-files :id="caseEntity.id" :flush="false" />
						</b-col>
						<b-col cols="12" xl="6" order="1" order-xl="2" class="mb-4">
							<b-row>
								<b-col cols="12" lg="6" class="mb-2">
									<b-card>
										<dl class="mb-0">
											<div class="row">
												<dt class="col-5 text-muted h6 small">Name</dt>
												<dd class="col-7">
													{{ entity.name }}
												</dd>
											</div>

											<div class="row">
												<dt class="col-5 text-muted h6 small">Status</dt>
												<dd class="col-7">
													<case-request-status-label :value="entity" />
												</dd>
											</div>
											<div class="row">
												<dt class="col-5 text-muted h6 small">Type</dt>
												<dd class="col-7">
													{{ entity.type_label }}
												</dd>
											</div>
											<div class="row">
												<dt class="col-5 text-muted h6 small">Insurance Provider</dt>
												<dd class="col-7">
													<router-link
														v-if="entity.insurance_provider"
														:to="{
															name: 'insuranceProviders.view',
															params: { id: entity.insurance_provider_id },
														}"
													>
														{{ entity.insurance_provider.name }}
													</router-link>
													<div v-else class="text-muted">&mdash;</div>
												</dd>
											</div>
											<div class="row">
												<dt class="col-5 text-muted h6 small">Agency</dt>
												<dd class="col-7">
													<router-link
														v-if="entity.agency"
														:to="{
															name: 'agencies.view',
															params: { id: entity.agency_id },
														}"
													>
														{{ entity.agency.name }}
													</router-link>
													<div v-else class="text-muted">&mdash;</div>
												</dd>
											</div>
											<div class="row">
												<dt class="col-5 text-muted h6 small">Priority</dt>
												<dd class="col-7">
													<div v-if="entity.priority" class="text-primary font-weight-bold">
														Yes
													</div>
													<div v-else class="text-muted">No</div>
												</dd>
											</div>
										</dl>
									</b-card>
								</b-col>
								<b-col cols="12" lg="6" class="mb-2">
									<b-card>
										<dl class="mb-0">
											<div class="row">
												<dt class="col-5 text-muted h6 small">Created</dt>
												<dd class="col-7">
													<p :title="$filters.formatTimestamp(entity.created)" class="mb-0">
														{{ $filters.fromNow(entity.created) }}
													</p>
													<p
														v-if="entity.created_by && entity.created_by_user"
														class="small text-muted mb-0"
													>
														by {{ entity.created_by_user.full_name }}
													</p>
												</dd>
											</div>
											<div class="row">
												<dt class="col-5 text-muted h6 small">Last Updated</dt>
												<dd class="col-7">
													<p :title="$filters.formatTimestamp(entity.modified)" class="mb-0">
														{{ $filters.fromNow(entity.modified) }}
													</p>
													<p
														v-if="entity.modified_by && entity.modified_by_user"
														class="small text-muted mb-0"
													>
														by {{ entity.modified_by_user.full_name }}
													</p>
												</dd>
											</div>
											<div class="row">
												<dt class="col-5 text-muted h6 small">Due</dt>
												<dd class="col-7">
													<span v-if="entity.due_date">
														{{ $filters.fromNow(entity.due_date) }} on
														{{ $filters.formatDate(entity.due_date) }}
													</span>
													<span v-else class="text-muted"> &mdash; </span>
												</dd>
											</div>
											<div class="row">
												<dt class="col-5 text-muted h6 small" title="Unable To Complete">
													UTC
												</dt>
												<dd class="col-7">
													<div v-if="entity.unable_to_complete" class="text-warning">
														Unable To Complete
													</div>
													<div v-else class="text-muted">No</div>
												</dd>
											</div>
											<div class="row">
												<dt class="col-5 text-muted h6 small">Completed</dt>
												<dd class="col-7">
													<span v-if="entity.completed" class="text-success">
														Completed {{ $filters.fromNow(entity.completed_at) }}
														<span v-if="entity.completed_by && entity.completed_by_user">
															by {{ entity.completed_by_user.full_name }}
														</span>
													</span>
													<span v-else class="text-muted"> &mdash; </span>
												</dd>
											</div>
										</dl>
									</b-card>
								</b-col>
							</b-row>
						</b-col>
					</b-row>
				</b-col>
			</b-row>
		</b-container>
	</div>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import CaseFiles from "@/clients/components/Cases/Files.vue";
import CaseRequestAssign from "@/clients/components/CaseRequests/Assign.vue";
import CaseRequestForm from "@/clients/components/CaseRequests/Form.vue";
import CaseRequestStatusLabel from "@/clients/components/CaseRequests/StatusLabel.vue";

export default {
	name: "ViewCaseRequest",
	components: {
		CaseFiles,
		CaseRequestAssign,
		CaseRequestForm,
		CaseRequestStatusLabel,
	},
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					id: null,
					client_id: null,
					created: null,
					created_by: null,
					modified: null,
					modified_by: null,
					deleted: null,
					deleted_by: null,
					case_id: null,
					request_type: null,
					name: null,
					unable_to_complete: null,
					due_date: null,
					completed: null,
					completed_at: null,
					completed_by: null,
					assigned: null,
					assigned_to: null,
					assigned_to_user: null,
					insurance_provider_id: null,
					insurance_provider: null,
					agency_id: null,
					agency: null,
					// Virtual
					days_due_left: null,
					status_label: "",
					type_label: "",
				};
			},
		},
		caseEntity: {
			type: Object,
			default: () => {
				return {
					id: null,
					patient: {
						id: null,
						full_name: null,
					},
					closed: null,
					insurance_provider: null,
					insurance_type: null,
				};
			},
		},
	},
	computed: {
		...mapGetters({
			// None
		}),
	},
	data() {
		return {
			entity: this.value,
			loading: true,
			error: false,
			editing: false,
			deleting: false,
			updating: false,
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

				const response = await this.$store.dispatch("caseRequests/get", {
					case_id: this.$route.params.id,
					id: this.$route.params.case_request_id,
				});

				this.entity = response;
			} catch (e) {
				this.error = true;
				this.$store.dispatch("apiError", {
					error: e,
					message: "Failed to find request",
				});
			} finally {
				this.loading = false;
			}
		},
		/**
		 * Edit
		 */
		async updatedRequest(entity) {
			this.$emit("updated-request", entity);
			this.editing = false;
		},
		/**
		 * Delete
		 */
		async destroy() {
			if (!confirm("Are you sure you want to delete this request?")) {
				return false;
			}

			try {
				this.deleting = true;

				const response = await this.$store.dispatch("caseRequests/delete", {
					case_id: this.$route.params.id,
					id: this.$route.params.case_request_id,
				});

				this.$emit("deleted-request", response);

				this.$store.dispatch("notify", {
					variant: "success",
					title: "Request deleted",
					message: "This case request was deleted",
					data: response,
				});

				this.$router.push({
					name: "cases.view",
					params: {
						id: this.$route.params.id,
					},
				});
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Failed to delete request",
				});
			} finally {
				this.deleting = false;
			}
		},
		/**
		 * Complete
		 */
		async close() {
			if (!confirm("Are you sure you want to mark this request as completed?")) {
				return false;
			}

			try {
				this.updating = true;

				const response = await this.$store.dispatch("caseRequests/close", {
					case_id: this.$route.params.id,
					id: this.$route.params.case_request_id,
				});

				this.$emit("updated-request", response);

				this.$store.dispatch("notify", {
					variant: "success",
					title: "Request Completed",
					message: "This case request was completed",
					data: response,
				});
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Failed to complete request",
				});
			} finally {
				this.updating = false;
			}
		},
		/**
		 * Reopen
		 */
		async reopen() {
			if (!confirm("Are you sure you want to reopen this request?")) {
				return false;
			}

			try {
				this.updating = true;

				const response = await this.$store.dispatch("caseRequests/reopen", {
					case_id: this.$route.params.id,
					id: this.$route.params.case_request_id,
				});

				this.$emit("updated-request", response);

				this.$store.dispatch("notify", {
					variant: "primary",
					title: "Request Reopened",
					message: "This case request was reopened",
					data: response,
				});
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Failed to reopen request",
				});
			} finally {
				this.updating = false;
			}
		},
		/**
		 * Unable To Complete
		 */
		async setUTC() {
			try {
				this.updating = true;
				const newValue = !this.entity.unable_to_complete;

				const response = await this.$store.dispatch("caseRequests/setUtc", {
					case_id: this.$route.params.id,
					id: this.$route.params.case_request_id,
					unable_to_complete: newValue,
				});

				this.$emit("updated-request", response);

				this.$store.dispatch("notify", {
					variant: "primary",
					title: "Request Marked UTC",
					message: "Case request UTC status updated",
					data: response,
				});
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Failed to update request UTC status",
				});
			} finally {
				this.updating = false;
			}
		},
	},
};
</script>
