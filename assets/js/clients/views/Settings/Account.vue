<template>
	<div>
		<page-header>
			<template #title>My Account</template>
		</page-header>

		<b-card no-body class="my-4 shadow-sm">
			<b-tabs card active-nav-item-class="font-weight-bold">
				<b-tab no-body :active="$route.hash === '#' || $route.hash === ''">
					<template #title>Profile</template>
					<b-card-body class="mb-0 fill-height p-4 p-lg-5">
						<div class="d-flex justify-content-between align-items-center mb-4">
							<div class="media">
								<svg
									xmlns="http://www.w3.org/2000/svg"
									width="48"
									height="48"
									fill="currentColor"
									class="bi bi-person-circle align-self-start mr-3 text-primary"
									viewBox="0 0 16 16"
								>
									<path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
									<path
										fill-rule="evenodd"
										d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"
									/>
								</svg>
								<div class="media-body">
									<h5 class="h5 mt-0 mb-0">{{ user.full_name }}</h5>
									<p class="h6 text-muted">{{ user.email }}</p>
								</div>
							</div>
						</div>

						<b-form-group label="First Name" label-for="firstName" label-cols-lg="4">
							<b-form-input name="firstName" type="text" v-model="user.first_name" :disabled="saving" />
						</b-form-group>

						<b-form-group label="Middle Name" label-for="middleName" label-cols-lg="4">
							<b-form-input name="middleName" type="text" v-model="user.middle_name" :disabled="saving" />
						</b-form-group>

						<b-form-group label="Last Name" label-for="lastName" label-cols-lg="4">
							<b-form-input name="lastName" type="text" v-model="user.last_name" :disabled="saving" />
						</b-form-group>

						<b-form-group label="Phone Number" label-for="phoneNumber" label-cols-lg="4">
							<b-form-input
								name="phoneNumber"
								type="text"
								v-model="user.phone"
								v-mask="'(###) ###-####'"
								:disabled="saving"
							/>
						</b-form-group>

						<b-form-group label="Birthday" label-for="birthday" label-cols-lg="4">
							<b-form-input name="birthday" type="date" v-model="user.date_of_birth" :disabled="saving" />
						</b-form-group>
					</b-card-body>
					<b-card-footer class="d-flex">
						<b-button variant="primary" type="submit" :disabled="saving" @click="save" class="ml-auto">
							<font-awesome-icon icon="circle-notch" v-if="saving" spin fixed-width />
							<span>Save Changes</span>
						</b-button>
					</b-card-footer>
				</b-tab>

				<b-tab no-body :active="$route.hash === '#password'">
					<template #title>Change Password</template>
					<validation-observer ref="changePasswordObserver" v-slot="{ invalid }">
						<b-card-body class="mb-0 fill-height p-4 p-lg-5">
							<validation-provider
								vid="current_password"
								name="Current Password"
								:rules="{ required: true, max: 50 }"
								v-slot="validationContext"
							>
								<b-form-group
									label="Current Password"
									label-for="current_password"
									label-cols-lg="4"
									class="mb-4"
								>
									<template #description>
										<span v-if="user.password_changed"
											>Last changed {{ $filters.fromNow(user.password_changed) }}.</span
										>
										<span v-else class="text-danger">
											<font-awesome-icon icon="exclamation-triangle" fixed-width />
											<span>Never changed.</span>
										</span>
									</template>
									<b-form-input
										name="current_password"
										type="password"
										v-model="currentPassword"
										:disabled="changingPassword"
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
								vid="password"
								name="New Password"
								:rules="{ required: true, max: 50 }"
								v-slot="validationContext"
							>
								<b-form-group label="New Password" label-for="password" label-cols-lg="4">
									<b-form-input
										name="password"
										type="password"
										v-model="newPassword"
										:disabled="changingPassword"
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
								vid="confirm_password"
								name="Confirm Password"
								:rules="{
									required: true,
									max: 50,
									confirmed: 'password',
								}"
								v-slot="validationContext"
							>
								<b-form-group label="Confirm Password" label-for="confirm_password" label-cols-lg="4">
									<b-form-input
										name="confirm_password"
										type="password"
										v-model="confirmPassword"
										:disabled="changingPassword"
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
						<b-card-footer class="d-flex">
							<b-button
								variant="primary"
								type="submit"
								:disabled="changingPassword"
								@click="changePassword"
								class="ml-auto"
							>
								<font-awesome-icon v-if="changingPassword" icon="circle-notch" spin fixed-width />
								<span>Change Password</span>
							</b-button>
						</b-card-footer>
					</validation-observer>
				</b-tab>
			</b-tabs>
		</b-card>
	</div>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import { formatErrors, getValidationState } from "@/validation";

export default {
	name: "ViewSettings",
	components: {},
	data() {
		return {
			saving: false,
			error: false,
			changingPassword: false,
			currentPassword: null,
			newPassword: null,
			confirmPassword: null,
		};
	},
	computed: mapGetters({
		user: "user",
	}),
	methods: {
		getValidationState,
		async save() {
			try {
				this.saving = true;

				const response = await this.$store.dispatch("auth/updateProfile", {
					first_name: this.user.first_name,
					middle_name: this.user.middle_name,
					last_name: this.user.last_name,

					phone: this.user.phone,
					date_of_birth: this.user.date_of_birth,
				});

				this.$emit("saved", response);
				this.$store.commit("setUser", response);

				this.$store.dispatch("notify", {
					variant: "primary",
					title: "Profile Updated",
					message: `Profile details updated.`,
				});
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Update Failed",
					message: "Failed to update profile. Please check for errors.",
					variant: "warning",
				});
			} finally {
				this.saving = false;
			}
		},
		async changePassword() {
			try {
				this.changingPassword = true;

				const response = await this.$store.dispatch("auth/changePassword", {
					current_password: this.currentPassword,
					password: this.newPassword,
					confirm_password: this.confirmPassword,
				});

				this.$emit("saved", response);
				this.$store.commit("setUser", response);

				this.$store.dispatch("notify", {
					variant: "primary",
					title: "Password Changed",
					message: `Your password has been updated.`,
				});

				this.$refs.changePasswordObserver.reset();

				this.currentPassword = null;
				this.newPassword = null;
				this.confirmPassword = null;
			} catch (e) {
				if (e.response.data.errors) {
					this.$refs.changePasswordObserver.setErrors(formatErrors(e.response.data.errors));
				}

				this.$store.dispatch("apiError", {
					error: e,
					title: "Password Change Failed",
					message: "Failed to update password. Please check for errors.",
					variant: "warning",
				});
			} finally {
				this.changingPassword = false;
			}
		},
	},
};
</script>
