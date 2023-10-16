<template>
	<b-form @submit.prevent="formSubmitted" class="my-0 py-0 position-relative">
		<b-input-group v-bind="$attrs">
			<template #prepend>
				<b-input-group-text>
					<font-awesome-icon v-bind="{ icon }" :spin="searching" />
				</b-input-group-text>
			</template>

			<b-form-input
				v-if="!hasSelected"
				ref="queryInput"
				v-model="query"
				v-bind="{ autofocus, debounce, disabled, placeholder }"
			/>
			<b-form-input v-else readonly :value="displayName" />

			<b-input-group-append v-if="hasSelected">
				<b-button :variant="clearVariant" @click="clear" v-bind="{ disabled }" :title="clearTitle">
					<font-awesome-icon :icon="clearIcon" fixed-width />
				</b-button>
			</b-input-group-append>
			<b-input-group-append v-if="canAdd">
				<b-button :variant="addVariant" @click="add" v-bind="{ disabled }" :title="addTitle">
					<font-awesome-icon :icon="addIcon" fixed-width />
				</b-button>
			</b-input-group-append>
		</b-input-group>

		<transition name="fade" :duration="{ enter: 500, leave: 250 }">
			<slot v-if="hasSelected" name="selected" v-bind="{ entity, fetching }"> </slot>
			<slot v-else-if="searching && empty" name="searching"> </slot>
			<slot v-else-if="empty && !queryEmpty" name="empty"> </slot>
			<div v-else-if="!hasSelected">
				<slot name="results" v-bind="{ results, searching, selectResult }"> </slot>

				<div v-if="hasPages" class="mt-2 text-right">
					<slot
						name="pagination"
						v-bind="{ loading: searching, prevPage, hasPrevPage, nextPage, hasNextPage }"
					>
						<simple-pagination
							v-bind="{ loading: searching, prevPage, hasPrevPage, nextPage, hasNextPage }"
						/>
					</slot>
				</div>
			</div>
		</transition>
	</b-form>
</template>

<script>
import { debounce } from "lodash";

export default {
	name: "EntityTypeahead",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					id: null,
				};
			},
		},
		autofocus: {
			type: Boolean,
			default: false,
		},
		clearable: {
			type: Boolean,
			default: true,
		},
		debounce: {
			type: Number,
			default: 100,
		},
		disabled: {
			type: Boolean,
			default: false,
		},
		displayField: {
			type: String,
			default: "name",
		},
		placeholder: {
			type: String,
			default: "Search...",
		},
		disabled: {
			type: Boolean,
			default: false,
		},
		defaultQuery: {
			type: String,
			default: "",
		},
		searchingIcon: {
			type: String,
			default: "circle-notch",
		},
		searchIcon: {
			type: String,
			default: "search",
		},
		selectedIcon: {
			type: String,
			default: "info-circle",
		},
		canAdd: {
			type: Boolean,
			default: false,
		},
		addIcon: {
			type: String,
			default: "plus",
		},
		addTitle: {
			type: String,
			default: "Add New",
		},
		addVariant: {
			type: String,
			default: "primary",
		},
		clearIcon: {
			type: String,
			default: "remove",
		},
		clearTitle: {
			type: String,
			default: "Clear",
		},
		clearVariant: {
			type: String,
			default: "secondary",
		},
		emptyDescription: {
			type: String,
			default: "No results found",
		},
		indexAction: {
			type: String,
			default: "",
			required: true,
		},
		getAction: {
			type: String,
			default: "",
			required: true,
		},
	},
	data() {
		return {
			// Pagination
			pagination: {
				count: 0,
				pageCount: 1,
				page: 1,
				perPage: 5,
				prevPage: false,
				nextPage: false,
			},
			page: 1,

			// Query
			query: this.defaultQuery,

			// Currently fetching result status
			fetching: false,

			// Has searched
			searched: false,

			// Currently searching status
			searching: false,

			// Selected entity
			entity: {
				id: this.value.id || null,
			},

			// List of results
			results: [],
		};
	},
	computed: {
		icon() {
			if (this.hasSelected) {
				return this.selectedIcon;
			}

			if (this.searching) {
				return this.searchingIcon;
			}

			return this.searchIcon;
		},
		hasSelected() {
			return this.entity && this.entity.id ? true : false;
		},
		displayName() {
			return this.entity && this.entity[this.displayField] ? this.entity[this.displayField] : "";
		},
		description() {
			if (this.empty) {
				return this.emptyDescription;
			} else {
				return "";
			}
		},
		empty() {
			return this.results.length <= 0 && this.searched == true;
		},
		queryEmpty() {
			return this.query == "";
		},
		hasNextPage() {
			return this.pagination.nextPage || false;
		},
		hasPrevPage() {
			return this.pagination.prevPage || false;
		},
		hasPages() {
			return this.pagination.pageCount > 1;
		},
	},
	methods: {
		/**
		 * User typed into the query
		 */
		queryChanged() {
			// nothing
		},
		/**
		 * Add new entity
		 */
		add() {
			this.$emit("add");
		},
		/**
		 * Clear result/input
		 */
		clear() {
			this.entity = {
				id: null,
			};

			this.$nextTick(function () {
				if (this.$refs.queryInput) {
					this.$refs.queryInput.focus();
				}
			});

			this.$emit("cleared");
		},
		/**
		 * Form submitted (enter key on query)
		 */
		formSubmitted() {
			if (this.results.length == 1) {
				const firstResult = this.results[0];
				this.selectResult(firstResult);
			} else {
				this.searchDebounce(this.query);
			}
		},
		/**
		 * Get single result by primary key
		 */
		async getResult(id) {
			try {
				this.fetching = true;

				const response = await this.$store.dispatch(this.getAction, {
					id: id,
				});

				this.$emit("input", response);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Error getting patient details",
				});

				this.$emit("error", e.response.data);
			} finally {
				this.fetching = false;
			}
		},
		/**
		 * Select from outside of component (slot)
		 */
		selectResult(entity) {
			this.entity = entity;
		},
		/**
		 * Selected a result from the list
		 */
		searchDebounce: debounce(function (query) {
			this.search(query);
		}, 300),
		async search(query) {
			if (query == "") {
				this.searched = false;
				return false;
			}

			try {
				this.searching = true;

				const response = await this.$store.dispatch(this.indexAction, {
					search: query,
					page: this.page,
					limit: this.pagination.perPage,
				});

				this.pagination = response.pagination;
				this.results = response.data;
				this.$emit("results", response.data);
				this.searched = true;
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Search Error",
					message: "A search error occurred.",
				});

				this.$emit("error", e.response.data);
			} finally {
				this.searching = false;
			}
		},
		prevPage() {
			if (this.hasPrevPage) {
				this.page--;
			}
		},
		nextPage() {
			if (this.hasNextPage) {
				this.page++;
			}
		},
		resetInput() {
			this.pagination = {
				count: 0,
				pageCount: 1,
				page: 1,
				perPage: 15,
				prevPage: false,
				nextPage: false,
			};
			this.results = [];

			this.searching = false;
			this.query = "";
		},
	},
	watch: {
		query(newQuery) {
			if (newQuery === "") {
				this.resetInput();
				return;
			}

			this.searchDebounce(newQuery);
		},
		value: {
			immediate: false,
			deep: true,
			handler(val) {
				this.entity = val;
			},
		},
		entity(newEntity) {
			if (newEntity && newEntity.id) {
				this.$emit("input", newEntity);
			}
		},
		page(val) {
			this.search(this.query);
		},
	},
};
</script>
