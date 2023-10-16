<template>
	<b-table
		hover
		striped
		bordered
		responsive
		no-local-sorting
		v-bind="$attrs"
		v-on="$listeners"
		:busy="loading"
		:fields="fields"
		:items="data"
		:sort-by="sort"
		:sort-desc="sortDescending"
		@row-clicked="clicked"
		@sort-changed="sortChanged"
		class="mb-0 cursor-pointer nowrap"
	>
		<template v-for="(_, slot) of $scopedSlots" v-slot:[slot]="scope">
			<slot :name="slot" v-bind="scope" />
		</template>
	</b-table>
</template>

<script type="text/javascript">
export default {
	name: "DataTable",
	components: {},
	props: {
		data: {
			type: Array,
			default: () => [],
		},
		emptyDescription: {
			type: String,
			default: "Nothing found.",
		},
		fields: {
			type: Array,
			default: () => [],
		},
		loading: {
			type: Boolean,
			default: false,
		},
		sort: {
			type: [String],
			default: "",
		},
		sortAliases: {
			type: Object,
			default: () => {
				return {};
			},
		},
		sortDescending: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		direction() {
			return this.sortDescending ? "desc" : "asc";
		},
		empty() {
			return !this.data || !this.data.length || this.data.length <= 0;
		},
		showLoading() {
			return this.empty && this.loading;
		},
	},
	methods: {
		clicked(row) {
			this.$emit("clicked", row);
		},
		sortChanged(params) {
			/**
			 * params: {
			 *   apiUrl: undefined,
			 *   currentPage: 1,
			 *   filter: undefined,
			 *   perPage: 0,
			 *   sortBy: "id",
			 *   sortDesc: false
			 * }
			 */

			// Allow aliasing sort fields for API
			const sortBy = this.sortAliases[params.sortBy] ? this.sortAliases[params.sortBy] : params.sortBy;

			// Update descending boolean
			const sortDesc = params.sortDesc;

			// Update props
			this.$emit("update:sort", sortBy);
			this.$emit("update:sort-descending", sortDesc);

			// Single sorted event
			this.$emit("sorted", {
				sort: sortBy,
				sortDescending: sortDesc,
			});
		},
	},
};
</script>
