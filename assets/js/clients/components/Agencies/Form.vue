<template>
	<loading-indicator v-if="loading" class="my-5" title="Fetching agency..." />
	<validation-observer v-else v-bind="$attrs" ref="observer" v-slot="{ invalid }">
		<b-form @submit.prevent="save">
			<b-card no-body>
				<slot name="header"></slot>

				<b-card-body>
					<validation-provider
						vid="name"
						name="Name"
						:rules="{ required: true, min: 2, max: 50 }"
						v-slot="validationContext"
					>
						<b-form-group label="Name" label-for="name" label-cols-lg="4" label-cols-xl="3">
							<b-form-input
								autofocus
								name="name"
								type="text"
								size="lg"
								v-model="entity.name"
								placeholder=""
								:state="getValidationState(validationContext)"
								:disabled="saving"
								required
							/>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
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
							label-cols-xl="3"
							description="Inactive agencies will not show up in dropdown lists."
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

					<validation-provider
						vid="third_party_contractor"
						name="Third Party Contractor"
						:rules="{ required: false }"
						v-slot="validationContext"
					>
						<b-form-group
							label="Third Party Contractor"
							label-for="third_party_contractor"
							label-cols-lg="4"
							label-cols-xl="3"
							description="This agency is a third party contractor."
						>
							<b-form-checkbox
								name="third_party_contractor"
								v-model="entity.third_party_contractor"
								:disabled="saving"
								>Third Party</b-form-checkbox
							>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>
				</b-card-body>
				<b-card-body class="mb-4">
					<h6 class="text-muted">Additional Details</h6>
					<b-card no-body>
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
								<b-form-group
									label="Address"
									label-for="street_address_1"
									label-cols-lg="4"
									label-cols-xl="3"
								>
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
									<b-form-group label="City" label-for="city" label-cols-lg="4" label-cols-xl="3">
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
									<b-form-group label="State" label-for="state" label-cols-lg="4" label-cols-xl="3">
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
									<b-form-group label="Zip" label-for="zip" label-cols-lg="4" label-cols-xl="3">
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
								>Contact Person</b-button
							>
						</b-card-header>
						<b-collapse id="collapseContact" role="tabpanel">
							<b-card-body>
								<validation-provider
									vid="contact_name"
									name="Name"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Name"
										label-for="contact_name"
										label-cols-lg="4"
										label-cols-xl="3"
									>
										<b-form-input
											name="contact_name"
											type="text"
											v-model="entity.contact_name"
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
									vid="contact_title"
									name="Title"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Title"
										label-for="contact_title"
										label-cols-lg="4"
										label-cols-xl="3"
									>
										<b-form-input
											name="contact_title"
											type="text"
											v-model="entity.contact_title"
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
									vid="contact_email"
									name="Email"
									:rules="{ required: false, min: 3, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Email"
										label-for="contact_email"
										label-cols-lg="4"
										label-cols-xl="3"
									>
										<b-form-input
											name="contact_email"
											type="email"
											v-model="entity.contact_email"
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
									vid="contact_phone"
									name="Phone"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Phone"
										label-for="contact_phone"
										label-cols-lg="4"
										label-cols-xl="3"
									>
										<b-form-input
											name="contact_phone"
											type="tel"
											v-model="entity.contact_phone"
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
									vid="contact_fax"
									name="Fax"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Fax"
										label-for="contact_fax"
										label-cols-lg="4"
										label-cols-xl="3"
									>
										<b-form-input
											name="contact_fax"
											type="tel"
											v-model="entity.contact_fax"
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

				<b-card-body>
					<h6 class="text-muted">Outgoing Documents</h6>

					<validation-provider
						vid="outgoing_primary_method"
						name="Primary Delivery Method"
						:rules="{ required: true }"
						v-slot="validationContext"
					>
						<b-form-group
							label="Primary Delivery Method"
							label-for="outgoing_primary_method"
							label-cols-lg="4"
							label-cols-xl="3"
							description="Primary method of delivering response packets to this agency."
						>
							<!-- <b-form-radio-group
								name="outgoing_primary_method"
								v-model="entity.outgoing_primary_method"
								:options="outgoingMethods"
								value-field="value"
								text-field="name"
								:state="getValidationState(validationContext)"
								:disabled="saving"
							/> -->


							<b-form-radio-group
								name="outgoing_primary_method"
								v-model="entity.outgoing_primary_method"
								:options="outgoingMethods"
								value-field="value"
								text-field="name"
								:state="getValidationState(validationContext)"
								:disabled="saving"
								>
								<!-- Add the new radio button for ESMD within the radio group -->
								<b-form-radio
									:value="'esmd'"
									:disabled="saving"
								>
									ESMD
								</b-form-radio>
    						</b-form-radio-group>
							
							
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<b-card no-body class="mt-4">
						<b-tabs card pills vertical v-model="outgoingMethodsTab" nav-class="px-2 px-lg-3">
							<b-tab title="Manual" id="outgoing_MANUAL" title-link-class="pr-lg-5">
								<b-alert show variant="light">
									<p>
										Outgoing documents that are unable to be automatically sent will be placed in
										the Manual queue.
									</p>
									<p class="mb-0">
										Manually queued documents can be marked as completed to remove them from the
										queue.
									</p>
								</b-alert>
							</b-tab>

							<b-tab title="Email" id="outgoing_EMAIL" title-link-class="pr-lg-5">
								<validation-provider
									vid="outgoing_profile.email"
									name="Email Address"
									:rules="{ required: false, min: 3, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Email Address"
										label-for="outgoing_profile.email"
										label-cols-lg="4"
										label-cols-xl="2"
										description="Email address to send outgoing documents to."
									>
										<b-form-input
											name="outgoing_profile.email"
											type="email"
											v-model="entity.outgoing_profile.email"
											:state="getValidationState(validationContext)"
											:disabled="saving"
											style="max-width: 32rem"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>
							</b-tab>

							<b-tab title="Fax" id="outgoing_FAX" title-link-class="pr-lg-5">
								<validation-provider
									vid="outgoing_profile.fax_number"
									name="Fax Number"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Fax Number"
										label-for="outgoing_profile.fax"
										label-cols-lg="4"
										label-cols-xl="2"
										description="Number where outgoing documents can be faxed to."
									>
										<b-form-input
											name="fax"
											type="tel"
											v-model="entity.outgoing_profile.fax_number"
											v-mask="'(###) ###-####'"
											:disabled="saving"
											:state="getValidationState(validationContext)"
											style="max-width: 24rem"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>
							</b-tab>

							<b-tab title="Website" id="outgoing_WEBSITE" title-link-class="pr-lg-5">
								<b-alert show variant="light">
									<font-awesome-icon icon="info-circle" fixed-width />
									Website-uploaded documents must be manually uploaded by the user processing them.
									{{ appName }} cannot directly upload to third party websites.
								</b-alert>

								<validation-provider
									vid="outgoing_profile.electronic_website"
									name="Website"
									:rules="{ required: false }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Upload URL"
										label-for="outgoing_profile.electronic_website"
										label-cols-lg="4"
										label-cols-xl="2"
										description="Website URL where documents can be submitted/uploaded."
									>
										<b-form-input
											name="outgoing_profile.electronic_website"
											type="text"
											v-model="entity.outgoing_profile.electronic_website"
											:state="getValidationState(validationContext)"
											:disabled="saving"
											style="max-width: 32rem"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>
							</b-tab>

							<b-tab title="ESMD" id="outgoing_WEBSITE" title-link-class="pr-lg-5">
								<!-- <b-alert show variant="light">
									<font-awesome-icon icon="info-circle" fixed-width />
									Website-uploaded documents must be manually uploaded by the user processing them.
									{{ appName }} cannot directly upload to third party websites.
								</b-alert> -->

								<validation-provider
									vid="outgoing_profile.electronic_website"
									name="Website"
									:rules="{ required: false }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Upload Gateway URL"
										label-for="outgoing_profile.electronic_website"
										label-cols-lg="4"
										label-cols-xl="2"
										description="Enter the URL for NHIN Gateway"
									>
										<b-form-input
											name="outgoing_profile.electronic_website"
											type="text"
											v-model="entity.outgoing_profile.electronic_website"
											:state="getValidationState(validationContext)"
											:disabled="saving"
											style="max-width: 32rem"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
									<b-form-group
										label="Upload CMS ID"
										label-for="outgoing_profile.electronic_website"
										label-cols-lg="4"
										label-cols-xl="2"
										description=""
									>
									<b-form-input
											name="outgoing_profile.electronic_website"
											type="text"
											v-model="entity.outgoing_profile.electronic_website"
											:state="getValidationState(validationContext)"
											:disabled="saving"
											style="max-width: 32rem"
										/>
									</b-form-group>
									<b-form-group
										label="Upload ESMD ID"
										label-for="outgoing_profile.electronic_website"
										label-cols-lg="4"
										label-cols-xl="2"
										description=""
									>
									<b-form-input
											name="outgoing_profile.electronic_website"
											type="text"
											v-model="entity.outgoing_profile.electronic_website"
											:state="getValidationState(validationContext)"
											:disabled="saving"
											style="max-width: 32rem"
										/>
									</b-form-group>
								</validation-provider>
							</b-tab>


							<b-tab title="Mail" id="outgoing_MAIL" title-link-class="pr-lg-5">
								<b-alert show variant="light">
									<font-awesome-icon icon="info-circle" fixed-width />
									Mailed documents must be manually marked as sent by a user processing them.
									{{ appName }} cannot directly mail outgoing documents.
								</b-alert>

								<validation-provider
									vid="outgoing_profile.mail_to_name"
									name="Name"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Name"
										label-for="outgoing_profile.mail_to_name"
										label-cols-lg="4"
										label-cols-xl="2"
										description="Name of recipient"
									>
										<b-form-input
											name="outgoing_profile.mail_to_name"
											type="text"
											v-model="entity.outgoing_profile.mail_to_name"
											:state="getValidationState(validationContext)"
											:disabled="saving"
											style="max-width: 32rem"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>

								<validation-provider
									vid="outgoing_profile.mail_to_department"
									name="Department"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Department"
										label-for="outgoing_profile.mail_to_department"
										label-cols-lg="4"
										label-cols-xl="2"
										description="Department name to be addressed to"
									>
										<b-form-input
											name="outgoing_profile.mail_to_department"
											type="text"
											v-model="entity.outgoing_profile.mail_to_department"
											:state="getValidationState(validationContext)"
											:disabled="saving"
											style="max-width: 32rem"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>

								<b-form-group
									label="Address"
									label-for="outgoing_profile.mail_to_address_1"
									label-cols-lg="4"
									label-cols-xl="2"
								>
									<validation-provider
										vid="outgoing_profile.mail_to_address_1"
										name="Street Address"
										:rules="{ required: false, max: 50 }"
										v-slot="validationContext"
									>
										<b-form-input
											name="outgoing_profile.mail_to_address_1"
											type="text"
											v-model="entity.outgoing_profile.mail_to_address_1"
											placeholder=""
											class="rounded-b-0"
											:state="getValidationState(validationContext)"
											:disabled="saving"
											style="max-width: 32rem"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</validation-provider>
									<validation-provider
										vid="outgoing_profile.mail_to_address_2"
										name="Street Address (Continued)"
										:rules="{ required: false, max: 50 }"
										v-slot="validationContext"
									>
										<b-form-input
											name="outgoing_profile.mail_to_address_2"
											type="text"
											v-model="entity.outgoing_profile.mail_to_address_2"
											placeholder=""
											class="rounded-t-0"
											:state="getValidationState(validationContext)"
											:disabled="saving"
											style="max-width: 32rem"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</validation-provider>
								</b-form-group>

								<validation-provider
									vid="outgoing_profile.mail_to_city"
									name="City"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="City"
										label-for="outgoing_profile.mail_to_city"
										label-cols-lg="4"
										label-cols-xl="2"
									>
										<b-form-input
											name="outgoing_profile.mail_to_city"
											type="text"
											v-model="entity.outgoing_profile.mail_to_city"
											:state="getValidationState(validationContext)"
											:disabled="saving"
											style="max-width: 32rem"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>

								<validation-provider
									vid="outgoing_profile.mail_to_state"
									name="State"
									:rules="{ required: false, max: 2 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="State"
										label-for="outgoing_profile.mail_to_state"
										label-cols-lg="4"
										label-cols-xl="2"
									>
										<b-form-select
											name="outgoing_profile.mail_to_state"
											v-model="entity.outgoing_profile.mail_to_state"
											:options="states"
											value-field="abbreviation"
											text-field="name"
											:state="getValidationState(validationContext)"
											:disabled="saving"
											style="max-width: 32rem"
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
									vid="outgoing_profile.mail_to_zip"
									name="Zip"
									:rules="{ required: false, max: 20, alpha_num: true }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Zip"
										label-for="outgoing_profile.mail_to_zip"
										label-cols-lg="4"
										label-cols-xl="2"
									>
										<b-form-input
											name="outgoing_profile.mail_to_zip"
											type="text"
											v-model="entity.outgoing_profile.mail_to_zip"
											:state="getValidationState(validationContext)"
											:disabled="saving"
											style="max-width: 16rem"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>
							</b-tab>
						</b-tabs>
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
import { save, get } from "@/clients/services/agencies";
import { formatErrors, getValidationState } from "@/validation";

