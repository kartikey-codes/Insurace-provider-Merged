<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'users' }" v-text="`Users /`" />
				<span>{{ entity.full_name }}</span>
			</template>
			<template #buttons>
				<b-button variant="primary" :to="{ name: 'users.edit', params: { id } }" title="Edit">
					<font-awesome-icon icon="edit" fixed-width />
					<span>Edit</span>
				</b-button>

				<b-dropdown variant="secondary" right title="More Options">
					<template #button-content>
						<font-awesome-icon icon="cog" fixed-width />
					</template>

					<b-dropdown-item v-if="isAdmin" v-b-modal.setPassword :disabled="loading">
						<font-awesome-icon icon="key" fixed-width />
						<span>Change Password</span>
					</b-dropdown-item>

					<b-dropdown-item v-if="isAdmin && !entity.active" @click="enable" :disabled="loading">
						<font-awesome-icon icon="unlock" fixed-width />
						<span>Activate User</span>
					</b-dropdown-item>
					<b-dropdown-item v-if="isAdmin && entity.active" @click="disable" :disabled="loading">
						<font-awesome-icon icon="lock" fixed-width />
						<span>Deactivate User</span>
					</b-dropdown-item>

					<b-dropdown-item v-if="isAdmin && entity.locked" @click="unlock" :disabled="loading">
						<font-awesome-icon icon="unlock" fixed-width />
						<span>Unlock Login</span>
					</b-dropdown-item>

					<b-dropdown-divider />

					<b-dropdown-item @click="deleteEntity" :disabled="loading" variant="danger">
						<font-awesome-icon icon="trash" fixed-width />
						<span>Delete User</span>
					</b-dropdown-item>
				</b-dropdown>
			</template>
		</page-header>

		<b-row class="my-4">
			<b-col cols="12" class="mb-4">
				<loading-indicator v-if="showLoading" class="my-5" />

				<div v-else>
					<b-row class="d-flex justify-content-between align-items-center mt-0 mb-lg-2">
						<b-col cols="12" lg="6" class="text-left mb-lg-2">
							<p class="text-muted mb-0">
								<span v-if="entity.last_seen">
									Last active {{ $filters.fromNow(entity.last_seen) }}
								</span>
								<span v-else-if="entity.last_login">
									Last logged in {{ $filters.fromNow(entity.last_login) }}
								</span>
								<span v-else>
									<font-awesome-icon icon="exclamation-triangle" fixed-width />
									Never logged in
								</span>
							</p>
						</b-col>
						<b-col cols="12" lg="6" class="text-left text-lg-right mb-2 mb-lg-2">
							<b-badge pill variant="danger" v-if="entity.active === false">
								<font-awesome-icon icon="times" fixed-width />
								<span>Inactive</span>
							</b-badge>

							<b-badge pill variant="primary" v-if="entity.admin === true">
								<font-awesome-icon icon="star" fixed-width />
								<span>Staff</span>
							</b-badge>

							<b-badge pill variant="primary" v-if="entity.client_admin === true">
								<font-awesome-icon icon="star" fixed-width />
								<span>Administrator</span>
							</b-badge>

							<b-badge pill variant="danger" v-if="entity.locked === true">
								<font-awesome-icon icon="lock" fixed-width />
								<span>Locked Out</span>
							</b-badge>
						</b-col>
					</b-row>

					<b-card no-body class="shadow-sm">
						<b-tabs card active-nav-item-class="font-weight-bold">
							<b-tab lazy>
								<template #title>Details</template>

								<b-row>
									<b-col cols="12" lg="6">
										<dl class="mb-4">
											<div class="row">
												<dt class="col-4 text-muted h6 small">Email</dt>
												<dd class="col-8">
													<span v-if="entity.email">
														<a
															:href="'mailto:' + entity.email"
															class="text-decoration-none"
															>{{ entity.email }}</a
														>
													</span>
													<span v-else class="text-danger"> Missing </span>
												</dd>
											</div>
											<div class="row">
												<dt class="col-4 text-muted h6 small">Phone</dt>
												<dd class="col-8">
													<span v-if="entity.phone && entity.phone.length > 0">
														<a
															:href="$filters.linkTel(entity.phone)"
															class="text-decoration-none"
															>{{ $filters.formatPhone(entity.phone) }}</a
														>
													</span>
													<span v-else class="text-muted"> &mdash; </span>
												</dd>
											</div>
											<div class="row" v-if="entity.last_login">
												<dt class="col-4 text-muted h6 small">Last Login</dt>
												<dd class="col-8">
													<div>
														{{ $filters.formatDate(entity.last_login) }}
													</div>
													<div class="small text-muted">
														<span> {{ $filters.fromNow(entity.last_login) }} </span>
														<span v-if="entity.last_login_ip">
															from {{ entity.last_login_ip }}
														</span>
													</div>
												</dd>
											</div>
											<div class="row">
												<dt class="col-4 text-muted h6 small">Password Changed</dt>
												<dd class="col-8">
													<div v-if="entity.password_changed">
														<div>
															{{ $filters.formatDate(entity.password_changed) }}
														</div>
														<div class="small text-muted">
															{{ $filters.fromNow(entity.password_changed) }}
														</div>
													</div>
													<div v-else class="text-danger">Never</div>
												</dd>
											</div>
											<div class="row" v-if="entity.created">
												<dt class="col-4 text-muted h6 small">Created</dt>
												<dd class="col-8">
													<div>
														{{ $filters.formatDate(entity.created) }}
													</div>
													<div class="small text-muted">
														<span> {{ $filters.fromNow(entity.created) }} </span>
														<span v-if="entity.created_by_user?.full_name">
															by {{ entity.created_by_user.full_name }}
														</span>
													</div>
												</dd>
											</div>
										</dl>
										<dl class="mb-0">
											<div class="row">
												<dt class="col-4 text-muted h6 small">Roles</dt>
												<dd class="col-8">
													<b-list-group v-if="entity.roles && entity.roles.length > 0">
														<b-list-group-item
															v-for="role in entity.roles"
															:key="role.id"
															:to="{ name: 'roles.view', params: { id: role.id } }"
															class="d-flex justify-content-between align-items-center"
														>
															<div>{{ role.name }}</div>
															<font-awesome-icon icon="chevron-right" fixed-width />
														</b-list-group-item>
													</b-list-group>
													<span v-else class="text-muted"> &mdash; </span>
												</dd>
											</div>
										</dl>
									</b-col>
									<b-col cols="12" lg="6">
										<b-card no-body>
											<b-card-header>Permissions</b-card-header>
											<b-list-group
												flush
												v-if="effectivePermissions && effectivePermissions.length > 0"
											>
												<b-list-group-item
													v-for="permission in effectivePermissions"
													:key="permission.id"
												>
													<div>
														{{ permission.name }}
													</div>
												</b-list-group-item>
											</b-list-group>
											<empty-result v-else icon="ban">
												No effective permissions
												<template #content>
													<p>Application permissions are granted through assigning roles.</p>
													<p>
														Users inherit all permissions granted to them through one or
														more roles.
													</p>
													<p>
														Users without roles are only granted access to to features
														available to all users.
													</p>
												</template>
											</empty-result>
										</b-card>
									</b-col>
								</b-row>
							</b-tab>

							<b-tab no-body lazy active>
								<template #title>Cases</template>
								<case-index
									ref="caseList"
									:filters="caseFilters"
									@clicked="viewCase"
									empty-description="No cases assigned to this user"
								/>
							</b-tab>
						</b-tabs>
					</b-card>
				</div>
			</b-col>
		</b-row>

		<!-- Change Password Modal -->
		<validation-observer ref="setPasswordObserver" v-slot="{ invalid }">
			<b-form @submit.prevent="setPassword">
				<b-modal
					id="setPassword"
					size="lg"
					title="Change User's Password"
					ref="changePasswordModal"
					@ok="setPassword"
					@shown="shownSetPassword"
				>
					<b-container>
						<b-row>
							<b-col>
								<validation-provider
									vid="password"
									name="Password"
									:rules="{ required: true, min: 4 }"
									v-slot="validationContext"
								>
									<b-form-group
										id="password"
										label="New Password"
										label-for="password"
										label-cols-lg="4"
									>
										<b-form-input
											name="password"
											ref="password"
											type="password"
											v-model="new_password"
											required="required"
											:state="getValidationState(validationContext)"
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
							<b-col>
								<validation-provider
									vid="confirm_password"
									name="Confirm Password"
									:rules="{ required: true, min: 4, confirmed: 'password' }"
									v-slot="validationContext"
								>
									<b-form-group
										id="confirmPassword"
										label="Confirm Password"
										label-for="confirmPassword"
										label-cols-lg="4"
									>
										<b-form-input
											name="confirm_password"
											ref="confirm_password"
											type="password"
											v-model="confirm_password"
											required="required"
											:state="getValidationState(validationContext)"
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
					</b-container>
					<template #modal-footer>
						<b-container fluid class="px-0">
							<b-row no-gutters>
								<b-col cols="6" class="text-left">
									<b-button variant="link" @click="resetPasswordChange">Reset</b-button>
								</b-col>
								<b-col cols="6" class="text-right">
									<b-button
										variant="light"
										text-variant="light"
										@click="cancelPasswordChange"
										class="mr-2"
										>Cancel</b-button
									>
									<b-button
										variant="primary"
										text-variant="light"
										type="submit"
										@click="setPassword"
										:disabled="busy || invalid"
									>
										<font-awesome-icon v-if="busy" icon="circle-notch" spin fixed-width />
										<span>Change</span>
									</b-button>
								</b-col>
							</b-row>
						</b-container>
					</template>
				</b-modal>
			</b-form>
		</validation-observer>
	</div>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import { formatErrors, getValidationState } from "@/validation";
