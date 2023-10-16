<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'cases' }">Cases /</router-link>
				<span span v-if="loading && !caseEntity.patient?.list_name">
					<font-awesome-icon icon="circle-notch" spin fixed-width />
				</span>
				<span v-else>
					{{ caseEntity.patient?.list_name ?? "View Case" }}
				</span>
			</template>
			<template #buttons>
				<b-button
					variant="primary"
					v-if="!caseClosed"
					v-b-modal.closeCaseModal
					:disabled="showLoading"
					title="Close this case when all opportunities have concluded"
				>
					<font-awesome-icon icon="lock" fixed-width />
					<span>Close Case</span>
				</b-button>
				<b-button
					variant="primary"
					v-else
					@click="reopenCase"
					:disabled="showLoading || reopening"
					title="Reopen this case for further progression"
				>
					<font-awesome-icon v-if="reopening" icon="sync" :spin="reopening" fixed-width />
					<font-awesome-icon v-else icon="unlock" fixed-width />
					<span>Reopen Case</span>
				</b-button>

				<b-dropdown variant="secondary" right title="More Options">
					<template #button-content>
						<font-awesome-icon icon="cog" :spin="loading" fixed-width />
					</template>

					<b-dropdown-item
						:to="{ name: 'cases.edit', params: { id: $route.params.id } }"
						title="Edit case details"
					>
						<font-awesome-icon icon="edit" fixed-width />
						<span>Edit Case</span>
					</b-dropdown-item>

					<b-dropdown-item @click="refresh" :disabled="loading" title="Refresh case details">
						<font-awesome-icon icon="sync" :spin="loading" fixed-width />
						<span>Refresh</span>
					</b-dropdown-item>

					<b-dropdown-divider />

					<b-dropdown-item
						v-if="caseEntity.patient && caseEntity.patient.id"
						:to="{
							name: 'patients.view',
							params: { id: caseEntity.patient.id },
						}"
						title="View patient details"
					>
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<div>View Patient</div>
								<small class="text-muted d-block">
									{{ $filters.truncatedString(caseEntity.patient.full_name, 20) }}
								</small>
							</div>
							<font-awesome-icon icon="chevron-right" class="my-0 py-0" />
						</div>
					</b-dropdown-item>

					<b-dropdown-item
						v-if="caseEntity.patient && caseEntity.patient.id"
						:to="{
							name: 'patients.edit',
							params: { id: caseEntity.patient.id },
						}"
						title="Edit patient details"
					>
						<font-awesome-icon icon="edit" fixed-width />
						<span>Edit Patient</span>
					</b-dropdown-item>

					<b-dropdown-divider />

					<b-dropdown-item
						v-if="caseEntity.facility && caseEntity.facility.id"
						:to="{
							name: 'facilities.view',
							params: { id: caseEntity.facility.id },
						}"
						title="View facility details"
					>
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<div>View Facility</div>
								<small class="text-muted d-block">
									{{ $filters.truncatedString(caseEntity.facility.name, 20) }}
								</small>
							</div>
							<font-awesome-icon icon="chevron-right" class="my-0 py-0" />
						</div>
					</b-dropdown-item>

					<b-dropdown-item
						v-if="caseEntity.insurance_provider && caseEntity.insurance_provider.id"
						:to="{
							name: 'insuranceProviders.view',
							params: { id: caseEntity.insurance_provider.id },
						}"
						title="View insurance provider details"
					>
						<div class="d-flex justify-content-between align-items-center">
							<div class="mr-3">
								<div>View Insurance Provider</div>
								<small class="text-muted d-block">
									{{ $filters.truncatedString(caseEntity.insurance_provider.name, 20) }}
								</small>
							</div>
							<font-awesome-icon icon="chevron-right" class="my-0 py-0" />
						</div>
					</b-dropdown-item>

					<b-dropdown-divider />

					<b-dropdown-item
						@click="deleteEntity"
						:disabled="showLoading"
						variant="danger"
						title="Delete this case"
					>
						<font-awesome-icon icon="trash" fixed-width />
						<span>Delete Case</span>
					</b-dropdown-item>
				</b-dropdown>
			</template>
		</page-header>

		<loading-indicator v-if="showLoading" class="my-5" />
		<b-container v-else-if="error" fluid>
			<error-alert class="my-5">
				<span>{{ error }}</span>
				<template #content>
					<div class="my-4">
						<b-button variant="primary" :to="{ name: 'cases' }">
							<font-awesome-icon icon="list" fixed-width />
							<span>All Cases</span>
						</b-button>

						<b-button variant="secondary" @click="refresh" :disabled="loading">
							<font-awesome-icon icon="sync" :spin="loading" fixed-width />
							<span>Retry</span>
						</b-button>
					</div>
				</template>
			</error-alert>
		</b-container>
		<b-card v-else no-body>
			<b-card-header header-tag="nav">
				<b-row>
					<b-col cols="12" md="6" class="mb-2">
						<case-activity :case-id="$route.params.id" />
					</b-col>
					<b-col cols="12" md="6" class="text-left text-md-right mb-2">
						<div v-if="caseEntity.modified" class="small text-muted">
							<span v-if="caseEntity.modified" :title="$filters.formatTimestamp(caseEntity.modified)">
								Last updated {{ $filters.fromNow(caseEntity.modified) }}
							</span>
							<span v-if="caseEntity.modified_by_user && caseEntity.modified_by_user.full_name">
								by
								<router-link
									:to="{ name: 'users.view', params: { id: caseEntity.modified_by } }"
									title="View User"
								>
									{{ caseEntity.modified_by_user.full_name }}
								</router-link>
							</span>
						</div>
					</b-col>
				</b-row>

				<b-nav card-header tabs>
					<b-nav-item
						title="Case Details"
						exact
						active-class="active font-weight-bold"
						link-classes="px-3"
						:to="{ name: 'cases.view', params: { id: $route.params.id } }"
					>
						<case-status-label :value="caseEntity" class="mb-0" />
						<b-badge v-if="caseEntity.unable_to_complete" pill variant="warning" title="Unable To Complete">
							<font-awesome-icon icon="exclamation-triangle" class="mx-0 px-0" />
						</b-badge>
						<span>Case</span>
					</b-nav-item>

					<b-nav-item
						v-for="caseRequest in caseEntity.case_requests"
						:key="'request_' + caseRequest.id"
						:to="{
							name: 'caseRequests.view',
							params: { id: caseEntity.id, case_request_id: caseRequest.id },
						}"
						:title="caseRequest.name ? caseRequest.name : '(Missing Name)'"
						active-class="active font-weight-bold"
					>
						<case-request-status-label icon :value="caseRequest" class="d-none d-lg-inline mr-2" />
						<span v-if="!caseRequest.type_label">Request</span>
						<span v-else>{{ caseRequest.type_label }}</span>
					</b-nav-item>

					<b-nav-item
						v-for="appeal in caseEntity.appeals"
						:key="'appeal_' + appeal.id"
						:to="{ name: 'appeals.view', params: { id: caseEntity.id, appeal_id: appeal.id } }"
						:title="
							appeal.appeal_level && appeal.appeal_level.short_name
								? appeal.appeal_level.short_name
								: '(Missing Level)'
						"
						active-class="active font-weight-bold"
					>
						<appeal-status-label icon :value="appeal" class="d-none d-lg-inline mr-2" />
						<span
							v-if="appeal.appeal_level && appeal.appeal_level.short_name"
							v-text="appeal.appeal_level.short_name"
						/>
						<span v-else class="text-danger">(Missing Level)</span>
					</b-nav-item>
				</b-nav>
			</b-card-header>

			<router-view
				ref="routerView"
				:key="routerKey"
				:case-entity="caseEntity"
				:value="currentAppeal"
				@added="addedAppeal"
				@deleted="deletedAppeal"
				@updated="updatedAppeal"
				@cancelled="refresh"
				@reopened="refresh"
				@added-request="addedRequest"
				@updated-request="updatedRequest"
				@deleted-request="deletedRequest"
			></router-view>
		</b-card>

		<close-case-modal
			v-if="caseEntity.id"
			id="closeCaseModal"
			title="Close Case"
			:case-entity="caseEntity"
			@closed="closedCase"
		/>
	</div>
