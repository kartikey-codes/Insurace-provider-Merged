<template>
	<loading-indicator v-if="loading" class="my-5" />
	<validation-observer v-else v-bind="$attrs" ref="observer" v-slot="{ invalid }">
		<b-form @submit.prevent="save">
			<b-card no-body>
				<slot name="header"></slot>

				<b-card-body>
					<b-row>
						<b-col cols="12" lg="6" xl="4">
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
										v-model="entity.first_name"
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

						<b-col cols="12" lg="6" xl="4">
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
						<b-col cols="12" lg="6" xl="4">
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
										v-model="entity.last_name"
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

					<validation-provider
						vid="email"
						name="Email"
						:rules="{ required: true, min: 3, max: 50 }"
						v-slot="validationContext"
					>
						<b-form-group
							label="Email"
							label-for="email"
							description="The email address this user will use to log in with"
							label-cols-lg="4"
						>
							<b-form-input
								name="email"
								type="email"
								v-model="entity.email"
								:state="getValidationState(validationContext)"
								:disabled="saving"
								:readonly="isCurrentUser"
							/>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<b-form-group label="Roles" label-for="role_ids" label-cols-lg="4">
						<b-form-checkbox-group
							stacked
							name="role_ids"
							v-model="entity.roles._ids"
							:options="roles"
							:disabled="saving || loadingRoles"
							value-field="id"
							text-field="name"
						/>
					</b-form-group>
				</b-card-body>

				<b-card-body>
					<h6 class="text-muted">Optional</h6>
					<b-card no-body class="mb-0">
						<b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseAdditional
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
							>
								Additional Details
							</b-button>
						</b-card-header>
						<b-collapse id="collapseAdditional" role="tabpanel">
							<b-card-body>
								<validation-provider
									vid="birthday"
									name="Birthday"
									:rules="{ required: false }"
									v-slot="validationContext"
								>
									<b-form-group label="Birthday" label-for="birthday" label-cols-lg="4">
										<b-form-input
											name="birthday"
											type="date"
											v-model="entity.date_of_birth"
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
									vid="phone"
									name="Phone"
									:rules="{ required: false }"
									v-slot="validationContext"
								>
									<b-form-group label="Phone" label-for="phone" label-cols-lg="4">
										<b-form-input
											name="phone"
											type="tel"
											v-model="entity.phone"
											v-mask="`(###) ###-####`"
											:disabled="saving"
											:state="getValidationState(validationContext)"
										/>
									</b-form-group>
									<b-form-invalid-feedback
										v-for="error in validationContext.errors"
										:key="error"
										v-text="error"
									/>
								</validation-provider>
							</b-card-body>
						</b-collapse>
					</b-card>
				</b-card-body>

				<b-card-footer>
					<b-row>
						<b-col cols="12" md="6" xl="4" class="mb-4 mb-md-0">
							<b-button block variant="light" type="button" @click.stop="cancel"> Cancel </b-button>
						</b-col>
						<b-col cols="12" md="6" offset-xl="4" xl="4" class="mb-2 mb-md-0">
							<b-button block variant="primary" type="submit" :disabled="saving || invalid">
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

export default {
	name: "UserEditForm",
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
				first_name: null,
				middle_name: null,
				last_name: null,
				email: null,
				active: true,
				roles: {
					_ids: [],
				},
			},
		};
	},
	computed: {
		isCurrentUser() {
			return this.id == this.userId;
		},
		...mapGetters({
			userId: "userId",
			roles: "roles/all",
			loadingRoles: "roles/loadingAll",
		}),
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
		cancel() {
			this.$emit("cancel");
		},
		async refresh() {
			try {
				this.loading = true;

				const response = await this.$store.dispatch("users/get", {
					id: this.id,
				});

				this.entity = response;
				if (response.roles) {
					this.entity.roles = {
						_ids: response.roles.map((item) => item.id),
					};
				}

				this.$emit("loaded", response);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Error getting user details",
				});
			} finally {
				this.loading = false;
			}
		},
		async save(e) {
			try {
				this.saving = true;
				const response = await this.$store.dispatch("users/update", this.entity);
				this.$emit("saved", response);
			} catch (e) {
				if (e.response.data.errors) {
					this.$refs.observer.setErrors(formatErrors(e.response.data.errors));
				}

				this.$store.dispatch("apiError", {
					error: e,
					title: "Save Failed",
					message: "Error updating user details. Please check for errors.",
					variant: "warning",
				});
			} finally {
				this.saving = false;
			}
		},
	},
};
</script>
