<template>
	<loading-indicator v-if="loading" class="my-5" />
	<validation-observer v-else v-bind="$attrs" ref="observer" v-slot="{ invalid }">
		<b-form @submit.prevent="save">
			<b-card no-body>
				<slot name="header"></slot>
				<b-card-body>
					<b-row>
						<b-col cols="12" lg="4">
							<validation-provider
								vid="first_name"
								name="First Name"
								:rules="{ required: true, min: 1, max: 50 }"
								v-slot="validationContext"
							>
								<b-form-group
									label="First Name"
									label-for="first_name"
									label-cols="12"
									label-cols-lg="12"
								>
									<b-form-input
										autofocus
										name="first_name"
										type="text"
										v-model="entity.first_name"
										required="required"
										placeholder="Required"
										:state="getValidationState(validationContext)"
										:disabled="saving"
									/>
									<b-form-invalid-feedback
										v-for="error in validationContext.errors"
										:key="error"
										v-text="error"
									/>
								</b-form-group>
							</validation-provider>
						</b-col>
						<b-col cols="12" lg="4">
							<validation-provider
								vid="middle_name"
								name="Middle Name"
								:rules="{ required: false, max: 50 }"
								v-slot="validationContext"
							>
								<b-form-group
									label="Middle Name"
									label-for="middle_name"
									label-cols="12"
									label-cols-lg="12"
								>
									<b-form-input
										name="middle_name"
										type="text"
										v-model="entity.middle_name"
										:state="getValidationState(validationContext)"
										:disabled="saving"
									/>
									<b-form-invalid-feedback
										v-for="error in validationContext.errors"
										:key="error"
										v-text="error"
									/>
								</b-form-group>
							</validation-provider>
						</b-col>
						<b-col cols="12" lg="4">
							<validation-provider
								vid="last_name"
								name="Last Name"
								:rules="{ required: true, min: 1, max: 50 }"
								v-slot="validationContext"
							>
								<b-form-group
									label="Last Name"
									label-for="last_name"
									label-cols="12"
									label-cols-lg="12"
								>
									<b-form-input
										name="last_name"
										type="text"
										v-model="entity.last_name"
										required="required"
										placeholder="Required"
										:state="getValidationState(validationContext)"
										:disabled="saving"
									/>
									<b-form-invalid-feedback
										v-for="error in validationContext.errors"
										:key="error"
										v-text="error"
									/>
								</b-form-group>
							</validation-provider>
						</b-col>
					</b-row>
					<b-row>
						<b-col cols="12" lg="4">
							<validation-provider
								vid="date_of_birth"
								name="Date of Birth"
								:rules="{ required: false }"
								v-slot="validationContext"
							>
								<b-form-group
									label="Date of Birth"
									label-for="date_of_birth"
									label-cols="12"
									label-cols-lg="12"
								>
									<b-form-input
										name="date_of_birth"
										type="date"
										v-model="entity.date_of_birth"
										:disabled="saving"
										:state="getValidationState(validationContext)"
										:min="minDate"
										:max="maxDate"
									/>
									<b-form-invalid-feedback
										v-for="error in validationContext.errors"
										:key="error"
										v-text="error"
									/>
								</b-form-group>
							</validation-provider>
						</b-col>
						<b-col cols="12" lg="4">
							<validation-provider
								vid="sex"
								name="Gender"
								:rules="{ required: false }"
								v-slot="validationContext"
							>
								<b-form-group
									id="sex"
									label="Gender"
									label-for="sex"
									label-cols="12"
									label-cols-lg="12"
								>
									<b-form-radio-group
										stacked
										name="sex"
										v-model="entity.sex"
										:options="sexes"
										:disabled="saving"
										:state="getValidationState(validationContext)"
										value-field="value"
										text-field="name"
									>
										<template #first>
											<!-- this slot appears above the options from 'options' prop -->
											<b-form-radio :value="null" class="text-muted">(No Answer)</b-form-radio>
										</template>
									</b-form-radio-group>
									<b-form-invalid-feedback
										v-for="error in validationContext.errors"
										:key="error"
										v-text="error"
									/>
								</b-form-group>
							</validation-provider>
						</b-col>
						<b-col cols="12" lg="4">
							<validation-provider
								vid="marital_status"
								name="Marital Status"
								:rules="{ required: false }"
								v-slot="validationContext"
							>
								<b-form-group
									label="Marital Status"
									label-for="marital_status"
									label-cols="12"
									label-cols-lg="12"
								>
									<b-form-radio-group
										stacked
										name="marital_status"
										v-model="entity.marital_status"
										:options="maritalStatuses"
										:disabled="saving"
										:state="getValidationState(validationContext)"
										value-field="value"
										text-field="name"
									>
										<template #first>
											<!-- this slot appears above the options from 'options' prop -->
											<b-form-radio :value="null" class="text-muted">(No Answer)</b-form-radio>
										</template>
									</b-form-radio-group>
									<b-form-invalid-feedback
										v-for="error in validationContext.errors"
										:key="error"
										v-text="error"
									/>
								</b-form-group>
							</validation-provider>
						</b-col>
					</b-row>

					<h6 class="text-muted">Optional</h6>

					<b-card no-body>
						<b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseDetails
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
							>
								Additional Details
							</b-button>
						</b-card-header>
						<b-collapse id="collapseDetails" role="tabpanel">
							<b-card-body>
								<validation-provider
									vid="medical_record_number"
									name="Medical Record Number"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Medical Record Number"
										label-for="medical_record_number"
										label-cols-lg="4"
										description="Unique identifier for this patient in an EMR system."
									>
										<b-form-input
											name="medical_record_number"
											type="text"
											v-model="entity.medical_record_number"
											:state="getValidationState(validationContext)"
											:disabled="saving"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>

								<validation-provider
									vid="ssn_last_four"
									name="SSN Last Four Digits"
									:rules="{ required: false, max: 4 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="SSN Last 4"
										label-for="ssn_last_four"
										label-cols-lg="4"
										description="Last four digits of the patient's SSN."
									>
										<b-form-input
											name="ssn_last_four"
											type="text"
											maxlength="4"
											v-model="entity.ssn_last_four"
											:state="getValidationState(validationContext)"
											:disabled="saving"
											style="max-width: 10rem"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>

								<validation-provider
									vid="secured"
									name="Secured"
									:rules="{ required: false }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Secured"
										label-for="secured"
										label-cols-lg="4"
										description="Hides patient name on generated documents."
									>
										<b-form-checkbox name="secured" v-model="entity.secured" :disabled="saving"
											>Secured</b-form-checkbox
										>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>
							</b-card-body>
						</b-collapse>

						<b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseAddress
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
								>Address</b-button
							>
						</b-card-header>
						<b-collapse id="collapseAddress" role="tabpanel">
							<b-card-body>
								<b-form-group label="Address" label-for="street_address_1" label-cols-lg="4">
									<validation-provider
										vid="street_address_1"
										name="Street Address"
										:rules="{ required: false, max: 50 }"
										v-slot="validationContext"
									>
										<b-form-input
											name="street_address_1"
											type="text"
											v-model="entity.street_address_1"
											placeholder=""
											class="rounded-b-0"
											:state="getValidationState(validationContext)"
											:disabled="saving"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</validation-provider>
									<validation-provider
										vid="street_address_2"
										name="Street Address (Continued)"
										:rules="{ required: false, max: 50 }"
										v-slot="validationContext"
									>
										<b-form-input
											name="street_address_2"
											type="text"
											v-model="entity.street_address_2"
											placeholder=""
											class="rounded-t-0"
											:state="getValidationState(validationContext)"
											:disabled="saving"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</validation-provider>
								</b-form-group>

								<validation-provider
									vid="city"
									name="City"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group label="City" label-for="city" label-cols-lg="4">
										<b-form-input
											name="city"
											type="text"
											v-model="entity.city"
											:state="getValidationState(validationContext)"
											:disabled="saving"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>

								<validation-provider
									vid="state"
									name="State"
									:rules="{ required: false, max: 2 }"
									v-slot="validationContext"
								>
									<b-form-group label="State" label-for="state" label-cols-lg="4">
										<b-form-select
											name="state"
											v-model="entity.state"
											:options="states"
											value-field="abbreviation"
											text-field="name"
											:state="getValidationState(validationContext)"
											:disabled="saving"
										>
											<template #first>
												<option :value="null" />
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
									vid="zip"
									name="Zip"
									:rules="{ required: false, max: 20, alpha_num: true }"
									v-slot="validationContext"
								>
									<b-form-group label="Zip" label-for="zip" label-cols-lg="4">
										<b-form-input
											name="zip"
											type="text"
											v-model="entity.zip"
											:state="getValidationState(validationContext)"
											:disabled="saving"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>
							</b-card-body>
						</b-collapse>

						<b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseContact
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
								>Contact Information</b-button
							>
						</b-card-header>
						<b-collapse id="collapseContact" role="tabpanel">
							<b-card-body>
								<validation-provider
									vid="email"
									name="Email"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group label="Email Address" label-for="email" label-cols-lg="4">
										<b-form-input
											name="email"
											type="email"
											v-model="entity.email"
											:state="getValidationState(validationContext)"
											:disabled="saving"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>

								<validation-provider
									vid="phone"
									name="Phone"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group label="Phone" label-for="phone" label-cols-lg="4">
										<b-form-input
											name="phone"
											type="tel"
											v-model="entity.phone"
											v-mask="'(###) ###-####'"
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
									vid="fax"
									name="fax"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group label="Fax" label-for="fax" label-cols-lg="4">
										<b-form-input
											name="fax"
											type="tel"
											v-model="entity.fax"
											v-mask="'(###) ###-####'"
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
							</b-card-body>
						</b-collapse>
					</b-card>
				</b-card-body>

				<b-card-footer>
					<b-row>
						<b-col cols="12" md="6" lg="4" class="mb-4 mb-md-0">
							<b-button block variant="light" type="button" @click.prevent="cancel">Cancel</b-button>
						</b-col>
						<b-col cols="12" md="6" offset-lg="4" lg="4" class="mb-2 mb-md-0">
							<b-button block variant="primary" type="submit" :disabled="saving">
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
import { mapGetters } from "vuex";
import { formatErrors, getValidationState } from "@/validation";
import { getTodaysDate, getAbsoluteMinimumDate } from "@/shared/helpers/dateHelper";

