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
			sort,
			sortDescending,
		}"
	>
		<b-container fluid class="mt-4">
			<b-row>
				<b-col cols="12" md="12" lg="4" order="1" class="mb-4 mb-lg-0">
					<b-form @submit.prevent="doSearch">
						<search-input v-model="search" v-bind="{ loading }" />
					</b-form>
				</b-col>
				<b-col cols="6" md="6" lg="2" order="2" class="text-left text-md-left text-lg-left">
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
				<b-col cols="6" md="6" lg="6" order="3" class="text-right">
					<simple-pagination v-bind="{ loading, nextPage, prevPage, hasPrevPage, hasNextPage }" />
				</b-col>
			</b-row>
		</b-container>

		<b-container fluid>
			<b-row>
				<b-col cols="12">
					<b-collapse v-model="filtering" class="py-2">
						<b-form @submit.prevent="doSearch">
							<b-card>
								<IndexFilters v-model="filters" :disabled="loading" hide-status />
							</b-card>
						</b-form>
					</b-collapse>
				</b-col>
			</b-row>
		</b-container>

		<b-container v-if="loading && empty" class="my-4">
			<loading-indicator size="4x" class="my-5" />
		</b-container>
		<b-container fluid v-else-if="!empty" class="mt-4">
			<b-row>
				<b-col cols="12" class="mb-4">
					<ResultTable
						:data="results"
						:loading="loading"
						:sort.sync="sort"
						:sort-descending.sync="sortDescending"
						@clicked="toView"
					/>
				</b-col>
			</b-row>
		</b-container>
		<b-container fluid v-else class="my-4">
			<empty-result>
				No UTC requests found
				<template #content> No unable-to-complete requests have been created or match your search. </template>
			</empty-result>
		</b-container>
	</paginated-results>
</template>

<script setup>
import { ref, reactive } from "vue";
import { useRouter } from "vue-router/composables";
import { getIndex } from "@/clients/services/caseRequests";

import ResultTable from "@/clients/components/CaseRequests/Table.vue";
import IndexFilters from "@/clients/components/CaseRequests/Filters.vue";

const defaultFilters = {
	request_type: null,
	insurance_provider_id: null,
	agency_id: null,
	assigned_to: null,
	priority: null,
};

const filters = reactive({ status: "UTC", ...defaultFilters });
const filtering = ref(false);
const resetFilters = () => Object.assign(filters, defaultFilters);

const router = useRouter();

const toView = function (entity) {
	router.push({
		name: "caseRequests.view",
		params: {
			id: entity.case_id,
			case_request_id: entity.id,
		},
	});
};

const action = getIndex;
const search = ref("");
const sort = ref("created");
const sortDescending = ref(true);
const perPage = ref(15);
</script>
