<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'roles' }" v-text="`Roles /`" />
				<span>{{ entity.name }}</span>
			</template>
			<template #buttons>
				<b-button variant="primary" :to="{ name: 'roles.edit', params: { id } }" title="Edit">
					<font-awesome-icon icon="edit" fixed-width />
					<span>Edit</span>
				</b-button>

				<b-dropdown variant="secondary" right title="More Options">
					<template #button-content>
						<font-awesome-icon icon="cog" fixed-width />
					</template>

					<b-dropdown-item @click="destroy" :disabled="loading || deleting" variant="danger">
						<font-awesome-icon icon="trash" fixed-width />
						<span>Delete</span>
					</b-dropdown-item>
				</b-dropdown>
			</template>
		</page-header>

		<loading-indicator v-if="showLoading" class="my-5" />
		<b-row v-else class="my-4">
			<b-col cols="12" xl="4" class="mb-4">
				<b-card class="shadow-sm">
					<template #header> Add Members </template>
					<paginated-results
						v-slot="{
							doSearch,
							empty,
							hasNextPage,
							hasPrevPage,
							loading,
							nextPage,
							prevPage,
							page,
							pages,
							refresh,
							results,
							total,
						}"
						v-bind="{
							action: GetUsers,
							filters: {
								active: true,
								not_in_role: this.id,
							},
							search: addUserSearch,
							perPage: 8,
						}"
					>
						<b-row class="mb-4">
							<b-col cols="7" md="6" lg="6" class="mb-0">
								<b-form @submit.prevent="doSearch">
									<search-input v-model="addUserSearch" v-bind="{ loading }" />
								</b-form>
							</b-col>
							<b-col cols="5" md="6" lg="6" class="text-right">
								<simple-pagination v-bind="{ loading, nextPage, prevPage, hasPrevPage, hasNextPage }" />
							</b-col>
						</b-row>
						<loading-indicator v-if="loading && empty" size="4x" class="my-5" />
						<b-list-group v-else-if="!empty" class="shadow-sm mb-0">
							<b-list-group-item
								v-for="result in results"
								:key="result.id"
								:variant="result.active ? '' : 'light'"
							>
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<router-link
											:to="{ name: 'users.view', params: { id: result.id } }"
											class="h6 mb-0"
										>
											{{ result.full_name }}
										</router-link>
										<p class="small text-muted mb-0 text-break">{{ result.email }}</p>
									</div>
									<div>
										<b-button
											variant="primary"
											@click="addUser(result)"
											:disabled="updating"
											title="Add User"
										>
											<font-awesome-icon icon="plus" fixed-width />
											<span class="sr-only">Add User</span>
										</b-button>
									</div>
								</div>
							</b-list-group-item>
						</b-list-group>
						<empty-result v-else>
							No eligible users found
							<template #content>
								Only active users not already assigned to this role can be added.
							</template>
						</empty-result>
					</paginated-results>
				</b-card>
			</b-col>
			<b-col cols="12" xl="4" class="mb-4">
				<b-card no-body class="shadow-sm" header-bg-variant="primary" header-text-variant="white">
					<template #header>
						Members
						<b-badge pill variant="light">
							{{ $filters.formatNumber(entity.users.length) }}
						</b-badge>
					</template>
					<b-list-group flush v-if="entity.users.length > 0">
						<b-list-group-item
							v-for="user in entity.users"
							:key="user.id"
							:variant="user.active ? '' : 'light'"
						>
							<div class="d-flex justify-content-between align-items-center">
								<div>
									<router-link :to="{ name: 'users.view', params: { id: user.id } }" class="h6 mb-0">
										{{ user.full_name }}
									</router-link>
									<p class="small text-muted mb-0 text-break">{{ user.email }}</p>
								</div>
								<div>
									<b-button
										variant="danger"
										@click="removeUser(user)"
										:disabled="updating"
										title="Remove User"
									>
										<font-awesome-icon icon="remove" fixed-width />
										<span class="sr-only">Remove</span>
									</b-button>
								</div>
							</div>
						</b-list-group-item>
					</b-list-group>
					<empty-result v-else icon="users">
						No users assigned
						<template #content> Add users to grant permissions assigned to the role. </template>
					</empty-result>
				</b-card>
			</b-col>

			<b-col cols="12" xl="4" mb="4">
				<b-card no-body>
					<template #header> Permissions </template>
					<b-tabs v-if="!permissionsEmpty" pills card vertical>
						<b-tab v-for="(items, group) in permissionsGrouped" :key="group" :title="group" no-body>
							<b-list-group flush>
								<b-list-group-item v-for="permission in items" :key="permission.id">
									{{ permission.name }}
								</b-list-group-item>
							</b-list-group>
						</b-tab>
					</b-tabs>
					<empty-result v-else>
						No permissions assigned
						<template #content>
							This role does not allow any additional access beyond what features are available to all
							users.
						</template>
					</empty-result>
				</b-card>
			</b-col>
		</b-row>
	</div>
