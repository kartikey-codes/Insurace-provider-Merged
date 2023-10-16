<template>
	<b-row>
		<b-col cols="12" class="mb-2">
			<h5 :class="sectionHeaderClass">Case Details</h5>
			<b-card no-body class="shadow-sm">
				<b-card-body class="mb-0">
					<dl class="mb-0">
						<div class="row">
							<dt :class="dtClass">Status</dt>
							<dd :class="ddClass">
								<case-status-label :value="value" />
							</dd>
						</div>
						<div v-if="value.case_type && value.case_type.name" class="row">
							<dt :class="dtClass">Type</dt>
							<dd :class="ddClass">{{ value.case_type.name }}</dd>
						</div>
						<div v-if="value.denial_type && value.denial_type.name" class="row">
							<dt :class="dtClass">Denial Type</dt>
							<dd :class="ddClass">
								{{ value.denial_type.name }}
							</dd>
						</div>
						<div v-if="value.case_outcome && value.case_outcome.name" class="row">
							<dt :class="dtClass">Outcome</dt>
							<dd :class="ddClass">
								{{ value.case_outcome.name }}
							</dd>
						</div>
					</dl>
				</b-card-body>

				<hr v-if="hasDollarAmounts" class="my-0" />

				<b-card-body v-if="hasDollarAmounts" class="mb-0">
					<dl class="mb-0">
						<div v-if="value.total_claim_amount" class="row">
							<dt :class="dtClass">Total Claim</dt>
							<dd :class="ddClass">
								{{ $filters.currency(value.total_claim_amount) }}
							</dd>
						</div>
						<div v-if="value.disputed_amount" class="row">
							<dt :class="dtClass">Disputed</dt>
							<dd :class="ddClass">
								{{ $filters.currency(value.disputed_amount) }}
							</dd>
						</div>
						<div v-if="value.settled_amount" class="row">
							<dt :class="dtClass">Settled</dt>
							<dd :class="ddClass">
								{{ $filters.currency(value.settled_amount) }}
							</dd>
						</div>
						<div v-if="value.reimbursement_amount" class="row">
							<dt :class="dtClass">Reimbursement</dt>
							<dd :class="ddClass">
								{{ $filters.currency(value.reimbursement_amount) }}
							</dd>
						</div>
					</dl>
				</b-card-body>

				<b-list-group v-if="value.denial_reasons && value.denial_reasons.length > 0">
					<b-list-group-item v-for="denialReason in value.denial_reasons" :key="denialReason.id" class="py-2">
						<span>{{ denialReason.name }}</span>
					</b-list-group-item>
				</b-list-group>
			</b-card>
		</b-col>
		<b-col cols="12" class="mb-2">
			<h5 :class="sectionHeaderClass">Facility &amp; Visit</h5>
			<b-card no-body class="shadow-sm">
				<b-card-body class="mb-0">
					<dl class="mb-0">
						<div v-if="value.facility && value.facility.name" class="row">
							<dt :class="dtClass">Name</dt>
							<dd :class="ddClass" v-text="value.facility.name" />
						</div>
						<div
							v-if="value.facility && value.facility_type && value.facility.facility_type.name"
							class="row"
						>
							<dt :class="dtClass">Type</dt>
							<dd :class="ddClass" v-text="value.facility.facility_type.name" />
						</div>
						<div v-if="value.visit_number" class="row">
							<dt :class="dtClass">Visit ID</dt>
							<dd :class="ddClass" v-text="value.visit_number" />
						</div>
						<div v-if="value.admit_date" class="row">
							<dt :class="dtClass">Admitted</dt>
							<dd :class="ddClass">
								{{ $filters.formatDate(value.admit_date) }}
							</dd>
						</div>
						<div v-if="value.discharge_date" class="row">
							<dt :class="dtClass">Discharged</dt>
							<dd :class="ddClass">
								{{ $filters.formatDate(value.discharge_date) }}
							</dd>
						</div>
						<div v-if="value.length_of_stay" class="row">
							<dt :class="dtClass">Length of Stay</dt>
							<dd :class="ddClass">
								{{ $filters.formatNumber(value.length_of_stay) }} day{{
									value.length_of_stay !== 1 ? "s" : ""
								}}
							</dd>
						</div>
					</dl>
				</b-card-body>
			</b-card>
		</b-col>
		<b-col cols="12" class="mb-2">
			<h5 :class="sectionHeaderClass">Primary Insurance</h5>
			<b-card no-body class="shadow-sm">
				<b-card-body class="mb-0">
					<dl class="mb-0">
						<div v-if="value.insurance_provider && value.insurance_provider.name" class="row">
							<dt :class="dtClass">Name</dt>
							<dd :class="ddClass">
								{{ value.insurance_provider.name }}
							</dd>
						</div>
						<div v-if="value.insurance_type && value.insurance_type.name" class="row">
							<dt :class="dtClass">Type</dt>
							<dd :class="ddClass">
								{{ value.insurance_type.name }}
							</dd>
						</div>
						<div v-if="value.insurance_plan" class="row">
							<dt :class="dtClass">Plan</dt>
							<dd :class="ddClass">
								{{ value.insurance_plan }}
							</dd>
						</div>
						<div v-if="value.insurance_number" class="row">
							<dt :class="dtClass">Number</dt>
							<dd :class="ddClass">
								{{ value.insurance_number }}
							</dd>
						</div>
					</dl>
				</b-card-body>
			</b-card>
		</b-col>
		<b-col v-if="value.case_readmissions && value.case_readmissions.length > 0" cols="12" class="mb-2">
			<h5 :class="sectionHeaderClass">Readmissions</h5>
			<b-card no-body class="shadow-sm">
				<b-list-group flush>
					<b-list-group-item
						v-for="readmission in value.case_readmissions"
						:key="readmission.id"
						class="d-flex justify-content-between align-items-center"
					>
						<div class="d-flex align-items-start">
							<b-avatar variant="light" class="mr-4">
								<font-awesome-icon icon="calendar" fixed-width />
							</b-avatar>
							<div>
								<div>
									<span v-if="readmission.admit_date">{{
										$filters.formatDate(readmission.admit_date)
									}}</span>
									<span v-if="readmission.admit_date && readmission.discharge_date" class="text-muted"
										>&mdash;</span
									>
									<span v-if="readmission.discharge_date">{{
										$filters.formatDate(readmission.discharge_date)
									}}</span>
								</div>
								<div class="small text-muted">
									<span v-if="readmission.length_of_stay"
										>{{ readmission.length_of_stay }} day{{
											readmission.length_of_stay != 1 ? "s" : ""
										}}</span
									>
									<span v-if="readmission.length_of_stay && readmission.visit_number">&mdash;</span>
									<span v-if="readmission.visit_number">Visit #{{ readmission.visit_number }}</span>
								</div>
							</div>
						</div>
					</b-list-group-item>
				</b-list-group>
			</b-card>
		</b-col>
	</b-row>
</template>

<script type="text/javascript">
import CaseStatusLabel from "./StatusLabel.vue";

export default {
	name: "CaseSummary",
	components: {
		CaseStatusLabel,
	},
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					id: null,
					disputed_amount: null,
					reimbursement_amount: null,
					settled_amount: null,
					total_claim_amount: null,
					visit_number: null,
					admit_date: null,
					discharge_date: null,
					length_of_stay: null,
					insurance_plan: null,
					insurance_number: null,
					case_type: {
						name: null,
					},
					case_outcome: {
						name: null,
					},
					denial_type: {
						name: null,
					},
					facility: {
						name: null,
						facility_type: {
							name: null,
						},
					},
					denial_reasons: [],
				};
			},
		},
		dtClass: {
			type: String,
			default: "col-5 text-muted h6 small",
		},
		ddClass: {
			type: String,
			default: "col-7",
		},
		sectionHeaderClass: {
			type: String,
			default: "h6 my-2 font-weight-bold text-muted text-uppercase",
		},
	},
	computed: {
		hasDollarAmounts() {
			return this.value.total_claim_amount ||
				this.value.disputed_amount ||
				this.value.settled_amount ||
				this.value.reimbursement_amount
				? true
				: false;
		},
	},
};
</script>
