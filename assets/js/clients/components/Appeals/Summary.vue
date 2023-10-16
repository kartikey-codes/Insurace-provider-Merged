<template>
	<b-row>
		<b-col cols="12" lg="4" class="mb-2">
			<h5 class="h6 my-2 font-weight-bold text-muted text-uppercase">Details</h5>
			<b-card>
				<dl class="mb-0">
					<div class="row">
						<dt class="col-5 text-muted h6 small">Status</dt>
						<dd class="col-7">
							<appeal-status-label :value="appeal" />
						</dd>
					</div>
					<div v-if="appeal.appeal_level && appeal.appeal_level.name" class="row">
						<dt class="col-5 text-muted h6 small">Level</dt>
						<dd class="col-7">{{ appeal.appeal_level.name }}</dd>
					</div>
					<div v-if="appeal.appeal_type && appeal.appeal_type.name" class="row">
						<dt class="col-5 text-muted h6 small">Type</dt>
						<dd class="col-7">
							{{ appeal.appeal_type.name }}
						</dd>
					</div>
					<div class="row">
						<dt class="col-5 text-muted h6 small">Priority</dt>
						<dd class="col-7">
							<span v-if="appeal.priority" class="text-primary font-weight-bold">Yes</span>
							<span v-else class="text-muted">No</span>
						</dd>
					</div>
					<div class="row">
						<dt class="col-5 text-muted h6 small">Audit Reviewer</dt>
						<dd class="col-7">
							<router-link
								v-if="appeal.audit_reviewer && appeal.audit_reviewer.full_name"
								:to="{
									name: 'auditReviewers.view',
									params: {
										id: appeal.audit_reviewer_id,
									},
								}"
							>
								{{ appeal.audit_reviewer.full_name }}
							</router-link>
							<span v-else class="text-muted"> &mdash; </span>
						</dd>
					</div>
					<div class="row">
						<dt class="col-5 text-muted h6 small">Agency</dt>
						<dd class="col-7">
							<router-link
								v-if="appeal.agency && appeal.agency.name"
								:to="{
									name: 'agencies.view',
									params: {
										id: appeal.agency_id,
									},
								}"
							>
								{{ appeal.agency.name }}
							</router-link>
							<span v-else class="text-muted"> &mdash; </span>
						</dd>
					</div>
				</dl>
			</b-card>
		</b-col>
		<b-col cols="12" lg="4" class="mb-2">
			<h5 class="h6 my-2 font-weight-bold text-muted text-uppercase">Dates</h5>
			<b-card>
				<dl class="mb-0">
					<div class="row">
						<dt class="col-5 text-muted h6 small">Letter Date</dt>
						<dd class="col-7">
							{{ $filters.formatDate(appeal.letter_date) }}
						</dd>
					</div>
					<div class="row">
						<dt class="col-5 text-muted h6 small">Received</dt>
						<dd class="col-7">
							{{ $filters.formatDate(appeal.received_date) }}
						</dd>
					</div>
					<div class="row">
						<dt class="col-5 text-muted h6 small">Response Due</dt>
						<dd class="col-7">
							<div>{{ $filters.formatDate(appeal.due_date) }}</div>
							<div class="small text-muted mb-2">{{ $filters.fromNow(appeal.due_date) }}</div>
						</dd>
					</div>
					<div class="row">
						<dt class="col-5 text-muted h6 small">Completed</dt>
						<dd class="col-7">
							<div v-if="appeal.completed">
								<div>{{ $filters.formatDate(appeal.completed) }}</div>
								<div class="small-text-muted">{{ $filters.fromNow(appeal.completed) }}</div>

								<div
									v-if="appeal.completed_by_user && appeal.completed_by_user.full_name"
									class="small text-muted"
								>
									By {{ appeal.completed_by_user.full_name }}
								</div>
							</div>
							<div v-else class="text-muted">No</div>
						</dd>
					</div>
				</dl>
			</b-card>
		</b-col>
		<b-col cols="12" lg="4" class="mb-2">
			<h5 class="h6 my-2 font-weight-bold text-muted text-uppercase">Reference</h5>
			<b-list-group>
				<b-list-group-item class="d-flex justify-content-between align-items-center">
					<div class="text-muted font-weight-normal">Audit ID</div>
					<div>
						<span v-if="appeal.audit_identifier">{{ appeal.audit_identifier }}</span>
						<span v-else class="text-muted">&mdash;</span>
					</div>
				</b-list-group-item>
				<b-list-group-item
					v-for="referenceNumber in appeal.appeal_reference_numbers"
					:key="referenceNumber.id"
					class="d-flex justify-content-between align-items-center"
				>
					<div class="text-muted font-weight-normal">
						{{ referenceNumber.reference_number.name }}
					</div>
					<div>
						{{ referenceNumber.value }}
					</div>
				</b-list-group-item>
			</b-list-group>
		</b-col>
	</b-row>
</template>

<script>
import AppealStatusLabel from "@/clients/components/Appeals/StatusLabel.vue";

export default {
	name: "AppealSummary",
	components: {
		AppealStatusLabel,
	},
	props: {
		appeal: {
			type: Object,
			required: true,
			default: () => {
				return {
					id: null,
					created: null,
					created_by: null,
					modified: null,
					modified_by: null,
					case_id: null,
					appeal_type_id: null,
					appeal_level_id: null,
					defendable: null,
					letter_date: null,
					received_date: null,
					due_date: null,
					priority: null,
					assigned: null,
					assigned_to: null,
					completed: null,
					completed_by: null,
					days_to_respond: null,
					days_to_respond_from_id: null,
					appeal_status: null,
					cancelled: null,
					cancelled_by: null,
					submitted: null,
					submitted_by: null,
					closed: null,
					closed_by: null,
					unable_to_complete: null,
					audit_reviewer_id: null,
					agency_id: null,
					audit_identifier: null,
					agency: {
						id: null,
						name: null,
					},
					appeal_type: {
						id: null,
						name: null,
					},
					appeal_level: {
						id: null,
						name: null,
						short_name: null,
					},
					days_to_respond_from: null,
					created_by_user: {
						id: null,
						first_name: null,
						middle_name: null,
						last_name: null,
						email: null,
						last_seen: null,
						full_name: null,
						list_name: null,
					},
					appeal_notes: [],
					appeal_reference_numbers: [],
					utc_reasons: [],
					not_defendable_reasons: [],
					assigned_to_user: null,
					// Virtual
					is_overdue: null,
					is_finished: null,
					can_cancel: null,
					can_close: null,
					can_delete: null,
					can_reopen: null,
					can_submit: false,
					pdf_title: null,
					pdf_url: null,
				};
			},
		},
	},
	computed: {
		hasReferenceNumbers() {
			return this.appeal.appeal_reference_numbers.length > 0;
		},
		hasNotDefendableReasons() {
			return this.appeal.not_defendable_reasons.length > 0;
		},
		hasUtcReasons() {
			return this.appeal.utc_reasons.length > 0;
		},
	},
};
</script>
