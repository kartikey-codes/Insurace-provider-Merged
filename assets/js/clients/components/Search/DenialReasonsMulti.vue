<template>
	<div style="display: flex">
		<multiselect
			v-model="selectedEntities"
			id="ajax"
			:label="displayField"
			track-by="id"
			placeholder="Search denial reasons..."
			open-direction="bottom"
			:options="entities"
			:multiple="true"
			:searchable="false"
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
			<!--
			<template #tag="{ option, remove }">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<span v-if="option[displayField]" class="mb-0">
							<span>{{ option[displayField] }}</span>
							<span class="custom__remove" @click="remove(option)">❌</span>
						</span>
						<span v-else class="text-danger">
							<font-awesome-icon icon="exclamation-triangle"/>Missing Name
							<span class="custom__remove" @click="remove(option)">❌</span>
						</span>
					</div>
				</div>
			</template>
			-->
			<template #clear="props">
				<div
					class="multiselect__clear"
					v-if="selectedEntities.length"
					@mousedown.prevent.stop="clearAll(props.search)"
				></div>
			</template>
			<span slot="noResult">No denial reason found.</span>
		</multiselect>
		<b-button variant="primary" @click="add">
			<font-awesome-icon icon="plus" />
		</b-button>
	</div>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "DenialReasonSearchMulti",
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
			//entities: [],
		};
	},
	computed: mapGetters({
		entities: "denialReasons/all",
	}),
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
		 * Add a new item entity
		 */
		add() {
			this.$emit("add");
		},
		/**
		 * Selected a result from the list
		 */
		async search(query) {
			// All reasons stored in vuex
		},
		/**
		 * Multi-select methods
		 */
		limitText(count) {
			return `and ${count} other denial reasons`;
		},
		clearAll() {
			this.selectedEntities = [];
		},
		close() {
			//this.entities = [];
		},
	},
};
</script>
