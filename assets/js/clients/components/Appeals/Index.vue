<template>
	<paginated-results
		v-slot="{
			doSort,
			doSearch,
			empty,
			hasNextPage,
			hasPrevPage,
			loading,
			nextPage,
			prevPage,
			refresh,
			results,
			total,
		}"
		v-bind="{
			action,
			filters,
			search: localSearch,
		}"
	>
		<b-container fluid class="my-2 px-2">
			<b-row>
				<b-col cols="7" lg="5" class="text-left">
					<b-form @submit.prevent="doSearch" class="mx-0">
						<search-input v-model="localSearch" v-bind="{ loading, autofocus }" />
					</b-form>
				</b-col>

				<b-col cols="5" lg="7" class="text-right">
					<simple-pagination v-bind="{ loading, nextPage, prevPage, hasPrevPage, hasNextPage }" />
				</b-col>
			</b-row>
		</b-container>

		<b-container v-if="loading && empty" class="my-4">
			<loading-indicator size="4x" class="my-5" />
		</b-container>
		<div v-else-if="!empty">
			<ResultTable
				:data="results"
				:loading="loading"
				:hide-facility="hideFacility"
				:hide-insurance-provider="hideInsuranceProvider"
				:hide-patient="hidePatient"
				@clicked="clickedRow"
				@sorted="doSort"
			/>
		</div>
		<b-container fluid v-else class="my-4">
			<empty-result>
				No appeals found
				<template #content>
					<slot name="empty-content">
						<p>{{ emptyDescription }}</p>
					</slot>
				</template>
			</empty-result>
		</b-container>
	</paginated-results>
</template>

<script>
import { getIndex } from "@/clients/services/appeals";
import ResultTable from "@/clients/components/Appeals/Table.vue";

export default {
	name: "AppealIndex",
	components: {
		ResultTable,
	},
	props: {
		hideFacility: {
			type: Boolean,
			default: false,
		},
		hideInsuranceProvider: {
			type: Boolean,
			default: false,
		},
		hidePatient: {
			type: Boolean,
			default: false,
		},
		filters: {
			type: Object,
			default: () => {
				return {};
			},
		},
		search: {
			type: String,
			default: "",
		},
		emptyDescription: {
			type: String,
			default: "No appeals were found.",
		},
		autofocus: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			localSearch: this.search,
			action: getIndex,
		};
	},
	methods: {
		clickedRow(row) {
			this.$emit("clicked", row);
		},
	},
	watch: {
		localSearch(val) {
			this.$emit("update:search", val);
		},
	},
};
</script>