</template>

<script type="text/javascript">
import AppealStatusLabel from "@/clients/components/Appeals/StatusLabel.vue";
import CaseActivity from "@/clients/components/Cases/Activity.vue";
import CloseCaseModal from "@/clients/components/Cases/CloseCaseModal.vue";
import CaseRequestStatusLabel from "@/clients/components/CaseRequests/StatusLabel.vue";
import CaseStatusLabel from "@/clients/components/Cases/StatusLabel.vue";

export default {
	name: "CaseView",
	components: {
		AppealStatusLabel,
		CaseActivity,
		CloseCaseModal,
		CaseRequestStatusLabel,
		CaseStatusLabel,
	},
	data() {
		return {
			loading: true,
			reopening: false,
			error: false,
			caseEntity: {
				id: null,
				created: null,
				modified: null,
				deleted: null,
				patient_id: null,
				facility_id: null,
				denial_type_id: null,
				case_outcome_id: null,
				insurance_provider_id: null,
				insurance_type_id: null,
				insurance_plan: null,
				insurance_number: null,
				total_claim_amount: null,
				reimbursement_amount: null,
				disputed_amount: null,
				settled_amount: null,
				visit_number: null,
				admit_date: null,
				discharge_date: null,
				closed: null,
				closed_by: null,
				assigned: null,
				assigned_to: null,
				client_employee_id: null,
				unable_to_complete: null,
				discipline_id: null,
				// Virtual fields
				can_add_appeal: null,
				// Associations
				patient: {
					id: null,
					full_name: "",
					list_name: "",
				},
				appeals: [],
				disciplines: [],
				case_requests: [],
			},
			timer: null,
		};
	},
	computed: {
		routerKey() {
			if (this.$route.params.appeal_id) {
				return "appeal_" + this.$route.params.appeal_id;
			}

			if (this.$route.params.case_request_id) {
				return "request_" + this.$route.params.case_request_id;
			}

			return "case";
		},
		currentAppeal() {
			return this.caseEntity.appeals.find((appeal) => appeal.id == this.$route.params.appeal_id);
		},
		hasAppeals() {
			if (!this.caseEntity || !this.caseEntity.appeals) {
				return false;
			}

			return this.caseEntity.appeals.length && this.caseEntity.appeals.length > 0;
		},
		appealCount() {
			if (!this.hasAppeals) {
				return 0;
			}

			return this.caseEntity.appeals.length;
		},
		showLoading() {
			if (this.caseEntity.id) {
				return false;
			}

			return this.loading;
		},
		caseClosed() {
			return this.caseEntity.closed && this.caseEntity.closed !== null;
		},
	},
	mounted() {
		this.refresh();

		//this.timer = setInterval(this.refresh, 30000);
	},
	methods: {
		/**
		 * Get Details
		 */
		async refresh() {
			try {
				this.loading = true;
				this.error = false;

				const response = await this.$store.dispatch("cases/get", {
					id: this.$route.params.id,
				});

				this.caseEntity = response;
			} catch (e) {
				this.error = e.response.data.message || "Unable to load case details";

				this.$store.dispatch("apiError", {
					error: e,
					message: "Failed getting case details",
				});
			} finally {
				this.loading = false;
			}
		},
		/**
		 * Delete
		 */
		async deleteEntity() {
			const confirmMessage = "Are you sure you want to delete this case?";
			if (!confirm(confirmMessage)) {
				return false;
			}

			try {
				this.deleting = true;

				const response = await this.$store.dispatch("cases/delete", {
					id: this.$route.params.id,
				});

				this.$router.push({
					name: "cases",
				});

				this.$nextTick(function () {
					this.$store.dispatch("notify", {
						variant: "primary",
						title: "Case Deleted",
						message: `Case for patient ${response.data.patient.list_name} was deleted.`,
					});
				});
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Delete Failed",
					message: "Failed to delete case.",
				});
			} finally {
				this.deleting = false;
			}
		},
		/**
		 * Reopen
		 */
		async reopenCase(e) {
			const confirmMessage = "Are you sure you want to reopen this case? The case outcome will be removed.";
			if (!confirm(confirmMessage)) {
				return false;
			}

			try {
				this.reopening = true;

				const response = await this.$store.dispatch("cases/open", {
					id: this.$route.params.id,
				});

				this.caseEntity = response;

				this.refresh();
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Reopen Failed",
					message: "Failed to reopen case",
				});
			} finally {
				this.reopening = false;
			}
		},
		closedCase(caseEntity) {
			this.caseEntity = caseEntity;
		},
		addedAppeal(appeal) {
			this.caseEntity.appeals.push(appeal);
		},
		updatedAppeal(appeal) {
			this.refresh();
		},
		deletedAppeal(appeal) {
			this.refresh();
		},
		addedRequest(request) {
			this.caseEntity.case_requests.push(request);
		},
		updatedRequest(request) {
			this.$refs.routerView.refresh();
			this.refresh();
		},
		deletedRequest(request) {
			this.refresh();
		},
	},
	destroyed() {
		clearInterval(this.timer);
	},
};
</script>
