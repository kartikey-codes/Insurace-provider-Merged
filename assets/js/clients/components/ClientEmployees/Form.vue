<template>
	<loading-indicator v-if="loading" class="my-5" title="Fetching physician..." />
	<validation-observer v-else v-bind="$attrs" ref="observer" v-slot="{ invalid }">
		<b-form @submit.prevent="save">
			<b-card no-body>
				<slot name="header"></slot>

				<div v-if="licensingEnabled && !loadingLicenses">
					<b-alert v-if="availableLicenses <= 0" show variant="danger" class="m-2 p-4 shadow">
						<b-row class="d-flex justify-content-between align-items-center">
							<b-col cols="12" lg="8" class="text-left text-lg-left mb-4 mb-lg-0">
								<p class="mb-0 font-weight-bold">
									<font-awesome-icon icon="exclamation-triangle" fixed-width class="mr-1" />
									<span>You have no licenses left.</span>
								</p>
								<p class="mb-0">Update your subscription and add more licenses to create physicians.</p>
							</b-col>
							<b-col cols="12" lg="4" class="text-left text-lg-right">
								<b-button :to="{ name: 'subscription' }" variant="primary">
									Update Subscription
								</b-button>
							</b-col>
						</b-row>
					</b-alert>
					<b-alert
						v-else-if="availableLicenses <= licenseWarnThreshold"
						show
						variant="info"
						class="m-2 p-4 shadow"
					>
						<b-row class="d-flex justify-content-between align-items-center">
							<b-col cols="12" lg="8" class="text-left text-lg-left mb-4 mb-lg-0">
								<p class="mb-0 font-weight-bold">
									<font-awesome-icon icon="info-circle" fixed-width class="mr-1" />
									<span>You have {{ availableLicenses }} licenses left.</span>
								</p>
							</b-col>
							<b-col cols="12" lg="4" class="text-left text-lg-right">
								<b-button :to="{ name: 'subscription' }" variant="info"> Update Subscription </b-button>
							</b-col>
						</b-row>
					</b-alert>
				</div>

				<b-card-body class="mb-0">
					<validation-provider
						vid="first_name"
						name="First Name"
						:rules="{ required: true, alpha_dash: true }"
						v-slot="validationContext"
					>
						<b-form-group label="First Name" label-for="first_name" label-cols-lg="4">
							<b-form-input
								autofocus
								name="first_name"
								type="text"
								v-model="entity.first_name"
								required="required"
								placeholder="Required"
								:state="getValidationState(validationContext)"
								:disabled="saving || formDisabled"
							/>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<validation-provider
						vid="last_name"
						name="Last Name"
						:rules="{ required: true, alpha_dash: true }"
						v-slot="validationContext"
					>
						<b-form-group label="Last Name" label-for="last_name" label-cols-lg="4">
							<b-form-input
								name="last_name"
								type="text"
								v-model="entity.last_name"
								required="required"
								placeholder="Required"
								:state="getValidationState(validationContext)"
								:disabled="saving || formDisabled"
							/>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<b-form-group label="Title" label-for="title" label-cols-lg="4">
						<b-form-input
							name="title"
							type="text"
							v-model="entity.title"
							:disabled="saving || formDisabled"
						/>
					</b-form-group>

					<b-form-group label="Facility" label-for="facility_id" label-cols-lg="4">
						<b-form-select
							name="facility_id"
							v-model="entity.facility_id"
							:disabled="saving || loadingFacilities || formDisabled"
							:options="facilities"
							value-field="id"
							text-field="name"
						>
							<template #first>
								<option :value="null">(None)</option>
							</template>
						</b-form-select>
					</b-form-group>

					<b-form-group label="State" label-for="state" label-cols-lg="4">
						<b-form-select
							name="state"
							v-model="entity.state"
							:options="states"
							:disabled="saving || formDisabled"
							value-field="abbreviation"
							text-field="name"
						/>
					</b-form-group>

					<validation-provider
						vid="npi_number"
						name="NPI Number"
						:rules="{ required: true, numeric: true }"
						v-slot="validationContext"
					>
						<b-form-group label="NPI Number" label-for="npi_number" label-cols-lg="4">
							<b-input-group>
								<b-form-input
									name="npi_number"
									type="text"
									placeholder="Required"
									v-model="entity.npi_number"
									:disabled="saving || formDisabled"
									:state="getValidationState(validationContext)"
								/>
								<b-form-invalid-feedback
									v-for="error in validationContext.errors"
									:key="error"
									v-text="error"
								/>
							</b-input-group>
						</b-form-group>
					</validation-provider>

					<validation-provider
						vid="active"
						name="Active"
						:rules="{ required: false }"
						v-slot="validationContext"
					>
						<b-form-group
							label="Active"
							label-for="active"
							label-cols-lg="4"
							description="Inactive physicians will not show up in dropdown lists."
						>
							<b-form-checkbox name="active" v-model="entity.active" :disabled="saving"
								>Active</b-form-checkbox
							>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>
				</b-card-body>

				<b-card-body>
					<h6 class="text-muted">Optional</h6>
					<b-card no-body>
						<b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseContact
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
							>
								Contact Information
							</b-button>
						</b-card-header>
						<b-collapse id="collapseContact" role="tabpanel">
							<b-card-body>
								<b-form-group label="Email Address" label-for="email" label-cols-lg="4">
									<b-form-input
										name="email"
										type="email"
										v-model="entity.email"
										:disabled="saving || formDisabled"
									/>
								</b-form-group>

								<b-form-group label="Work Phone" label-for="work_phone" label-cols-lg="4">
									<b-form-input
										name="work_phone"
										type="tel"
										v-model="entity.work_phone"
										v-mask="'(###) ###-####'"
										:disabled="saving || formDisabled"
									/>
								</b-form-group>

								<b-form-group label="Mobile Phone" label-for="mobile_phone" label-cols-lg="4">
									<b-form-input
										name="mobile_phone"
										type="tel"
										v-model="entity.mobile_phone"
										v-mask="'(###) ###-####'"
										:disabled="saving || formDisabled"
									/>
								</b-form-group>
							</b-card-body>
						</b-collapse>
					</b-card>
				</b-card-body>

				<b-card-footer>
					<b-row>
						<b-col cols="12" md="6" xl="4" class="mb-4 mb-md-0">
							<b-button block variant="light" type="button" @click.stop="cancel">Cancel</b-button>
						</b-col>
						<b-col cols="12" md="6" xl="4" offset-xl="4" class="mb-2 mb-md-0">
							<b-button
								block
								variant="primary"
								type="submit"
								:disabled="saving || invalid || formDisabled"
							>
								<font-awesome-icon v-if="saving" icon="circle-notch" spin fixed-width />
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

