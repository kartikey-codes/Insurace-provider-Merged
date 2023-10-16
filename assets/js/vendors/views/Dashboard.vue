<template>
	<div>
		<page-header>
			<template #title>Vendor Dashboard</template>
			<template #buttons>
				<b-button variant="light" @click="showFilters = !showFilters" :pressed="showFilters">
					<font-awesome-icon icon="filter" fixed-width class="mr-1" />
					<span>Filter</span>
				</b-button>

				<b-dropdown variant="light" right title="More Options">
					<template #button-content>
						<font-awesome-icon icon="cog" fixed-width />
					</template>

					<b-dropdown-item @click="refresh" :disabled="loading">
						<font-awesome-icon icon="sync" :spin="loading" fixed-width />
						<span>Refresh</span>
					</b-dropdown-item>

					<b-dropdown-divider />

					<b-dropdown-item @click="resetFilters">
						<font-awesome-icon icon="times" fixed-width />
						<span>Clear Filters</span>
					</b-dropdown-item>
				</b-dropdown>
			</template>
			<template #search>
				<b-input-group>
					<b-input-group-prepend is-text>
						<font-awesome-icon icon="search" />
					</b-input-group-prepend>
					<b-form-input
						ref="generalSearch"
						v-model="searchText"
						:class="{ loading: loading }"
						id="generalSearch"
						type="text"
						placeholder="Search reviews..."
						autocomplete="off"
						class="ml-auto"
						autofocus
					/>
				</b-input-group>
			</template>
			<template #total>
				<span v-if="!loading">{{ $filters.formatNumber(total) }}</span>
				<loading-indicator v-else size="1x" />
			</template>
		</page-header>

		<b-collapse v-model="showFilters" id="collapseFilters">
			<b-container fluid class="bg-light py-0 mb-4">
				<appeal-filters v-model="filters" />
			</b-container>
		</b-collapse>

		<appeal-index
			ref="list"
			:filters="filters"
			:loading.sync="loading"
			@refreshed="refreshed"
			@clicked="view"
			@sorted="sorted"
		/>
	</div>
</template>

<script type="text/javascript">
import { debounce } from "lodash";
import store from "@/vendors/store";
import AppealFilters from "@/vendors/components/Appeals/Filters.vue";
import AppealIndex from "@/vendors/components/Appeals/Index.vue";

export default {
	name: "Dashboard",
	components: {
		AppealFilters,
		AppealIndex,
	},
	data() {
		return {
			showFilters: false,
			loading: true,
			total: false,
			searchText: null,
		};
	},
	beforeRouteEnter(to, from, next) {
		if (Object.keys(to.params).length > 0) {
			store.commit("setAppealFilters", to.params);
		}

		if (Object.keys(to.query).length > 0) {
			store.commit("setAppealFilters", to.query);
		}

		next((vm) => {
			next();
		});
	},
	beforeRouteUpdate(to, from, next) {
		if (Object.keys(to.params).length > 0) {
			store.commit("setAppealFilters", to.params);
		}

		if (Object.keys(to.query).length > 0) {
			store.commit("setAppealFilters", to.query);
		}

		next((vm) => {
			next();
		});
	},
	mounted() {
		// Show filters if we have 1 or more values
		if (Object.keys(this.filters).length > 0) {
			//this.$set(this, 'showFilters', true);
		}

		// if (this.$refs.generalSearch) {
		//  this.$refs.generalSearch.focus();
		// }
	},
	computed: {
		isFiltered() {
			if (Object.keys(this.filters).length > 0) {
				return true;
			}

			return false;
		},
		// Get and set filters back to vuex
		filters: {
			get() {
				var filters = this.$store.state.appealFilters;
				filters.search = this.searchText;

				return this.$store.state.appealFilters;
			},
			set(newValue) {
				this.$store.commit("setAppealFilters", newValue);
			},
		},
	},
	methods: {
		view(entity) {
			this.$router.push({
				name: "cases.view",
				params: {
					id: entity.case_id,
					appeal_id: entity.id,
				},
			});
		},
		resetFilters() {
			this.filters = {};
		},
		refresh() {
			this.$refs.list.refresh();
		},
		refreshed(data, pagination) {
			this.total = pagination.count || false;
		},
		sorted(field, direction) {
			// We aren't storing this anywhere yet
			console.log("Sorted", field, direction);
		},
		debounceSearch: debounce(function (e) {
			this.refresh();
		}, 500),
	},
	watch: {
		searchText: function (value) {
			return this.debounceSearch();
		},
		filters: {
			immediate: false,
			deep: true,
			handler(val) {
				this.$nextTick(() => this.debounceSearch());
			},
		},
	},
};
</script>
