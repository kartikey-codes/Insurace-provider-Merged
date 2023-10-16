<template>
	<validation-observer v-bind="$attrs" ref="observer" v-slot="{ invalid }">
		<b-form @submit.stop.prevent="submit">
			<b-alert v-if="invalid" variant="warning" show> The form is invalid. Please fix any errors. </b-alert>

			<b-row>
				<b-col cols="12">
					<b-card class="mb-4">
						<b-card-title>Details</b-card-title>

						<validation-provider name="name" :rules="{ required: true, min: 3 }" v-slot="validationContext">
							<b-form-group label="Name" label-for="name" label-cols-lg="4" class="mb-4">
								<b-form-input
									name="name"
									size="lg"
									type="text"
									v-model="localValue.name"
									required
									placeholder="Required"
									:state="getValidationState(validationContext)"
									:disabled="busy"
								/>
								<b-form-invalid-feedback
									v-for="error in validationContext.errors"
									:key="error"
									v-text="error"
								/>
							</b-form-group>
						</validation-provider>

						<b-form-group label="Type" label-for="clientType" label-cols-lg="4" class="mb-4">
							<b-form-radio-group
								stacked
								name="client_type_id"
								v-model="localValue.client_type_id"
								:options="clientTypes"
								:disabled="loadingClientTypes || busy"
								required="required"
								value-field="id"
								text-field="name"
							/>
						</b-form-group>

						<b-form-group
							label="NPI Number"
							label-for="npi_number"
							label-cols-lg="4"
							description="Must be unique for each client"
						>
							<b-form-input
								name="npi_number"
								type="text"
								v-model="localValue.npi_number"
								:disabled="busy"
							/>
						</b-form-group>

						<b-form-group label="Primary Taxonomy Code" label-for="primary_taxonomy" label-cols-lg="4">
							<b-form-input
								name="primary_taxonomy"
								type="text"
								v-model="localValue.primary_taxonomy"
								:disabled="busy"
							/>
						</b-form-group>

						<b-form-group label="Status" label-for="status" label-cols-lg="4">
							<b-form-radio-group
								stacked
								name="status"
								v-model="localValue.status"
								:options="statuses"
								:disabled="busy"
								@change="statusChanged"
								value-field="value"
								text-field="name"
							/>
						</b-form-group>

						<b-form-group
							label="Active"
							label-for="active"
							label-cols-lg="4"
							description="Inactive clients will not show up in dropdown lists."
						>
							<b-form-checkbox
								name="active"
								v-model="localValue.active"
								:disabled="busy"
								@change="activeChanged"
								>Active</b-form-checkbox
							>
						</b-form-group>
					</b-card>
				</b-col>

				<b-col cols="12">
					<b-card class="mb-4">
						<b-card-title>Contact</b-card-title>

						<b-form-group label="Phone" label-for="phone" label-cols-lg="4">
							<b-form-input
								name="phone"
								type="text"
								v-model="localValue.phone"
								v-mask="'(###) ###-####'"
								:disabled="busy"
							/>
						</b-form-group>

						<b-form-group label="Fax" label-for="fax" label-cols-lg="4">
							<b-form-input
								name="fax"
								type="text"
								v-model="localValue.fax"
								v-mask="'(###) ###-####'"
								:disabled="busy"
							/>
						</b-form-group>

						<b-form-group label="Group Email" label-for="email" label-cols-lg="4">
							<b-form-input name="email" type="email" v-model="localValue.email" :disabled="busy" />
						</b-form-group>
					</b-card>
				</b-col>

				<b-col cols="12">
					<b-card class="mb-4">
						<b-card-title>Address</b-card-title>

						<b-form-group label="Address" label-for="streetAddress1" label-cols-lg="4">
							<b-form-input
								name="street_address_1"
								type="text"
								v-model="localValue.street_address_1"
								placeholder="Street address"
								:disabled="busy"
							/>
						</b-form-group>

						<b-form-group label="Address (Continued)" label-for="streetAddress2" label-cols-lg="4">
							<b-form-input
								name="street_address_2"
								type="text"
								v-model="localValue.street_address_2"
								:disabled="busy"
								placeholder="Suite, unit, floor, etc..."
							/>
						</b-form-group>

						<b-form-group label="City" label-for="city" label-cols-lg="4">
							<b-form-input name="city" type="text" v-model="localValue.city" :disabled="busy" />
						</b-form-group>

						<b-form-group label="State" label-for="state" label-cols-lg="4">
							<b-form-select
								name="state"
								v-model="localValue.state"
								:options="states"
								:disabled="busy"
								value-field="abbreviation"
								text-field="name"
							>
								<template #first>
									<!-- this slot appears above the options from 'options' prop -->
									<option :value="null"></option>
								</template>
							</b-form-select>
						</b-form-group>

						<b-form-group label="Zip" label-for="zip" label-cols-lg="4">
							<b-form-input name="zip" type="text" v-model="localValue.zip" :disabled="busy" />
						</b-form-group>
					</b-card>
				</b-col>

				<b-col cols="12">
					<b-card class="mb-4">
						<b-card-title>Contact Person</b-card-title>

						<b-form-group label="Contact First Name" label-for="contact_first_name" label-cols-lg="4">
							<b-form-input
								name="contact_first_name"
								type="text"
								v-model="localValue.contact_first_name"
								:disabled="busy"
							/>
						</b-form-group>

						<b-form-group label="Contact Last Name" label-for="contact_last_name" label-cols-lg="4">
							<b-form-input
								name="contact_last_name"
								type="text"
								v-model="localValue.contact_last_name"
								:disabled="busy"
							/>
						</b-form-group>

						<b-form-group label="Contact Title" label-for="contact_title" label-cols-lg="4">
							<b-form-input
								name="contact_title"
								type="text"
								v-model="localValue.contact_title"
								:disabled="busy"
							/>
						</b-form-group>

						<b-form-group label="Department" label-for="contact_department" label-cols-lg="4">
							<b-form-input
								name="contact_department"
								type="text"
								v-model="localValue.contact_department"
								:disabled="busy"
							/>
						</b-form-group>

						<b-form-group label="Contact Phone" label-for="contact_phone" label-cols-lg="4">
							<b-form-input
								name="contact_phone"
								type="text"
								v-model="localValue.contact_phone"
								:disabled="busy"
								v-mask="'(###) ###-####'"
							/>
						</b-form-group>

						<b-form-group label="Contact Fax" label-for="contact_fax" label-cols-lg="4">
							<b-form-input
								name="contact_fax"
								type="text"
								v-model="localValue.contact_fax"
								:disabled="busy"
								v-mask="'(###) ###-####'"
							/>
						</b-form-group>

						<b-form-group label="Contact Email" label-for="contact_email" label-cols-lg="4">
							<b-form-input
								name="contact_email"
								type="email"
								v-model="localValue.contact_email"
								:disabled="busy"
							/>
						</b-form-group>
					</b-card>
				</b-col>

				<b-col cols="12">
					<b-card>
						<b-card-title>Subscription</b-card-title>

						<b-form-group
							label="Subscription Active"
							label-for="subscription_active"
							label-cols-lg="4"
							description="Allows the client to access the application. Can be used to bypass a subscription."
						>
							<b-form-checkbox
								name="subscription_active"
								v-model="localValue.subscription_active"
								:disabled="busy"
							>
								Subscription Active
							</b-form-checkbox>
						</b-form-group>

						<b-form-group
							label="Licenses"
							label-for="licenses"
							label-cols-lg="4"
							description="The number of licenses available to this client. This only affects the application. Ensure the correct quantity is also set in the client's subscription through the provider."
						>
							<b-form-input
								name="licenses"
								type="number"
								v-model="localValue.licenses"
								:disabled="busy"
								min="0"
								max="999"
								step="1"
							/>
						</b-form-group>

						<hr />

						<p class="text-muted">
							These settings are related to the subscription/payment provider, such as Stripe.
						</p>

						<b-form-group
							label="Customer ID"
							label-for="payment_provider_customer_id"
							label-cols-lg="4"
							description="The ID for this customer from the provider."
						>
							<b-form-input
								name="payment_provider_customer_id"
								type="text"
								v-model="localValue.payment_provider_customer_id"
								:disabled="busy"
							/>
						</b-form-group>

						<b-form-group
							label="Subscription Product ID"
							label-for="subscription_product_id"
							label-cols-lg="4"
							description="The ID of the product (subscription plan) the client is subscribed to."
						>
							<b-form-input
								name="subscription_product_id"
								type="text"
								v-model="localValue.subscription_product_id"
								:disabled="busy"
							/>
						</b-form-group>

						<b-form-group
							label="Subscription ID"
							label-for="payment_provider_subscription_id"
							label-cols-lg="4"
							description="The ID of the subscription belonging to this client from the provider."
						>
							<b-form-input
								name="payment_provider_subscription_id"
								type="text"
								v-model="localValue.payment_provider_subscription_id"
								:disabled="busy"
							/>
						</b-form-group>
					</b-card>
				</b-col>
			</b-row>
		</b-form>
	</validation-observer>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import { getValidationState } from "@/validation";

export default {
	name: "ClientForm",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					id: null,
					name: "",
					active: true,
					status: "Active",
					licenses: 1,
				};
			},
		},
		busy: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		...mapGetters({
			states: "states/states",
			statuses: "clients/statuses",
			loadingClientTypes: "clients/loadingTypes",
			clientTypes: "clients/types",
		}),
	},
	data() {
		return {
			localValue: Object.assign({}, this.value),
		};
	},
	mounted() {
		if (this.clientTypes.length <= 0) {
			this.$store.dispatch("clients/getTypes");
		}
	},
	methods: {
		getValidationState,
		statusChanged(value) {
			this.localValue.active = value == "Active";
		},
		activeChanged(value) {
			if (value) {
				this.localValue.status = "Active";
			} else {
				// not active
				if (this.localValue.status == "Active") {
					this.localValue.status = "Inactive";
				}
			}
		},
		async submit(e) {
			if (e) {
				e.preventDefault();
			}

			const valid = await this.$refs.observer.validate();

			if (valid) {
				this.$emit("input", this.localValue);
				this.$emit("save", this.localValue);
			} else {
				alert("Please correct any errors before submitting.");
			}
		},
	},
};
</script>
