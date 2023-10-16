<template>
	<paginated-results
		v-slot="{ doSearch, empty, hasNextPage, hasPrevPage, loading, nextPage, prevPage, refresh, results }"
		v-bind="{
			action,
			filters: {
				status: 'CANCELLED',
			},
			search: cancelledSearch,
			perPage,
		}"
	>
		<b-row class="mb-4">
			<b-col cols="6" md="6" lg="6" class="mb-0">
				<b-form @submit.prevent="doSearch">
					<search-input v-model="cancelledSearch" v-bind="{ loading }" />
				</b-form>
			</b-col>
			<b-col cols="6" md="6" lg="6" class="text-right">
				<simple-pagination v-bind="{ loading, nextPage, prevPage, hasPrevPage, hasNextPage }" />
			</b-col>
		</b-row>
		<b-row>
			<b-col cols="12">
				<loading-indicator v-if="loading && empty" size="4x" class="my-5" />
				<div v-else-if="!empty">
					<outgoing-document-list-item
						v-for="result in results"
						:key="result.id"
						:value="result"
						@updated="refresh"
						class="mb-2 shadow-sm"
					/>
				</div>
				<empty-result v-else icon="envelope">
					No cancelled documents
					<template #content> Cancelled documents are removed from the outgoing queue. </template>
				</empty-result>
			</b-col>
		</b-row>
	</paginated-results>
</template>

<script setup>
import { ref } from "vue";
import { getIndex } from "@/clients/services/outgoingDocuments";
import OutgoingDocumentListItem from "@/clients/components/OutgoingDocuments/ListItem.vue";
import store from "@/clients/store";

// Search queries for various statuses
const cancelledSearch = ref("");

const perPage = ref(15);
const action = getIndex;

const recount = () => {
	store.dispatch("updateState");
};
</script>
