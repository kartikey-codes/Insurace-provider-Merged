<template>
	<b-container fluid class="mb-4">
		<b-row class="my-4">
			<b-col cols="4" xl="8" class="text-left">
				<b-button @click="showDetails = !showDetails" variant="secondary">
					<span v-if="showDetails">Hide Details</span>
					<span v-else>Show Details</span>
				</b-button>
			</b-col>
			<b-col cols="8" xl="4" class="text-right">
				<case-assign :case-entity="caseEntity" />
			</b-col>
		</b-row>
		<b-collapse v-model="showDetails">
			<b-row>
				<b-col cols="12" lg="4" class="mb-2">
					<h5 class="h6 my-2 font-weight-bold text-muted text-uppercase">Case Details</h5>
					<b-card>
						<dl class="mb-0">
							<div class="row">
								<dt class="col-5 text-muted h6 small">Status</dt>
								<dd class="col-7">
									<case-status-label :value="caseEntity" />
									<b-badge
										v-if="caseEntity.unable_to_complete"
										pill
										variant="warning"
										title="Unable To Complete"
									>
										<font-awesome-icon icon="exclamation-triangle" class="mx-0 px-0" />
										UTC
									</b-badge>
								</dd>
							</div>
							<div v-if="caseEntity.patient && caseEntity.patient.full_name" class="row">
								<dt class="col-5 text-muted h6 small">Patient</dt>
								<dd class="col-7">
									<router-link
										:to="{ name: 'patients.view', params: { id: caseEntity.patient_id } }"
										title="View Patient"
									>
										{{ caseEntity.patient.full_name }}
									</router-link>
								</dd>
							</div>
							<div v-if="caseEntity.patient && caseEntity.patient.date_of_birth" class="row">
								<dt class="col-5 text-muted h6 small">Patient DOB</dt>
								<dd class="col-7">
									<span>{{ $filters.formatDate(caseEntity.patient.date_of_birth) }}</span>
									<span v-if="caseEntity.patient.age">
										&mdash;
										{{ caseEntity.patient.age }}
									</span>
								</dd>
							</div>

							<div v-if="caseEntity.case_type && caseEntity.case_type.name" class="row">
								<dt class="col-5 text-muted h6 small">Type</dt>
								<dd class="col-7">{{ caseEntity.case_type.name }}</dd>
							</div>
							<div v-if="caseEntity.denial_type && caseEntity.denial_type.name" class="row">
								<dt class="col-5 text-muted h6 small">Denial Type</dt>
								<dd class="col-7">
									{{ caseEntity.denial_type.name }}
								</dd>
							</div>
							<div v-if="caseEntity.client_employee && caseEntity.client_employee.full_name" class="row">
								<dt class="col-5 text-muted h6 small">Primary Physician</dt>
								<dd class="col-7">
									<router-link
										:to="{
											name: 'clientEmployees.view',
											params: { id: caseEntity.client_employee.id },
										}"
										title="View Physician"
									>
										{{ caseEntity.client_employee.full_name }}
									</router-link>
								</dd>
							</div>
							<div v-if="caseEntity.case_outcome && caseEntity.case_outcome.name" class="row">
								<dt class="col-5 text-muted h6 small">Outcome</dt>
								<dd class="col-7">
									{{ caseEntity.case_outcome.name }}
								</dd>
							</div>
							<div v-if="caseEntity.total_claim_amount" class="row">
								<dt class="col-5 text-muted h6 small">Total Claim</dt>
								<dd class="col-7">
									{{ $filters.currency(caseEntity.total_claim_amount) }}
								</dd>
							</div>
							<div v-if="caseEntity.disputed_amount" class="row">
								<dt class="col-5 text-muted h6 small">Disputed</dt>
								<dd class="col-7">
									{{ $filters.currency(caseEntity.disputed_amount) }}
								</dd>
							</div>
							<div v-if="caseEntity.settled_amount" class="row">
								<dt class="col-5 text-muted h6 small">Settled</dt>
								<dd class="col-7">
									{{ $filters.currency(caseEntity.settled_amount) }}
								</dd>
							</div>
							<div v-if="caseEntity.reimbursement_amount" class="row">
								<dt class="col-5 text-muted h6 small">Reimbursement</dt>
								<dd class="col-7">
									{{ $filters.currency(caseEntity.reimbursement_amount) }}
								</dd>
							</div>
						</dl>
					</b-card>
				</b-col>
				<b-col cols="12" lg="4" class="mb-2">
					<h5 class="h6 my-2 font-weight-bold text-muted text-uppercase">Facility &amp; Visit</h5>
					<b-card>
						<dl class="mb-0">
							<div class="row">
								<dt class="col-5 text-muted h6 small">Name</dt>
								<dd class="col-7">
									<div v-if="caseEntity.facility && caseEntity.facility.name">
										<router-link
											:to="{
												name: 'facilities.view',
												params: { id: caseEntity.facility.id },
											}"
										>
											{{ caseEntity.facility.name }}
										</router-link>
									</div>
									<div v-else class="text-muted">&mdash;</div>
								</dd>
							</div>
							<div
								v-if="
									caseEntity.facility &&
									caseEntity.facility_type &&
									caseEntity.facility.facility_type.name
								"
								class="row"
							>
								<dt class="col-5 text-muted h6 small">Type</dt>
								<dd class="col-7" v-text="caseEntity.facility.facility_type.name" />
							</div>
							<div v-if="caseEntity.visit_number" class="row">
								<dt class="col-5 text-muted h6 small">Visit ID</dt>
								<dd class="col-7" v-text="caseEntity.visit_number" />
							</div>
							<div v-if="caseEntity.admit_date" class="row">
								<dt class="col-5 text-muted h6 small">Admitted</dt>
								<dd class="col-7">
									<p class="mb-0">
										{{ $filters.formatDate(caseEntity.admit_date) }}
									</p>
									<p class="mb-0 small text-muted">
										{{ $filters.fromNow(caseEntity.admit_date) }}
									</p>
								</dd>
							</div>
							<div v-if="caseEntity.discharge_date" class="row">
								<dt class="col-5 text-muted h6 small">Discharged</dt>
								<dd class="col-7">
									<p class="mb-0">
										{{ $filters.formatDate(caseEntity.discharge_date) }}
									</p>
									<p class="mb-0 small text-muted">
										{{ $filters.fromNow(caseEntity.discharge_date) }}
									</p>
								</dd>
							</div>
							<div v-if="caseEntity.length_of_stay" class="row">
								<dt class="col-5 text-muted h6 small">Length of Stay</dt>
								<dd class="col-7">
									{{ $filters.formatNumber(caseEntity.length_of_stay) }} day{{
										caseEntity.length_of_stay !== 1 ? "s" : ""
									}}
								</dd>
							</div>
							<div v-if="hasReadmissions" class="row">
								<dt class="col-5 text-muted h6 small">Readmissions</dt>
								<dd class="col-7">
									<b-list-group class="mb-0">
										<b-list-group-item
											v-for="readmission in caseEntity.case_readmissions"
											:key="readmission.id"
											class="d-flex justify-content-between align-items-center"
										>
											<div class="d-flex align-items-start">
												<b-avatar variant="light" class="mr-2" size="sm">
													<font-awesome-icon icon="calendar" fixed-width />
												</b-avatar>
												<div>
													<div>
														<span v-if="readmission.admit_date">
															{{ $filters.formatDate(readmission.admit_date) }}
														</span>
														<span
															v-if="readmission.admit_date && readmission.discharge_date"
															class="text-muted"
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
														<span
															v-if="
																readmission.length_of_stay && readmission.visit_number
															"
															>&mdash;</span
														>
														<span v-if="readmission.visit_number"
															>Visit #{{ readmission.visit_number }}</span
														>
													</div>
												</div>
											</div>
										</b-list-group-item>
									</b-list-group>
								</dd>
							</div>
						</dl>
					</b-card>
				</b-col>
				<b-col cols="12" lg="4" class="mb-2">
					<h5 class="h6 my-2 font-weight-bold text-muted text-uppercase">Primary Insurance</h5>
					<b-card class="mb-4">
						<dl class="mb-0">
							<div v-if="caseEntity.insurance_provider && caseEntity.insurance_provider.name" class="row">
								<dt class="col-5 text-muted h6 small">Name</dt>
								<dd class="col-7">
									<router-link
										:to="{
											name: 'insuranceProviders.view',
											params: { id: caseEntity.insurance_provider.id },
										}"
									>
										{{ caseEntity.insurance_provider.name }}
									</router-link>
								</dd>
							</div>
							<div class="row">
								<dt class="col-5 text-muted h6 small">Type</dt>
								<dd class="col-7">
									<span v-if="caseEntity.insurance_type && caseEntity.insurance_type.name">
										{{ caseEntity.insurance_type.name }}
									</span>
									<span v-else class="text-danger"> Missing </span>
								</dd>
							</div>
							<div v-if="caseEntity.insurance_plan" class="row">
								<dt class="col-5 text-muted h6 small">Plan</dt>
								<dd class="col-7">
									{{ caseEntity.insurance_plan }}
								</dd>
							</div>
							<div v-if="caseEntity.insurance_number" class="row">
								<dt class="col-5 text-muted h6 small">Number</dt>
								<dd class="col-7">
									{{ caseEntity.insurance_number }}
								</dd>
							</div>
						</dl>
					</b-card>

					<div v-if="caseEntity.denial_reasons.length > 0" cols="12" lg="4" class="mb-2">
						<h5 class="h6 my-2 font-weight-bold text-muted text-uppercase">Denial Reasons</h5>

						<b-list-group>
							<b-list-group-item
								v-for="denialReason in caseEntity.denial_reasons"
								:key="denialReason.id"
								class="py-2"
							>
								<span>{{ denialReason.name }}</span>
							</b-list-group-item>
						</b-list-group>
					</div>
					<div v-if="caseEntity.disciplines.length > 0" cols="12" lg="4" class="mb-2">
						<h5 class="h6 my-2 font-weight-bold text-muted text-uppercase">Disciplines</h5>

						<b-list-group>
							<b-list-group-item
								v-for="discipline in caseEntity.disciplines"
								:key="discipline.id"
								class="py-2"
							>
								<span>{{ discipline.name }}</span>
							</b-list-group-item>
						</b-list-group>
					</div>
				</b-col>
			</b-row>
		</b-collapse>
		<b-row>
			<b-col cols="12" lg="6" xl="6">
				<h5 class="h6 my-2 font-weight-bold text-muted text-uppercase">Case Documents</h5>
				<case-files :id="caseEntity.id" :flush="false" />
			</b-col>
			<b-col cols="12" lg="6" xl="6">
				<b-alert v-if="caseClosed" show variant="warning" class="my-4 p-4">
					<h6 class="h6 mb-2">
						<font-awesome-icon icon="lock" fixed-width class="mr-1 mr-lg-2" />
						<strong>This case has been closed.</strong>
					</h6>
					<p class="small mb-0">
						<span>Appeal and requests cannot be added or changed unless this case is reopened.</span>
					</p>
				</b-alert>
				<div v-else>
					<h5 class="h6 my-2 font-weight-bold text-muted text-uppercase">Add New...</h5>
					<b-card no-body>
						<b-tabs card active-nav-item-class="font-weight-bold">
							<b-tab title="Appeal" no-body>
								<appeal-form disable-cancel flush :case-entity="caseEntity" @saved="createdAppeal" />
							</b-tab>
							<b-tab title="Request" no-body>
								<case-request-form
									disable-cancel
									flush
									:case-entity="caseEntity"
									@saved="createdRequest"
								/>
							</b-tab>
						</b-tabs>
					</b-card>
				</div>
			</b-col>
		</b-row>
	</b-container>
