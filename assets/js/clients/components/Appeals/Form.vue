<template>
	<validation-observer v-bind="$attrs" ref="observer" v-slot="{ invalid }">
		<b-form @submit.prevent="save">
			<b-card no-body :class="flush ? 'border-0' : ''">
				<slot name="header"></slot>

				<b-card-body class="mb-0">
					<validation-provider
						vid="appeal_type_id"
						name="Appeal Type"
						:rules="{ required: true }"
						v-slot="validationContext"
					>
						<!-- <b-form-group label="Type" label-for="appeal_type_id" label-cols-lg="4">
							<b-form-radio-group
								v-model="entity.appeal_type_id"
								:options="appealTypes"
								:disabled="saving || loadingAppealTypes"
								:state="getValidationState(validationContext)"
								name="appeal_type_id"
								value-field="id"
								text-field="name"
								required="required"
							/>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group> -->
					</validation-provider>

					<validation-provider
						vid="appeal_level_id"
						name="Appeal Level"
						:rules="{ required: true }"
						v-slot="validationContext"
					>
						<b-form-group label="Level" label-for="appeal_level_id" label-cols-lg="4">
							<b-form-select
								v-model="entity.appeal_level_id"
								:options="insuranceData"
      							:disabled="saving || !insuranceData || insuranceData.length <= 0"
								:state="getValidationState(validationContext)"
								name="appeal_level_id"
								value-field="id"
      							text-field="label"
								required="required"
								
							/>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<validation-provider
						vid="letter_date"
						name="Letter Date"
						:rules="{ required: true }"
						v-slot="validationContext"
					>
						<b-form-group label="Letter Date" label-for="letter_date" label-cols-lg="4">
							<b-form-input
								type="date"
								v-model="letterDate"
								name="letter_date"
								required="required"
								:disabled="saving"
								:state="getValidationState(validationContext)"
								:min="minDate"
								:max="today"
							/>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<validation-provider
						vid="received_date"
						name="Received Date"
						:rules="{ required: true }"
						v-slot="validationContext"
					>
						<b-form-group label="Received Date" label-for="received_date" label-cols-lg="4">
							<b-form-input
								type="date"
								v-model="entity.received_date"
								name="received_date"
								required="required"
								:disabled="saving"
								:state="getValidationState(validationContext)"
								:min="minDate"
								:max="today"
							/>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<validation-provider
						vid="days_to_respond"
						name="Days to respond"
						:rules="{ required: true, min: 0, max: 365 }"
						v-slot="validationContext"
					>
						<b-form-group label="Days To Decision " label-for="days_to_respond" label-cols-lg="4">
							<b-form-input
								name="days_to_respond"
								type="number"
								step="1"
								min="0"
								max="365"
								default="daysToRespond"
								v-model="daysToRespond"
								:disabled="saving"
								:state="getValidationState(validationContext)"
								required="required"
							/>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<validation-provider
						vid="days_to_respond_from"
						name="Days to respond from"
						:rules="{ required: true }"
						v-slot="validationContext"
					>
						<b-form-group label="From" label-for="days_to_respond_from" label-cols-lg="4">
							<b-form-radio-group
								class="mt-2"
								v-model="entity.days_to_respond_from_id"
								:options="daysToRespondFroms"
								:disabled="saving || loadingDaysToRespondFroms"
								:state="getValidationState(validationContext)"
								name="days_to_respond_from"
								value-field="id"
								text-field="name"
								required="required"
							/>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<validation-provider
						vid="due_date"
						name="Due Date"
						:rules="{ required: true }"
						v-slot="validationContext"
					>
						<b-form-group label="Due Date" label-for="due_date" label-cols-lg="4">
							<b-form-input
								type="date"
								v-model="due_date"
								name="due_date"
								required="required"
								:readonly="false"
								:disabled="saving"
								:state="getValidationState(validationContext)"
								:min="entity.received_date"
							/>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<validation-provider
						vid="assigned_to"
						name="Assigned To"
						:rules="{ required: false }"
						v-slot="validationContext"
					>
						<b-form-group label="Assigned To" label-for="assigned_to" label-cols-lg="4">
							<b-form-select
								v-model="entity.assigned_to"
								name="assigned_to"
								:options="users"
								:disabled="saving"
								value-field="id"
								text-field="full_name"
							>
								<template #first>
									<option :value="null">(Unassigned)</option>
								</template>
							</b-form-select>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<validation-provider
						vid="audit_reviewer_id"
						name="Audit Reviewer"
						:rules="{ required: false }"
						v-slot="validationContext"
					>
						<b-form-group label="Audit Reviewer" label-for="audit_reviewer_id" label-cols-lg="4">
							<b-input-group>
								<b-form-select
									v-model="entity.audit_reviewer_id"
									:options="auditReviewers"
									:disabled="saving || loadingAuditReviewers"
									:state="getValidationState(validationContext)"
									@change="changedAuditReviewer"
									name="audit_reviewer_id"
									value-field="id"
									text-field="full_name"
								>
									<template #first>
										<option :value="null">(None)</option>
									</template>
								</b-form-select>
								<template #append>
									<b-button
										variant="primary"
										@click="addingAuditReviewer = !addingAuditReviewer"
										:active="addingAuditReviewer"
									>
										<font-awesome-icon icon="plus" fixed-width />
									</b-button>
								</template>
								<b-form-invalid-feedback
									v-for="error in validationContext.errors"
									:key="error"
									v-text="error"
								/>
							</b-input-group>
						</b-form-group>
					</validation-provider>

					<div v-if="addingAuditReviewer" class="mb-4">
						<audit-reviewer-form @saved="addedAuditReviewer" @cancel="addingAuditReviewer = false">
							<template #header>
								<b-card-header>
									<div class="d-flex justify-content-between align-items-center">
										<span class="font-weight-bold">Add New Audit Reviewer</span>
										<b-button
											variant="secondary"
											size="sm"
											@click="addingAuditReviewer = false"
											title="Cancel"
											class="mb-0"
										>
											<font-awesome-icon icon="remove" fixed-width class="my-0 py-0" />
										</b-button>
									</div>
								</b-card-header>
							</template>
						</audit-reviewer-form>
					</div>

					<validation-provider
						vid="agency_id"
						name="Agency"
						:rules="{ required: false }"
						v-slot="validationContext"
					>
						<b-form-group label="Agency" label-for="agency_id" label-cols-lg="4">
							<b-input-group>
								<b-form-select
									v-model="entity.agency_id"
									:options="agencies"
									:disabled="saving || loadingAgencies"
									:state="getValidationState(validationContext)"
									name="agency_id"
									value-field="id"
									text-field="name"
								>
									<template #first>
										<option :value="null">(None)</option>
									</template>
								</b-form-select>
								<template #append>
									<b-button
										variant="primary"
										@click="addingAgency = !addingAgency"
										:active="addingAgency"
									>
										<font-awesome-icon icon="plus" fixed-width />
									</b-button>
								</template>
								<b-form-invalid-feedback
									v-for="error in validationContext.errors"
									:key="error"
									v-text="error"
								/>
							</b-input-group>
						</b-form-group>
					</validation-provider>

					<div v-if="addingAgency" class="mb-4">
						<agency-form @saved="addedAgency" @cancel="addingAgency = false">
							<template #header>
								<b-card-header>
									<div class="d-flex justify-content-between align-items-center">
										<span class="font-weight-bold">Add New Agency</span>
										<b-button
											variant="secondary"
											size="sm"
											@click="addingAgency = false"
											title="Cancel"
											class="mb-0"
										>
											<font-awesome-icon icon="remove" fixed-width class="my-0 py-0" />
										</b-button>
									</div>
								</b-card-header>
							</template>
						</agency-form>
					</div>

						<validation-provider
							vid="insurance_provider_id"
							name="Insurance Provider"
							:rules="{ required: false }"
							v-slot="validationContext"
						>
							<b-form-group label="Insurance Provider" label-for="insurance_provider" label-cols-lg="4">
								<b-input-group>
									<b-select
										name="insurance_provider_id"
										v-model="insurance"
										:options="insuranceProviders"
										:disabled="saving || loadingInsuranceProviders"
										:state="getValidationState(validationContext)"
										value-field="id"
										text-field="name"
									>
										<template #first>
											<b-select-option :value="null"> (None) </b-select-option>
										</template>
									</b-select>
									<template #append>
										<b-button
											variant="primary"
											@click="addingInsuranceProvider = !addingInsuranceProvider"
											:active="addingInsuranceProvider"
										>
											<font-awesome-icon icon="plus" fixed-width />
										</b-button>
									</template>
								</b-input-group>
								<b-form-invalid-feedback
									v-for="error in validationContext.errors"
									:key="error"
									v-text="error"
								/>
							</b-form-group>
						</validation-provider>

						<div v-if="addingInsuranceProvider" class="mb-4">
							<insurance-provider-form
								@cancel="addingInsuranceProvider = false"
								@saved="addedNewInsuranceProvider"
							>
								<template #header>
									<b-card-header>
										<div class="d-flex justify-content-between align-items-center">
											<span class="font-weight-bold">Add Insurance Provider</span>
											<b-button
												variant="secondary"
												size="sm"
												@click="addingInsuranceProvider = false"
												title="Cancel"
												class="mb-0"
											>
												<font-awesome-icon icon="remove" fixed-width class="my-0 py-0" />
											</b-button>
										</div>
									</b-card-header>
								</template>
							</insurance-provider-form>
						</div>
					

					<b-card-footer>
						<b-row>
							<b-col cols="12" md="6" xl="4" class="mb-4 mb-md-0">
								<b-button v-if="!disableCancel" block variant="light" @click="cancel">Cancel</b-button>
							</b-col>
							<b-col cols="12" md="6" offset-xl="4" xl="4">
								<b-button
									block
									variant="primary"
									type="submit"
									:disabled="saving || disabled"
									:title="invalid ? 'Please fix any validation errors' : 'Save'"
									@click="buttonPressed"
									>
									<font-awesome-icon icon="circle-notch" v-if="saving" spin fixed-width />
									<span>Save</span>
								</b-button>
							</b-col>
						</b-row>
					</b-card-footer>

					

					<validation-provider
						vid="audit_identifier"
						name="Audit ID"
						:rules="{ required: false, max: 100 }"
						v-slot="validationContext"
					>
						<b-form-group
							label="Audit ID"
							label-for="audit_identifier"
							description="A vendor provided reference ID to this appeal"
							label-cols-lg="4"
						>
							<b-form-input
								name="audit_identifier"
								type="text"
								v-model="entity.audit_identifier"
								:disabled="saving"
								:state="getValidationState(validationContext)"
								placeholder="Optional"
							/>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<b-form-group
						label="Priority"
						label-for="priority"
						label-cols-lg="4"
						description="This is a high priority audit."
					>
						<b-form-checkbox name="priority" v-model="entity.priority">Priority</b-form-checkbox>
					</b-form-group>
				</b-card-body>
				

				<b-card-body>
					<h6 class="text-muted">Optional</h6>
					<b-card no-body>
						<b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseHearing
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
								>Hearing</b-button
							>
						</b-card-header>
						<b-collapse id="collapseHearing" role="tabpanel">
							<b-card-body>
								<validation-provider
									vid="hearing_date"
									name="Hearing Date"
									:rules="{ required: false }"
									v-slot="validationContext"
								>
									<b-form-group label="Hearing Date" label-for="hearing_date" label-cols-lg="4">
										<b-form-input
											name="hearing_date"
											type="date"
											v-model="entity.hearing_date"
											:disabled="saving"
											:state="getValidationState(validationContext)"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>

								<validation-provider
									vid="hearing_time"
									name="Hearing Time"
									:rules="{ required: false }"
									v-slot="validationContext"
								>
									<b-form-group label="Hearing Time" label-for="hearing_time" label-cols-lg="4">
										<b-form-input
											name="hearing_time"
											type="time"
											v-model="entity.hearing_time"
											:disabled="saving"
											:state="getValidationState(validationContext)"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>

								<b-form-group label="Meeting Type" label-for="meeting_type" label-cols-lg="4">
									<b-form-select
										id="meeting_type"
										v-model="entity.meeting_type"
										:disabled="saving"
									>
										<option value="Location">Location</option>
										<option value="Telephonic">Telephonic</option>
										<option value="Video Conference">Video Conference</option>
									</b-form-select>
								</b-form-group>

								<!-- Render input based on selected Meeting Type -->
								<template v-if="entity.meeting_type === 'Location'">
									<b-form-group label="Address" label-for="address" label-cols-lg="4">
										<b-form-input
											id="address"
											v-model="entity.address"
											:disabled="saving"
										/>
									</b-form-group>
								</template>
								<template v-else-if="entity.meeting_type === 'Telephonic'">
									<b-form-group label="Phone Number" label-for="phone_number" label-cols-lg="4">
										<b-form-input
											id="phone_number"
											v-model="entity.phone_number"
											:disabled="saving"
										/>
									</b-form-group>
								</template>
								<template v-else-if="entity.meeting_type === 'Video Conference'">
									<b-form-group label="Conference URL" label-for="conference_url" label-cols-lg="4">
										<b-form-input
											id="conference_url"
											v-model="entity.conference_url"
											:disabled="saving"
										/>
									</b-form-group>
								</template>
								</b-card-body>
								</b-collapse>

						<b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseReferenceNumbers
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
								>Reference Numbers</b-button
							>
						</b-card-header>
						<b-collapse id="collapseReferenceNumbers" role="tabpanel">
							<b-card-body>
								<div class="d-flex">
									<b-button
										variant="secondary"
										@click="addReferenceNumber"
										:disabled="!canAddReferenceNumber"
										class="ml-auto mb-4"
									>
										<font-awesome-icon icon="plus" fixed-width />
										<span>Add Reference Number</span>
									</b-button>
								</div>
								<div v-if="entity.appeal_reference_numbers.length > 0">
									<div
										v-for="(refNumber, index) in entity.appeal_reference_numbers"
										:key="index"
										class="mb-2"
									>
										<b-form-group>
											<b-input-group>
												<b-form-select
													v-model="refNumber.reference_number_id"
													:options="referenceNumbers"
													:disabled="saving"
													value-field="id"
													text-field="name"
													required="required"
													placeholder="Label"
												>
													<template
														#first
														v-if="!referenceNumbers || referenceNumbers.length == 0"
													>
														<option
															v-if="!refNumber.reference_number_id"
															value="null"
															disabled
														>
															Select a label
														</option>
														<option v-else value="null" disabled>
															No labels were found
														</option>
													</template>
												</b-form-select>
												<b-form-input
													type="text"
													v-model="refNumber.value"
													:disabled="saving"
													required="required"
													placeholder="Value"
												/>
												<b-input-group-prepend>
													<b-button
														variant="danger"
														@click="removeReferenceNumber(refNumber, index)"
													>
														<font-awesome-icon icon="times" fixed-width />
													</b-button>
												</b-input-group-prepend>
											</b-input-group>
										</b-form-group>
									</div>
								</div>
								<empty-result v-else>
									No reference numbers
									<template #content>
										<p>Reference numbers are used for storing additional appeal details.</p>
									</template>
								</empty-result>
							</b-card-body>
						</b-collapse>
					</b-card>
				</b-card-body>

				<b-card-footer>
					<b-row>
						<b-col cols="12" md="6" xl="4" class="mb-4 mb-md-0">
							<b-button v-if="!disableCancel" block variant="light" @click="cancel">Cancel</b-button>
						</b-col>
						<b-col cols="12" md="6" offset-xl="4" xl="4">
							<b-button
								block
								variant="primary"
								type="submit"
								:disabled="saving"
								:title="invalid ? 'Please fix any validation errors' : 'Save'"
							>
								<font-awesome-icon icon="circle-notch" v-if="saving" spin fixed-width />
								<span>Save</span>
							</b-button>
						</b-col>
					</b-row>
				</b-card-footer>
			</b-card>
		</b-form>
	</validation-observer>
