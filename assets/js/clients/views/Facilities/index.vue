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
			<template #title>Facilities</template>
			<template #buttons>
				<b-dropdown right variant="primary">
					<template #button-content>
						<font-awesome-icon icon="plus" fixed-width />
						<span>Add New</span>
					</template>
					<b-dropdown-item :to="{ name: 'facilities.add' }" title="Create New Facility">
						<font-awesome-icon icon="pencil" fixed-width />
						<span>Create New Facility</span>
					</b-dropdown-item>
					<b-dropdown-item :to="{ name: 'facilities.add.npi' }" title="Add New Facility from NPI Registry">
						<font-awesome-icon icon="search" fixed-width />
						<span>Search NPI Registry</span>
					</b-dropdown-item>
				</b-dropdown>
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
							<IndexFilters v-model="filters" :disabled="loading" />
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
							:to="{ name: 'facilities.view', params: { id: result.id } }"
							:variant="result.active ? '' : 'light'"
						>
							<div class="py-2 d-flex justify-start align-items-top">
								<b-avatar
									rounded
									:variant="result.active ? 'primary' : 'light'"
									class="mr-3 px-0 text-center"
								>
									<font-awesome-icon icon="building" class="px-0 mx-0" />
								</b-avatar>
								<b-row class="flex-fill">
									<b-col cols="12" class="text-left">
										<h6 class="h6 font-weight-bold mb-1">
											{{ result.name }}
										</h6>
										<p v-if="result.chain_name" class="small mb-1 text-muted" title="Chain">
											<font-awesome-icon icon="link" fixed-width class="d-none d-sm-inline" />
											{{ result.chain_name }}
										</p>

										<p v-if="result.full_address" class="small mb-1 text-muted" title="Facility">
											<font-awesome-icon
												icon="location-dot"
												fixed-width
												class="d-none d-sm-inline"
											/>
											{{ result.full_address }}
										</p>
										<div>
											<b-badge variant="light" v-if="!result.active"> Inactive </b-badge>
											<b-badge variant="light" v-if="result.facility_type">
												{{ result.facility_type.name }}
											</b-badge>
										</div>
									</b-col>
								</b-row>
							</div>
						</b-list-group-item>
					</b-list-group>
				</div>
				<empty-result v-else>
					No facilities found
					<template #content> No facilities have been created or match your search. </template>
				</empty-result>
			</b-col>
		</b-row>
	</paginated-results>
</template>

<script setup>
import { ref, reactive } from "vue";
import { getIndex } from "@/clients/services/facilities";

import IndexFilters from "@/clients/components/Facilities/Filters.vue";

const defaultFilters = {
	active: null,
	facility_type_id: null,
	state: null,
	client_owned: null,
	has_contract: null,
};

const filters = reactive({ ...defaultFilters });
const resetFilters = () => Object.assign(filters, defaultFilters);

const filtering = ref(false);
const search = ref("");
const perPage = ref(15);
const action = getIndex;
</script>