</template>

<script>
import AppealForm from "@/clients/components/Appeals/Form.vue";
import CaseAssign from "@/clients/components/Cases/Assign.vue";
import CaseStatusLabel from "@/clients/components/Cases/StatusLabel.vue";
import CaseFiles from "@/clients/components/Cases/Files.vue";
import CaseRequestForm from "@/clients/components/CaseRequests/Form.vue";

export default {
	name: "ViewCasesViewHome",
	components: {
		AppealForm,
		CaseAssign,
		CaseFiles,
		CaseStatusLabel,
		CaseRequestForm,
	},
	props: {
		caseEntity: {
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
						id: null,
						name: null,
					},
					case_outcome: {
						id: null,
						name: null,
					},
					denial_type: {
						id: null,
						name: null,
					},
					client_employee: {
						id: null,
						full_name: null,
					},
					facility: {
						id: null,
						name: null,
						facility_type: {
							id: null,
							name: null,
						},
					},
					denial_reasons: [],
					disciplines: [],
					case_requests: [],
					case_readmissions: [],
				};
			},
		},
	},
	computed: {
		caseClosed() {
			return this.caseEntity.closed && this.caseEntity.closed !== null;
		},
		hasReadmissions() {
			return this.caseEntity.case_readmissions && this.caseEntity.case_readmissions.length > 0;
		},
	},
	data() {
		return {
			showDetails: false,
		};
	},
	methods: {
		createdAppeal(appeal) {
			this.$router.push({
				name: "appeals.view",
				params: {
					id: this.caseEntity.id,
					appeal_id: appeal.id,
				},
			});

			this.$emit("added", appeal);
		},
		createdRequest(request) {
			this.$router.push({
				name: "caseRequests.view",
				params: {
					id: this.caseEntity.id,
					case_request_id: request.id,
				},
			});

			this.$emit("added-request", request);
		},
	},
};
</script>
