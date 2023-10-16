<template>
	<div>
		<TypeAhead
			ref="typeAhead"
			v-model="query"
			@hit="selectedResult"
			@input="queryChanged"
			:min-matching-chars="0"
			:disabled="disabled"
			:required="required"
			:input-class="inputClasses"
			:data="entities"
			:serializer="formatResults"
			:placeholder="placeholder"
			show-all-results
		>
			<template #suggestion="{ data, htmlText }">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<span v-if="data[displayField]" class="mb-0">
							<span v-if="htmlText">
								<span v-html="htmlText"></span>
							</span>
							<span v-else>{{ data[displayField] }}</span>

							<div class="small text-muted">
								<span v-if="data && data.date_of_birth">{{
									$filters.formatDate(data.date_of_birth)
								}}</span>
								<span v-else>
									<font-awesome-icon icon="exclamation-triangle" fixed-width />
									<span>No Date Of Birth</span>
								</span>
							</div>
						</span>
						<span v-else class="text-danger">
							<font-awesome-icon icon="exclamation-triangle" fixed-width />
							<span>Missing Name</span>
						</span>
					</div>

					<div v-if="data.age != null && data.age != undefined">
						<font-awesome-icon icon="birthday-cake" fixed-width class="text-muted" />
						<span class="font-weight-bold">{{ data.age }}</span>
					</div>
				</div>
			</template>
			<template v-if="canAdd" #append>
				<b-button variant="primary" @click="add" title="Add New Patient">
					<font-awesome-icon icon="plus" fixed-width />
				</b-button>
			</template>
			<!-- <template #prepend>
				<span class="input-group-text">
					<font-awesome-icon icon="search" fixed-width />
				</span>
			</template> -->
			<template #prepend>
				<span class="input-group-text">
					<font-awesome-icon icon="search" fixed-width />
				</span>
				<b-button variant="outline-primary"  title="OCR">
					OCR
				</b-button>
			</template>

		</TypeAhead>
		<div v-if="description" class="bg-light rounded text-muted px-4 py-2">
			{{ description }}
		</div>
	</div>
</template>

<script type="text/javascript">
/**
 * Working on replacing this with /components/Patients/SearchInput.vue
 */

import { debounce } from "lodash";
import TypeAhead from "vue-typeahead-bootstrap";

export default {
	name: "PatientSearch",
	components: {
		TypeAhead,
	},
	props: {
		value: {
			type: Object,
			default: () => {
				return {};
			},
		},
		disabled: {
			type: Boolean,
			default: false,
		},
		displayField: {
			type: String,
			default: "list_name",
		},
		required: {
			type: Boolean,
			default: false,
		},
		placeholder: {
			type: String,
			default: "Last name, first name...",
		},
		canAdd: {
			type: Boolean,
			default: true,
		},
	},
	data() {
		return {
			// Currently fetching result status
			fetching: false,
			// Has searched
			searched: false,
			// Currently searching status
			searching: false,
			// Selected entity
			entity: {},
			// Typeahead text,
			query: "",
			// Typeahead results
			entities: [],
		};
	},
	computed: {
		inputClasses() {
			var string = "mb-0";

			if (this.fetching) {
				string = string + " fetching";
			}

			if (this.searching) {
				string = string + " searching";
			}

			return string;
		},
		description() {
			if (this.empty) {
				return "No patients were found matching your search.";
			} else {
				return "";
			}
		},
		empty() {
			return this.entities.length <= 0 && this.searched == true;
		},
	},
	mounted() {
		if (this.value && this.value[this.displayField]) {
			this.$refs.typeAhead.inputValue = this.value[this.displayField];
		}
	},
	watch: {
		query(newQuery) {
			if (newQuery === "") {
				this.cleared();
				return;
			}

			this.searchDebounce(newQuery);
		},
		entity(newEntity) {
			if (newEntity && newEntity.ClientId) {
				this.$emit("input", newEntity);
			}
		},
		value(newEntity) {
			if (newEntity && newEntity[this.displayField]) {
				this.$refs.typeAhead.inputValue = newEntity[this.displayField];
			} else {
				this.$refs.typeAhead.inputValue = "";
			}
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
		 * Add a new item entity
		 */
		add() {
			this.$emit("add");
		},
		/**
		 * Cleared result/input
		 */
		cleared() {
			this.$emit("clear");
		},
		/**
		 * Get single result by primary key
		 */
		async getResult(id) {
			try {
				this.fetching = true;

				const response = await this.$store.dispatch("patients/get", {
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
		 * Selected a result from the list
		 */
		selectedResult(entity) {
			this.$emit("input", entity);
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

				const response = await this.$store.dispatch("patients/index", {
					search: query,
				});

				this.entities = response.data;
				this.$emit("results", response.data);
				this.searched = true;
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Search Error",
					message: "Error searching for patients. Please check for errors.",
				});

				this.$emit("error", e.response.data);
			} finally {
				this.searching = false;
			}
		},
		/**
		 * Serializer function for typeahead
		 */
		formatResults(input) {
			return input[this.displayField];
		},
	},
};
</script>
