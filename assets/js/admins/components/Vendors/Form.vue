<template>
	<validation-observer v-bind="$attrs" ref="observer" v-slot="{ invalid }">
		<b-form @submit.stop.prevent="submit">
			<b-alert v-if="invalid" variant="warning" show> The form is invalid. Please fix any errors. </b-alert>

			<b-row>
				<b-col cols="12">
					<b-card class="mb-4">
						<b-card-title>Details</b-card-title>

						<validation-provider name="name" :rules="{ required: true, min: 3 }" v-slot="validationContext">
							<b-form-group
								id="name-input-group"
								label="Name"
								label-for="name"
								label-cols-lg="4"
								class="mb-4"
							>
								<b-form-input
									name="name"
									v-model="localValue.name"
									required
									size="lg"
									type="text"
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

						<b-form-group
							id="owner"
							label="Owner"
							label-for="owner"
							label-cols-lg="4"
							description="The user that owns this vendor group"
						>
							<new-user-search v-model="localValue.owner" :disabled="busy" readonly />
						</b-form-group>

						<b-form-group
							id="active"
							label="Active"
							label-for="active"
							label-cols-lg="4"
							description="Inactive vendors will not show up in dropdown lists."
						>
							<b-form-checkbox v-model="localValue.active" :disabled="busy">Active</b-form-checkbox>
						</b-form-group>
					</b-card>
				</b-col>

				<b-col cols="12">
					<b-card class="mb-4">
						<b-card-title>Contact</b-card-title>

						<validation-provider
							name="phone"
							:rules="{ regex: /^\(\d{3}\) \d{3}-\d{4}$/ }"
							v-slot="validationContext"
						>
							<b-form-group id="phone" label="Phone" label-for="phone" label-cols-lg="4">
								<b-form-input
									type="text"
									v-model="localValue.phone"
									v-mask="'(###) ###-####'"
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
					</b-card>
				</b-col>

				<b-col cols="12">
					<b-card class="mb-4">
						<b-card-title>Address</b-card-title>

						<b-form-group id="streetAddress1" label="Address" label-for="streetAddress1" label-cols-lg="4">
							<b-form-input
								type="text"
								v-model="localValue.street_address_1"
								:disabled="busy"
								placeholder="Street address"
							/>
						</b-form-group>

						<b-form-group
							id="streetAddress2"
							label="Address (Continued)"
							label-for="streetAddress2"
							label-cols-lg="4"
						>
							<b-form-input
								type="text"
								v-model="localValue.street_address_2"
								placeholder="Suite, unit, floor, etc..."
								:disabled="busy"
							/>
						</b-form-group>

						<b-form-group id="city" label="City" label-for="city" label-cols-lg="4">
							<b-form-input type="text" v-model="localValue.city" :disabled="busy" />
						</b-form-group>

						<b-form-group id="state" label="State" label-for="state" label-cols-lg="4">
							<b-form-select
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

						<b-form-group id="zip" label="Zip" label-for="zip" label-cols-lg="4">
							<b-form-input type="text" v-model="localValue.zip" :disabled="busy" />
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

/** @todo Move to shared */
import NewUserSearch from "@/clients/components/Search/NewUsers.vue";
import SpecialtySearchMulti from "@/clients/components/Search/SpecialtiesMulti.vue";

export default {
	name: "VendorForm",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					id: null,
					name: "",
				};
			},
		},
		busy: {
			type: Boolean,
			default: false,
		},
	},
	components: {
		SpecialtySearchMulti,
		NewUserSearch,
	},
	computed: {
		...mapGetters({
			states: "states/states",
		}),
	},
	data() {
		return {
			localValue: Object.assign({}, this.value),
		};
	},
	methods: {
		getValidationState,
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