export default {
	name: "PatientForm",
	props: {
		id: {
			default: null,
		},
	},
	data() {
		return {
			loading: true,
			saving: false,
			entity: {
				id: this.id,
				first_name: "",
				middle_name: "",
				last_name: "",
				date_of_birth: null,
				sex: null,
				marital_status: null,
				phone: null,
				fax: null,
				email: null,
				street_address_1: null,
				street_address_2: null,
				city: null,
				state: null,
				zip: null,
				secured: null,
				medical_record_number: null,
				ssn_last_four: null,
			},
			minDate: getAbsoluteMinimumDate(),
			maxDate: getTodaysDate(),
		};
	},
	computed: mapGetters({
		states: "states/states",
		sexes: "patients/sexes",
		maritalStatuses: "patients/maritalStatuses",
	}),
	mounted() {
		if (this.id) {
			this.refresh();
		} else {
			this.loading = false;
		}
	},
	methods: {
		getValidationState,
		cancel(e) {
			if (e) {
				e.preventDefault();
			}

			this.$emit("cancel");
		},
		async refresh() {
			try {
				this.loading = true;

				const response = await this.$store.dispatch("patients/get", {
					id: this.id,
				});

				this.entity = response;
				this.$emit("loaded", response);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Error getting patient details",
				});
			} finally {
				this.loading = false;
			}
		},
		async save() {
			try {
				this.saving = true;

				const response = await this.$store.dispatch("patients/save", this.entity);
				this.$emit("saved", response);
				this.$emit("update:id", response.id);
			} catch (e) {
				if (e.response.data.errors) {
					this.$refs.observer.setErrors(formatErrors(e.response.data.errors));
				}

				this.$store.dispatch("apiError", {
					error: e,
					title: "Save Failed",
					message: "Error saving patient details. Please check for errors.",
					variant: "warning",
				});
			} finally {
				this.saving = false;
			}
		},
	},
};
</script>
