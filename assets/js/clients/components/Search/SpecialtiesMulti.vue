<template>
	<div style="display: flex">
		<multiselect
			v-model="selectedEntities"
			id="ajax"
			:label="displayField"
			track-by="id"
			placeholder="Search specialties..."
			open-direction="bottom"
			:options="entities"
			:multiple="true"
			:searchable="true"
			:loading="searching"
			:internal-search="false"
			:preserve-search="false"
			:clear-on-select="true"
			:close-on-select="false"
			:options-limit="300"
			:limit="10"
			:limit-text="limitText"
			:max-height="600"
			:show-no-results="false"
			:hide-selected="true"
			@search-change="search"
			@close="close"
		>
			<template #clear="props">
				<div
					class="multiselect__clear"
					v-if="selectedEntities.length"
					@mousedown.prevent.stop="clearAll(props.search)"
				></div>
			</template>
			<span slot="noResult">No specialty found.</span>
		</multiselect>
	</div>
</template>

<script type="text/javascript">
export default {
	name: "SpecialtySearchMulti",
	props: {
		value: {
			type: Array,
			default: () => [],
		},
		displayField: {
			type: String,
			default: "name",
		},
	},
	data() {
		return {
			// Currently searching status
			searching: false,
			// Selected entity
			selectedEntities: [],
			// Typeahead results
			entities: [],
		};
	},
	mounted() {
		if (this.value && this.value.length > 0) {
			this.selectedEntities = this.value;
		}
	},
	watch: {
		selectedEntities(newEntities) {
			if (newEntities) {
				this.$emit("input", newEntities);
			}
		},
		value(newEntities) {
			this.selectedEntities = newEntities;
		},
	},
	methods: {
		/**
		 * Selected a result from the list
		 */
		async search(query) {
			if (query == "") {
				return false;
			}

			try {
				this.searching = true;

				const response = await this.$store.dispatch("specialties/index", {
					search: query,
				});

				this.entities = response.data;
				this.$emit("results", response.data);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Error getting specialties list",
				});

				this.$emit("error", e.response.data);
			} finally {
				this.searching = false;
			}
		},
		/**
		 * Multi-select methods
		 */
		limitText(count) {
			return `and ${count} other specialties`;
		},
		clearAll() {
			this.selectedEntities = [];
		},
		close() {
			this.entities = [];
		},
	},
};
</script>