export default {
	name: "ClientEmployeeForm",
	components: {},
	props: {
		id: {
			default: null,
		},
		facilityId: {
			default: null,
		},
	},
	data() {
		return {
			loading: true,
			searchingNpi: null,
			npiResults: [],
			saving: false,
			entity: {
				id: this.id,
				facility_id: this.facilityId,
				first_name: null,
				last_name: null,
				title: null,
				npi_number: null,
				work_phone: null,
				mobile_phone: null,
				email: null,
				state: null,
				active: true,
			},
		};
	},
	computed: {
		enteredName() {
			return this.entity.first_name && this.entity.last_name;
		},
		lookupDisabled() {
			return this.searchingNpi || !this.enteredName || !this.entity.state;
		},
		formDisabled() {
			if (!this.licensingEnabled) {
				return false;
			}

			return this.entity.id == null && this.availableLicenses <= 0;
		},
		...mapGetters({
			facilities: "facilities/active",
			loadingFacilities: "facilities/loadingActive",
			states: "states/states",
			licensingEnabled: "licenses/enabled",
			availableLicenses: "licenses/available",
			loadingLicenses: "licenses/loading",
			licenseWarnThreshold: "licenses/warnThreshold",
		}),
	},
	mounted() {
		if (!this.id) {
			this.loading = false;
		} else {
			this.refresh();
		}
	},
	methods: {
		getValidationState,
		cancel() {
			this.$emit("cancel");
		},
		async refresh() {
			try {
				this.loading = true;

				const response = await this.$store.dispatch("clientEmployees/get", {
					id: this.id,
				});

				this.entity = response;
				this.$emit("loaded", response);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Error getting employee details",
				});
			} finally {
				this.loading = false;
				this.initialLoaded = true;
			}
		},
		async save(e) {
			try {
				this.saving = true;

				const response = await this.$store.dispatch("clientEmployees/save", this.entity);

				this.$emit("saved", response);
				this.$emit("update:id", response.id);
			} catch (e) {
				if (e.response.data.errors) {
					this.$refs.observer.setErrors(formatErrors(e.response.data.errors));
				}

				this.$store.dispatch("apiError", {
					error: e,
					title: "Save Failed",
					message: "Error saving employee details. Please check for errors.",
					variant: "warning",
				});
			} finally {
				this.saving = false;
			}
		},
	},
};
</script>
