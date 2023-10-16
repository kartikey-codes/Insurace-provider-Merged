<template>
	<div>
		<page-header>
			<template #title>Users</template>
			<template #buttons>
				<b-button variant="primary" :to="{ name: 'users.add' }" title="Add User">
					<font-awesome-icon icon="plus" fixed-width />
					<span>Add New</span>
				</b-button>
			</template>
		</page-header>

		<b-card no-body class="my-4">
			<b-tabs card active-nav-item-class="font-weight-bold" lazy>
				<b-tab lazy>
					<template #title>
						<div class="d-flex justify-content-between align-items-center">
							<span>Active</span>
						</div>
					</template>
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
							action,
							filters: {
								active: true,
							},
							search: activeSearch,
							perPage,
						}"
					>
						<b-row class="mb-4">
							<b-col cols="7" md="6" lg="6" class="mb-0">
								<b-form @submit.prevent="doSearch">
									<search-input v-model="activeSearch" v-bind="{ loading }" />
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
								:to="{ name: 'users.view', params: { id: result.id } }"
								:variant="result.active ? '' : 'light'"
							>
								<div class="py-2 d-flex justify-start align-items-top">
									<b-avatar
										rounded
										:variant="result.active ? 'primary' : 'light'"
										class="mr-3 px-0 text-center"
									>
										<font-awesome-icon icon="user" class="px-0 mx-0" />
									</b-avatar>
									<b-row class="flex-fill">
										<b-col cols="12" class="text-left">
											<h6 class="h6 font-weight-bold mb-1">
												{{ result.full_name }}
											</h6>
											<p v-if="result.email" class="small mb-1 text-muted" title="Email">
												<font-awesome-icon
													icon="envelope"
													fixed-width
													class="d-none d-sm-inline"
												/>
												{{ result.email }}
											</p>
											<p
												v-if="result.roles && result.roles.length > 0"
												class="small mb-1 text-muted"
												title="Roles"
											>
												<font-awesome-icon
													icon="graduation-cap"
													fixed-width
													class="d-none d-sm-inline"
												/>
												{{ result.roles.map((role) => role.name).join(", ") }}
											</p>
											<div>
												<b-badge pill variant="primary" v-if="result.client_admin">
													Admin
												</b-badge>
												<b-badge pill variant="light" v-if="!result.active"> Inactive </b-badge>
											</div>
										</b-col>
									</b-row>
								</div>
							</b-list-group-item>
						</b-list-group>
						<empty-result v-else> No active users found </empty-result>
					</paginated-results>
				</b-tab>
				<b-tab lazy>
					<template #title>
						<div class="d-flex justify-content-between align-items-center">
							<span>Inactive</span>
						</div>
					</template>
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
							action,
							filters: {
								active: false,
							},
							search: inactiveSearch,
							perPage,
						}"
					>
						<b-row class="mb-4">
							<b-col cols="7" md="6" lg="6" class="mb-0">
								<b-form @submit.prevent="doSearch">
									<search-input v-model="inactiveSearch" v-bind="{ loading }" />
								</b-form>
							</b-col>
							<b-col cols="5" md="6" lg="6" class="text-right">
								<simple-pagination v-bind="{ loading, nextPage, prevPage, hasPrevPage, hasNextPage }" />
							</b-col>
						</b-row>

						<loading-indicator v-if="loading && empty" size="4x" class="my-5" />
						<div v-else-if="!empty">
							<b-list-group class="shadow-sm mb-0">
								<b-list-group-item
									v-for="result in results"
									:key="result.id"
									:to="{ name: 'users.view', params: { id: result.id } }"
									:variant="result.active ? '' : 'light'"
									class="py-2"
								>
									<div class="py-2 d-flex justify-start align-items-top">
										<b-avatar
											rounded
											:variant="result.active ? 'primary' : 'light'"
											class="mr-3 px-0 text-center"
										>
											<font-awesome-icon icon="user" class="px-0 mx-0" />
										</b-avatar>
										<b-row class="flex-fill">
											<b-col cols="12" class="text-left">
												<h6 class="h6 font-weight-bold mb-1">
													{{ result.full_name }}
												</h6>
												<p v-if="result.email" class="small mb-1 text-muted" title="Email">
													<font-awesome-icon
														icon="envelope"
														fixed-width
														class="d-none d-sm-inline"
													/>
													{{ result.email }}
												</p>
												<p
													v-if="result.roles && result.roles.length > 0"
													class="small mb-1 text-muted"
													title="Roles"
												>
													<font-awesome-icon
														icon="graduation-cap"
														fixed-width
														class="d-none d-sm-inline"
													/>
													{{ result.roles.map((role) => role.name).join(", ") }}
												</p>
												<div>
													<b-badge pill variant="primary" v-if="result.client_admin">
														Admin
													</b-badge>
													<b-badge pill variant="light" v-if="!result.active">
														Inactive
													</b-badge>
												</div>
											</b-col>
										</b-row>
									</div>
								</b-list-group-item>
							</b-list-group>
						</div>
						<empty-result v-else>
							No inactive users found
							<template #content> Inactive users are unable to log in</template>
						</empty-result>
					</paginated-results>
				</b-tab>
			</b-tabs>
		</b-card>
	</div>
</template>

<script setup>
import { ref, reactive } from "vue";
import { getIndex } from "@/clients/services/users";

const activeSearch = ref("");
const inactiveSearch = ref("");

const perPage = ref(15);
const action = getIndex;
</script>
