<template>
	<div>
		<entity-typeahead
			v-model="selected"
			v-bind="{
				autofocus,
				canAdd,
				disabled,
				displayField,
				getAction,
				indexAction,
				placeholder,
				required,
				selectedIcon,
			}"
			v-on="{ cleared }"
		>
			<template #result="{ entity }">
				<slot name="result" v-bind="{ entity }">
					<p class="mb-0">{{ result[displayField] || "(Missing Name)" }}</p>
				</slot>
			</template>
			<template #empty>
				<slot name="empty">
					<b-alert show variant="light" class="mt-2 mb-0 shadow-sm">
						No patients matched your search.
					</b-alert>
				</slot>
			</template>
			<template #searching>
				<slot name="searching"> </slot>
			</template>
			<template #results="{ results, selectResult }">
				<slot name="results" v-bind="{ results, selectResult }">
					<b-list-group class="mt-2 mb-0 shadow-sm" role="menu" autofocus>
						<b-list-group-item
							v-for="result in results"
							:key="result.id"
							@click="selectResult(result)"
							class="cursor-pointer"
						>
							<div class="d-flex justify-items-start align-items-center">
								<b-avatar variant="light" class="mr-3">
									<font-awesome-icon icon="user" fixed-width />
								</b-avatar>
								<div>
									<h4 class="h6 mb-0">{{ result[displayField] || "(Missing Name)" }}</h4>
									<p v-if="result.date_of_birth" class="text-muted mb-0">
										{{ $filters.formatDate(result.date_of_birth) }}
										<span v-if="result.age">
											&mdash;
											<font-awesome-icon icon="birthday-cake" fixed-width />
											{{ result.age }}
										</span>
									</p>
									<p v-else class="text-warning mb-0">Missing DOB</p>
								</div>
							</div>
						</b-list-group-item>
					</b-list-group>
				</slot>
			</template>
		</entity-typeahead>
	</div>
</template>

<script type="text/javascript">
/**
 * Replacement for /components/Search/Patients.vue
 */
export default {
	name: "PatientSearchInput",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					id: null,
					first_name: null,
					middle_name: null,
					last_name: null,
					full_name: null,
					list_name: null,
				};
			},
		},
		autofocus: {
			type: Boolean,
			default: false,
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
			default: false,
		},
		indexAction: {
			type: String,
			default: "patients/index",
		},
		getAction: {
			type: String,
			default: "patients/get",
		},
		selectedIcon: {
			type: String,
			default: "user",
		},
	},
	data() {
		return {
			selected: this.value,
		};
	},
	methods: {
		cleared() {
			this.selected = {
				id: null,
				first_name: null,
				middle_name: null,
				last_name: null,
				full_name: null,
				list_name: null,
			};
		},
	},
	watch: {
		value: {
			deep: true,
			handler(value) {
				this.selected = value;
			},
		},
		selected: {
			immediate: false,
			handler(value) {
				this.$emit("input", value);
			},
		},
	},
};
</script>