</template>

<script type="text/javascript">
import moment from "moment";
import { mapGetters } from "vuex";
import { formatErrors, getValidationState } from "@/validation";
import { getAbsoluteMinimumDate, getTodaysDate } from "@/shared/helpers/dateHelper";
import AuditReviewerForm from "@/clients/components/AuditReviewers/Form.vue";
import AgencyForm from "@/clients/components/Agencies/Form.vue";
import InsuranceProviderForm from "@/clients/components/InsuranceProviders/Form.vue";
import axios from "axios";


export default {
	name: "AppealForm",
	components: {
		AuditReviewerForm,
		AgencyForm,
		InsuranceProviderForm,
	},
	props: {
		appeal: {
			type: Object,
			default: () => {
				return {
					id: null,
					case_id: null,
					appeal_type_id: null,
					appeal_level_id: null,
					days_to_respond: 30,
					days_to_respond_from_id: null,
					assigned_to: null,
					letter_date: getTodaysDate(),
					received_date: getTodaysDate(),
					due_date: null,
					hearing_date: null,
					hearing_time: null,
					appeal_reference_numbers: [],
					audit_reviewer_id: null,
					agency_id: null,
					priority: null,
					audit_identifier: null,
				};
			},
		},
		caseEntity: {
			type: Object,
			default: () => {
				return {
					id: null,
					assigned_to: null,
				};
			},
		},
		referenceLimit: {
			type: Number,
			default: 10,
		},
		flush: {
			type: Boolean,
			default: false,
		},
		disableCancel: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			entity: this.appeal,
			loading: true,
			saving: false,
			minDate: getAbsoluteMinimumDate(),
			today: getTodaysDate(),
			addingAuditReviewer: false,
			addingAgency: false,
			addingInsuranceProvider: false,
			letterDate: null,	
			due_date:null,
			insurance:null,
			agency_autofill:null,
			daysToRespond:null,
			insuranceData:[],
		};
		
	},
	computed: {
		availableAppealLevels() {
			if (
				!this.caseEntity ||
				!this.caseEntity.insurance_provider ||
				!this.caseEntity.insurance_provider.appeal_levels
			) {
				return this.appealLevels;
			}

			if (this.caseEntity.insurance_provider.appeal_levels.length > 0) {
				return this.caseEntity.insurance_provider.appeal_levels;
			}

			return this.appealLevels;
		},
		dueDate() {
			if (this.entity.days_to_respond && this.entity.days_to_respond_from_id) {
				if (this.entity.days_to_respond_from_id == 1) {
					// received date
					if (this.entity.received_date) {
						return moment(this.entity.received_date)
							.add(this.entity.days_to_respond, "days")
							.format("YYYY-MM-DD");
					} else {
						return null;
					}
				} else if (this.entity.days_to_respond_from_id == 2) {
					// letter date
					if (this.entity.letter_date) {
						return moment(this.entity.letter_date)
							.add(this.entity.days_to_respond, "days")
							.format("YYYY-MM-DD");
					} else {
						return null;
					}
				} else {
					console.error("Invalid days_to_respond_from_id " + this.entity.days_to_respond_from_id);
					return null;
				}
			}
			return null;
		},
		canAddReferenceNumber() {
			if (!this.entity.appeal_reference_numbers || !this.entity.appeal_reference_numbers.length) {
				return true;
			}

			// Limit number of readmissions
			return this.entity.appeal_reference_numbers.length < this.referenceLimit;
		},
		...mapGetters({
			loadingUsers: "users/loadingActive",
			users: "users/active",
			agencies: "agencies/active",
			loadingAgencies: "agencies/loadingActive",
			appealTypes: "appealTypes/all",
			loadingAppealTypes: "appealTypes/loadingAll",
			auditReviewers: "auditReviewers/active",
			loadingAuditReviewers: "auditReviewers/loadingActive",
			daysToRespondFroms: "daysToRespondFroms/all",
			loadingDaysToRespondFroms: "daysToRespondFroms/loadingAll",
			appealLevels: "appealLevels/all",
			loadingAppealLevels: "appealLevels/loadingAll",
			referenceNumbers: "referenceNumbers/all",
			loadingReferenceNumbers: "referenceNumbers/loadingAll",
			insuranceProviders: "insuranceProviders/active",
			loadingInsuranceProviders: "insuranceProviders/loadingActive",
		}),
	},
	mounted() {

		this.test();

		
		// Default appeal type
		if (this.entity.appeal_type_id === null && this.appealTypes.length) {
			this.entity.appeal_type_id = this.appealTypes[0].id;
			
		}

		// Default appeal level
		if (this.entity.appeal_level_id === null && this.appealLevels.length) {
			// this.entity.appeal_level_id = this.appealLevels[0].id;
			
		}

		// Default days to respond from
		if (this.entity.days_to_respond_from_id === null && this.daysToRespondFroms.length) {
			this.entity.days_to_respond_from_id = this.daysToRespondFroms[0].id;
			
		}

		// Default assigned_to
		if (this.entity.assigned_to === null && this.caseEntity.assigned_to) {
			this.entity.assigned_to = this.caseEntity.assigned_to;
			
		}

		if (this.users.length <= 0) {
			this.$store.dispatch("users/getActive");
			
		}

		// for calling autofillform function during mounting phase for autofilling the form
		if(true){
			this.autoFillForm();
		}
	},
	methods: {
		getValidationState,
		cancel() {
			this.$emit("cancel");
			this.reset();
		},
		async save(e) {
			try {
				this.saving = true;
				console.log("saving appeal =", this.entity.appeal_level_id)
				const response = await this.$store.dispatch("appeals/save", {
					id: this.entity.id || null,
					case_id: this.entity.case_id || this.caseEntity.id,
					appeal_type_id: this.entity.appeal_type_id,
					// appeal_level_id: this.entity.appeal_level_id,
					appeal_level_id: null,
					days_to_respond: this.entity.days_to_respond,
					days_to_respond_from_id: this.entity.days_to_respond_from_id,
					letter_date: this.entity.letter_date,
					received_date: this.entity.received_date,
					due_date: this.entity.due_date,
					hearing_date: this.entity.hearing_date,
					hearing_time: this.entity.hearing_time,
					assigned_to: this.entity.assigned_to,
					appeal_reference_numbers: this.entity.appeal_reference_numbers,
					audit_reviewer_id: this.entity.audit_reviewer_id,
					agency_id: this.entity.agency_id,
					audit_identifier: this.entity.audit_identifier,
					priority: this.entity.priority,
				});

				this.saving = false;
				this.$emit("saved", response);
			} catch (e) {
				console.log('error =',e);
				if (e.response.data.errors) {
					this.$refs.observer.setErrors(formatErrors(e.response.data.errors));
				}

				this.$store.dispatch("apiError", {
					error: e,
					title: "Save Failed",
					message: "Failed to save appeal",
				});
			} finally {
				this.saving = false;
			}
		},
		addReferenceNumber() {
			this.entity.appeal_reference_numbers.push({});
		},
		removeReferenceNumber(refNumber, index) {
			this.entity.appeal_reference_numbers.splice(index, 1);
		},
		reset() {
			this.entity = {
				appeal_type_id: null,
				appeal_level_id: null,
				days_to_respond: null,
				days_to_respond_from_id: null,
				assigned_to: null,
				letter_date: null,
				received_date: null,
				due_date: null,
				audit_reviewer_id: null,
				agency_id: null,
				hearing_date: null,
				hearing_time: null,
				appeal_reference_numbers: [],
				priority: null,
				audit_identifier: null,
			};
		},
		addedAuditReviewer(auditReviewer) {
			this.addingAuditReviewer = false;
			this.entity.audit_reviewer_id = auditReviewer.id;
			this.$store.dispatch("auditReviewers/getActive");
		},
		changedAuditReviewer(value) {
			const auditReviewer = this.auditReviewers.find((ar) => ar.id === value);
			if (auditReviewer && auditReviewer.id) {
				this.entity.agency_id = auditReviewer.agency_id ?? null;
			}
		},
		addedAgency(agency) {
			this.addingAgency = false;
			this.entity.agency_id = agency.id;
			this.$store.dispatch("agencies/getActive");
		},
		addedNewInsuranceProvider(insuranceProvider) {
			this.$store.dispatch("insuranceProviders/getActive");
			this.addingInsuranceProvider = false;
			this.entity.insurance_provider_id = insuranceProvider.id;
		},

		formatDate(dateString) {
			const date = new Date(dateString);
			const year = date.getFullYear();
			const month = String(date.getMonth() + 1).padStart(2, '0');
			const day = String(date.getDate()).padStart(2, '0');
			return `${year}-${month}-${day}`;
		},

		addDaysToDate(dateString, days)
		 {
			const date = new Date(dateString);
			date.setDate(date.getDate() + days);
			return date.toISOString().split('T')[0];
		},

		async autoFillForm() {
			try{
				const url = "http://localhost:8765/client/textract";
				
				const responses = await axios.get(url, {
				headers: {
					"Accept": "application/json",
					// You can add other headers here if needed
				},
				});
				this.daysToRespond =  45;
				this.letterDate = this.formatDate('09/08/2023');  
				this.due_date = this.addDaysToDate(this.letterDate , this.daysToRespond);
				// this.insurance = responses.data[0].insurance_provider; 
				// this.insuranceProviders.unshift(this.insurance);
				this.agency_autofill = 'Performant';
				this.agencies.unshift(this.agency_autofill);
				
				
			} 
			catch (error) {
				console.error("Error:", error);
			}
    	},

		async buttonPressed() {
				console.log('Save button pressed');
				console.log("assigned_to = ", this.entity.assigned_to);
				const data = {'id':this.entity.assigned_to}

				// Use Axios to make the request to the controller for sending user_id 
				console.log(data);
				const resp = await axios.post('/client/sendemail', data);
				console.log(resp);
		},
		async test(){
			try {
				const url = "/client/insuranceappeal";
				
				const response = await axios.get(url, {
				headers: {
					"Accept": "application/json",
					// You can add other headers here if needed
				},
				});
				// Handle the response data here
				// this.insuranceData = response.data;
				let count =1 ;
				response.data.forEach((item, index) => {
				console.log(`Element at index ${index}:`, item);
				if(item.insurance_provider_id==this.caseEntity.insurance_provider_id){
					console.log("match found = ", item);
					let ids = parseInt(item.id, 10);
					this.insuranceData.push({label:item.label, id:ids , count:count});
					count ++;
				}
				});
				this.insuranceData.sort((a,b)=> a.id - b.id);
				console.log("response updated = " , this.insuranceData);
				console.log("case entity =", this.caseEntity);
				console.log("appeal =", this.entity.appeal_level_id);
				} 
			catch (error) {
				console.error(error);
			}

		},

		updateAppealLevelCount(){
			const selectedOption = this.insuranceData.find(option => option.id === this.entity.appeal_level_id);
			if (selectedOption) {
				this.entity.appeal_level_id = selectedOption.count;
			} else {
				this.entity.appeal_level_id = null; // Handle the case where no option is selected
			}
			console.log("apeeal level =" , this.entity.appeal_level_id);
		}

	},
	watch: {
		dueDate: function (newVal) {
			if (newVal) {
				this.entity.due_date = newVal;
			}
		},
	},
};
</script>
