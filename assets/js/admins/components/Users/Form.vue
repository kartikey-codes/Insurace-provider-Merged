<template>
	<validation-observer v-bind="$attrs" ref="observer" v-slot="{ invalid }">
		<b-form @submit.stop.prevent="submit">
			<b-alert v-if="invalid" variant="warning" show> The form is invalid. Please fix any errors. </b-alert>

			<b-row>
				<b-col cols="12">
					<b-card class="mb-4">
						<b-card-title>Details</b-card-title>

						<validation-provider
							vid="first_name"
							name="First Name"
							:rules="{ required: true, min: 3, max: 50 }"
							v-slot="validationContext"
						>
							<b-form-group label="First Name" label-for="first_name" label-cols-lg="4">
								<b-form-input
									name="first_name"
									type="text"
									v-model="localValue.first_name"
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

						<validation-provider
							vid="middle_name"
							name="Middle Name"
							:rules="{ required: false, max: 50 }"
							v-slot="validationContext"
						>
							<b-form-group label="Middle Name" label-for="middle_name" label-cols-lg="4">
								<b-form-input
									name="middle_name"
									type="text"
									v-model="localValue.middle_name"
									required
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

						<validation-provider
							vid="last_name"
							name="Last Name"
							:rules="{ required: true, min: 3, max: 50 }"
							v-slot="validationContext"
						>
							<b-form-group label="Last Name" label-for="last_name" label-cols-lg="4">
								<b-form-input
									name="last_name"
									type="text"
									v-model="localValue.last_name"
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

						<validation-provider
							vid="email"
							name="Email"
							:rules="{ required: true, min: 3, max: 50, email: true }"
							v-slot="validationContext"
						>
							<b-form-group label="Email" label-for="email" label-cols-lg="4">
								<b-form-input
									name="email"
									type="email"
									v-model="localValue.email"
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

						<validation-provider
							vid="client_id"
							name="Client"
							:rules="{ required: false }"
							v-slot="validationContext"
						>
							<b-form-group label="Client" label-for="client_id" label-cols-lg="4">
								<b-form-select
									v-model="localValue.client_id"
									:options="clients"
									:disabled="busy || loadingClients"
									:state="getValidationState(validationContext)"
									name="client_id"
									value-field="id"
									text-field="name"
									required="required"
								>
									<template #first>
										<option :value="null">(None)</option>
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
							vid="vendor_id"
							name="Vendor"
							:rules="{ required: false }"
							v-slot="validationContext"
						>
							<b-form-group label="Vendor" label-for="vendor_id" label-cols-lg="4">
								<b-form-select
									v-model="localValue.vendor_id"
									:options="vendors"
									:disabled="busy || loadingVendors"
									:state="getValidationState(validationContext)"
									name="vendor_id"
									value-field="id"
									text-field="name"
									required="required"
								>
									<template #first>
										<option :value="null">(None)</option>
									</template>
								</b-form-select>
								<b-form-invalid-feedback
									v-for="error in validationContext.errors"
									:key="error"
									v-text="error"
								/>
							</b-form-group>
						</validation-provider>

						<b-form-group
							label="Active"
							label-for="active"
							label-cols-lg="4"
							description="User can log in to the application."
						>
							<b-form-checkbox name="active" v-model="localValue.active">Active</b-form-checkbox>
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
	name: "UserForm",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					id: null,
					first_name: "",
					middle_name: "",
					last_name: "",
					email: "",
					active: true,
					client_id: null,
					vendor_id: null,
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
			clients: "clients/all",
			loadingClients: "clients/loadingAll",
			vendors: "vendors/all",
			loadingVendors: "vendors/loadingAll",
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
