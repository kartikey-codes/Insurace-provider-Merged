<template>
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
			search,
			perPage,
		}"
	>
		<page-header v-bind="{ loading, total }">
			<template #title>Roles</template>
			<template #buttons>
				<b-button variant="primary" :to="{ name: 'roles.add' }" title="Add New">
					<font-awesome-icon icon="plus" fixed-width />
					<span>Add New</span>
				</b-button>
			</template>
		</page-header>

		<b-row class="mt-4" v-if="pages > 1">
			<b-col cols="7" md="6" lg="6" class="mb-0">
				<b-form @submit.prevent="doSearch">
					<search-input v-model="search" v-bind="{ loading }" />
				</b-form>
			</b-col>
			<b-col cols="5" md="6" lg="6" class="text-right">
				<simple-pagination v-bind="{ loading, nextPage, prevPage, hasPrevPage, hasNextPage }" />
			</b-col>
		</b-row>

		<b-row class="my-4">
			<b-col cols="12" class="mb-4">
				<loading-indicator v-if="loading && empty" size="4x" class="my-5" />
				<b-list-group v-else-if="!empty" class="shadow-sm">
					<b-list-group-item
						v-for="result in results"
						:key="result.id"
						:to="{ name: 'roles.view', params: { id: result.id } }"
						:variant="result.member_names !== '' ? '' : 'light'"
					>
						<div class="py-2 d-flex justify-start align-items-top">
							<b-avatar
								rounded
								:variant="result.member_names !== '' ? 'primary' : 'light'"
								class="mr-3 px-0 text-center"
							>
								<font-awesome-icon icon="building" class="px-0 mx-0" />
							</b-avatar>
							<b-row class="flex-fill">
								<b-col cols="12" class="text-left">
									<h6 class="h6 font-weight-bold mb-1">
										{{ result.name }}
									</h6>
									<p v-if="result.member_names" class="small mb-1 text-muted" title="Members">
										<font-awesome-icon icon="users" fixed-width class="d-none d-sm-inline" />
										{{ result.member_names }}
									</p>
									<p v-else class="small mb-1 text-muted">Empty</p>
								</b-col>
							</b-row>
						</div>
					</b-list-group-item>
				</b-list-group>
				<empty-result v-else>
					No roles found
					<template #content> No roles match your search. </template>
				</empty-result>
			</b-col>
		</b-row>
	</paginated-results>
</template>

<script setup>
import { ref, reactive } from "vue";
import { getIndex } from "@/clients/services/roles";

const search = ref("");
const perPage = ref(15);
const action = getIndex;
</script>