</template>

<script>
import { mapGetters } from "vuex";
import { get as GetRole, destroy as DeleteRole } from "@/clients/services/roles";
import { getIndex as GetUsers } from "@/clients/services/users";
import { groupPermissions } from "@/shared/helpers/permissionsHelper";

export default {
	name: "ViewRole",
	components: {},
	data() {
		return {
			id: this.$route.params.id,
			loading: true,
			deleting: false,
			updating: false,
			entity: {
				id: null,
				created: null,
				created_by: null,
				modified: null,
				modified_by: null,
				name: "",
				users: [],
				permissions: [],
			},
			addUserSearch: "",
		};
	},
	computed: {
		showLoading() {
			return this.loading && (!this.entity.id || this.entity.id == null);
		},
		permissionsEmpty() {
			return this.entity.permissions?.length <= 0 ?? true;
		},
		permissionsGrouped() {
			return groupPermissions(this.entity.permissions);
		},
		...mapGetters({
			permissions: "permissions/all",
		}),
	},
	created() {
		this.$store.dispatch("permissions/getAll");
	},
	mounted() {
		this.refresh();
	},
	methods: {
		GetUsers,
		async refresh() {
			try {
				this.loading = true;
				this.entity = await GetRole(this.id);
			} catch (e) {
				this.$router.push({ name: "roles" });
			} finally {
				this.loading = false;
			}
		},
		async addUser(user) {
			if (!confirm("Add user to this role?")) {
				return false;
			}

			try {
				this.updating = true;

				const response = await this.$store.dispatch("roles/addUser", {
					id: this.id,
					user_id: user.id,
				});

				this.$store.dispatch("notify", {
					variant: "primary",
					title: "User Added",
					message: `${user.full_name} added to role ${this.entity.name}.`,
				});

				this.refresh();
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Role Addition Failed",
					message: "Unable to add user to this role.",
				});
			} finally {
				this.updating = false;
			}
		},
		async removeUser(user) {
			if (!confirm("Remove user from this role?")) {
				return false;
			}

			try {
				this.updating = true;

				const response = await this.$store.dispatch("roles/removeUser", {
					id: this.id,
					user_id: user.id,
				});

				this.$store.dispatch("notify", {
					variant: "primary",
					title: "User Removed",
					message: `${user.full_name} removed from role ${this.entity.name}.`,
				});

				this.refresh();
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Role Removal Failed",
					message: "Unable to remove user from role.",
				});
			} finally {
				this.updating = false;
			}
		},
		async destroy() {
			if (!confirm("Are you sure you want to delete this role?")) {
				return;
			}

			try {
				this.deleting = true;
				const response = await DeleteRole(this.id);
				this.$router.push({ name: "roles" });

				this.$nextTick(function () {
					this.$store.dispatch("notify", {
						variant: "primary",
						title: "Role Deleted",
						message: `Role ${response.name || response.data.name} was deleted.`,
					});
				});
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Delete Failed",
					message: "Failed to delete role",
				});
			} finally {
				this.deleting = false;
			}
		},
	},
};
</script>