import CaseIndex from "@/clients/components/Cases/Index.vue";

// Should match server-side configuration
const passwordMinLength = 4;

export default {
	name: "UserView",
	components: {
		CaseIndex,
	},
	computed: {
		caseFilters() {
			return {
				assigned_to: this.$route.params.id,
			};
		},
		showLoading() {
			return this.loading && (!this.entity.id || this.entity.id == null);
		},
		isEmpty() {
			return !this.users || this.users.length <= 0;
		},
		newPasswordsMatch() {
			if (this.new_password == this.confirm_password) {
				return true;
			}

			return false;
		},
		// @todo Handle this in User associations
		effectivePermissions() {
			var permissions = [];

			for (var i = 0; i < this.entity.roles?.length ?? 0; i++) {
				const role = this.entity.roles[i];
				for (var j = 0; j < role.permissions.length; j++) {
					const permission = role.permissions[j];

					if (!permissions.find((existingPermission) => existingPermission.id == permission.id)) {
						permissions.push(permission);
					}
				}
			}

			return permissions;
		},
		...mapGetters({
			users: "users/active",
			loadingUsers: "users/loadingActive",
			isAdmin: "isAdmin",
			user: "user",
		}),
	},
	data() {
		return {
			id: this.$route.params.id,
			busy: false,
			loading: true,
			deleting: false,
			entity: {
				id: null,
				first_name: null,
				middle_name: null,
				last_name: null,
				date_of_birth: null,
				email: null,
				active: null,
				full_name: null,
			},
			new_password: null,
			confirm_password: null,
			confirmDeleteText: "Are you sure you want to delete this user?",
		};
	},
	mounted() {
		this.refresh();

		if (this.users.length <= 0) {
			this.$store.dispatch("users/getActive");
		}
	},
	methods: {
		getValidationState,
		async refresh() {
			try {
				this.loading = true;

				const response = await this.$store.dispatch("users/get", {
					id: this.$route.params.id,
				});

				this.entity = response;
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Error loading user details",
				});
			} finally {
				this.loading = false;
			}
		},
		viewUser(entity) {
			this.$router.push({
				name: "users.view",
				params: {
					id: entity.id,
				},
			});
		},
		viewCase(caseEntity) {
			this.$router.push({
				name: "cases.view",
				params: {
					id: caseEntity.id,
				},
			});
		},
		cancelPasswordChange(e) {
			this.$refs.changePasswordModal.hide();
		},
		resetPasswordChange(e) {
			this.new_password = null;
			this.confirm_password = null;
		},
		shownSetPassword() {
			if (this.$refs.newPassword) {
				this.$refs.newPassword.focus();
			}
		},
		async setPassword() {
			try {
				this.busy = true;

				const response = await this.$store.dispatch("users/setPassword", {
					id: this.entity.id,
					password: this.new_password,
					confirm_password: this.confirm_password,
				});

				this.entity = response;
				this.$refs.changePasswordModal.hide();

				this.$store.dispatch("notify", {
					variant: "primary",
					title: "User Password Changed",
					message: "The users password has been changed",
				});
			} catch (e) {
				if (e.response.data.errors) {
					this.$refs.setPasswordObserver.setErrors(formatErrors(e.response.data.errors));
				}

				this.$store.dispatch("apiError", {
					error: e,
					title: "Set Password Failed",
					message: "Failed to update user password. Please check for errors.",
				});
			} finally {
				this.busy = false;
			}
		},
		async deleteEntity() {
			if (!confirm(this.confirmDeleteText)) {
				return false;
			}

			try {
				this.deleting = true;

				const response = await this.$store.dispatch("users/delete", {
					id: this.$route.params.id,
				});

				this.$router.push({
					name: "users",
				});
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Delete Failed",
					message: "Failed to delete user. This user may not be eligible for deletion.",
				});
			} finally {
				this.deleting = false;
			}
		},
		async enable() {
			var message = `Are you sure you want to enable this user? They will be allowed to log in to the application as well as be assigned tasks.`;

			if (!confirm(message)) {
				return false;
			}

			try {
				this.loading = true;

				const response = await this.$store.dispatch("users/enable", {
					id: this.$route.params.id,
				});

				this.entity = response;
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Enable Failed",
					message: "Failed to enable user account",
				});
			} finally {
				this.loading = false;
			}
		},
		async disable() {
			var message = `Are you sure you want to disable this user? They will not be allowed to log in to the application until they are enabled again.`;

			if (!confirm(message)) {
				return false;
			}

			try {
				this.loading = true;

				const response = await this.$store.dispatch("users/disable", {
					id: this.$route.params.id,
				});

				this.entity = response;
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Disable Failed",
					message: "Failed to disable user account",
				});
			} finally {
				this.loading = false;
			}
		},
		async unlock() {
			var message = `Are you sure you want to unlock login for this user? They will be allowed to log in to the application again provided they use the correct password.`;

			if (!confirm(message)) {
				return false;
			}

			try {
				this.loading = true;

				const response = await this.$store.dispatch("users/unlock", {
					id: this.$route.params.id,
				});

				this.entity = response;
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Unlock Failed",
					message: "Failed to unlock user account",
				});
			} finally {
				this.loading = false;
			}
		},
	},
	watch: {
		"$route.params.id": {
			handler(val) {
				this.entity = {
					id: null,
					first_name: null,
					middle_name: null,
					last_name: null,
					date_of_birth: null,
					email: null,
					active: null,
					full_name: null,
				};

				this.refresh();
			},
		},
	},
};
</script>