export default {
	name: "AgencyForm",
	components: {},
	props: {
		id: {
			default: null,
		},
	},
	computed: mapGetters({
		appName: "appName",
		states: "states/states",
		outgoingMethods: "outgoingDocuments/availableMethods",
	}),
	data() {
		return {
			loading: true,
			saving: false,
			entity: {
				id: this.id,
				name: "",
				active: true,
				third_party_contractor: true,
				outgoing_primary_method: "MANUAL",
				outgoing_profile: {},
			},
			outgoingMethodsTab: 0,
			outgoingTabs: [
				// Must match order of tabs in template
				"outgoing_MANUAL",
				"outgoing_EMAIL",
				"outgoing_FAX",
				"outgoing_WEBSITE",
				"outgoing_MAIL",
			],
		};
	},
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
				const response = await get(this.id);
				this.entity = response;
				if (!response.outgoing_profile) {
					this.entity.outgoing_profile = {};
				}
				this.$emit("loaded", this.entity);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Error getting agency details",
				});
			} finally {
				this.loading = false;
			}
		},
		async save() {
			try {
				this.saving = true;

				const response = await save(this.entity);

				this.$emit("saved", response);
				this.$emit("update:id", response.id);
			} catch (e) {
				if (e.response.data.errors) {
					this.$refs.observer.setErrors(formatErrors(e.response.data.errors));
				}

				this.$store.dispatch("apiError", {
					error: e,
					title: "Save Failed",
					message: "Error saving agency details. Please check for errors.",
					variant: "warning",
				});
			} finally {
				this.saving = false;
			}
		},
	},
	watch: {
		entity: {
			deep: true,
			immediate: true,
			handler(val) {
				const tabIndex = this.outgoingTabs.findIndex(
					(tab) => tab === "outgoing_" + val.outgoing_primary_method
				);
				if (tabIndex > -1) {
					this.outgoingMethodsTab = tabIndex;
				}
			},
		},
	},
};
</script>
