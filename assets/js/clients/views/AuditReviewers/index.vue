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
			filters,
			search,
			perPage,
		}"
	>
		<page-header v-bind="{ loading, total }">
			<template #title>Audit Reviewers</template>
			<template #buttons>
				<b-button variant="primary" :to="{ name: 'auditReviewers.add' }" title="Add New">
					<font-awesome-icon icon="plus" fixed-width />
					<span>Add New</span>
				</b-button>
			</template>
		</page-header>

		<b-row class="mt-4">
			<b-col cols="12" md="9" lg="4" order="1" class="mb-4 mb-lg-0">
				<b-form @submit.prevent="doSearch">
					<search-input v-model="search" v-bind="{ loading }" />
				</b-form>
			</b-col>
			<b-col cols="6" md="3" lg="2" order="2" class="text-left text-md-right text-lg-left">
				<b-dropdown split @click="filtering = !filtering" :pressed="filtering">
					<template #button-content>
						<font-awesome-icon icon="filter" fixed-width />
						<span>Filter</span>
					</template>
					<b-dropdown-item-button @click="resetFilters" :disabled="loading" title="Clear all filters">
						<span>Clear Filters</span>
					</b-dropdown-item-button>
					<b-dropdown-item-button @click="refresh" :disabled="loading" title="Refresh">
						<span>Refresh</span>
					</b-dropdown-item-button>
				</b-dropdown>
			</b-col>
			<b-col cols="6" md="12" lg="6" order="3" class="text-right">
				<simple-pagination v-bind="{ loading, nextPage, prevPage, hasPrevPage, hasNextPage }" />
			</b-col>
		</b-row>
		<b-row>
			<b-col cols="12">
				<b-collapse v-model="filtering" class="py-2">
					<b-form @submit.prevent="doSearch">
						<b-card>
							<index-filters v-model="filters" :disabled="loading" />
						</b-card>
					</b-form>
				</b-collapse>
			</b-col>
		</b-row>

		<b-row class="my-4">
			<b-col cols="12" class="mb-4">
				<loading-indicator v-if="loading && empty" size="4x" class="my-5" />
				<div v-else-if="!empty">
					<b-list-group class="shadow-sm">
						<b-list-group-item
							v-for="result in results"
							:key="result.id"
							:to="{ name: 'auditReviewers.view', params: { id: result.id } }"
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
										<p v-if="result.agency?.name" class="small mb-1 text-muted" title="Agency">
											<font-awesome-icon icon="building" fixed-width class="d-none d-sm-inline" />
											{{ result.agency.name }}
										</p>
										<div>
											<b-badge pill variant="light" v-if="!result.active"> Inactive </b-badge>
										</div>
									</b-col>
								</b-row>
							</div>
						</b-list-group-item>
					</b-list-group>
				</div>
				<empty-result v-else>
					No audit reviewers found
					<template #content> No audit reviewers created or match your search. </template>
				</empty-result>
			</b-col>
		</b-row>
	</paginated-results>
</template>

<script setup>
import { ref, reactive } from "vue";
import { getIndex } from "@/clients/services/auditReviewers";

import IndexFilters from "@/clients/components/AuditReviewers/Filters.vue";

const defaultFilters = {
	active: null,
	agency_id: null,
};

const filters = reactive({ ...defaultFilters });
const resetFilters = () => Object.assign(filters, defaultFilters);

const filtering = ref(false);
const search = ref("");
const perPage = ref(15);
const action = getIndex;
</script>
