<template>
	<div>
		<TypeAhead
			:input-class="inputClasses"
			ref="typeAhead"
			v-model="query"
			@hit="selectedResult"
			@input="queryChanged"
			:data="entities"
			:serializer="formatResults"
			:disabled="readonly"
			min-matching-chars.number="0"
			placeholder="Search users..."
		>
			<template #suggestion="{ data, htmlText }">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<span v-if="data[displayField]" class="mb-0">
							<span v-if="htmlText">
								<span v-html="htmlText"></span>
							</span>
							<span v-else>{{ data[displayField] }}</span>
							<div v-if="data.full_address" class="text-muted">
								<small v-html="data.full_address"></small>
							</div>
						</span>
						<span v-else class="text-danger">
							<font-awesome-icon icon="exclamation-triangle" />Missing Name
						</span>
					</div>
				</div>
			</template>
		</TypeAhead>
	</div>
</template>

<script type="text/javascript">
import { debounce } from "lodash";
import TypeAhead from "vue-typeahead-bootstrap";

export default {
	name: "NewUserSearch",
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
			default: false, // Not working with vue-bootstrap-typeahead :(
		},
		displayField: {
			type: String,
			default: "full_name",
		},
		readonly: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			// Currently fetching result status
			fetching: false,
			// Currently searching status
			searching: false,
			// Selected entity
			entity: {},
			// Typeahead text,
			query: null,
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
	},
	mounted() {
		if (this.value && this.value[this.displayField]) {
			this.$refs.typeAhead.inputValue = this.value[this.displayField];
		}
	},
	watch: {
		query(newQuery) {
			this.searchDebounce(newQuery);
		},
		entity(newEntity) {
			if (newEntity && newEntity.ClientId) {
				this.$emit("input", newEntity);
			}
		},
		value(newEntity) {
			this.$refs.typeAhead.inputValue = newEntity[this.displayField];
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
		 * Get single result by primary key
		 */
		async getResult(id) {
			try {
				this.fetching = true;

				const response = await this.$store.dispatch("users/get", {
					id: id,
				});

				this.$emit("input", response);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Error getting user details",
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
				return false;
			}

			try {
				this.searching = true;
				const response = await this.$store.dispatch("users/getNew", {
					search: query,
				});

				this.entities = response;
				this.$emit("results", response);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Error getting list of new users",
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
