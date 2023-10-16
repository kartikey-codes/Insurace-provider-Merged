<template>
	<div>
		<transition name="fade" mode="out-in">
			<b-row class="mb-2" v-if="selectedResults.length > 0">
				<b-col>
					<b-button variant="primary" @click="mergeResults">
						<font-awesome-icon icon="paperclip" fixed-width />
						Merge {{ selectedResults.length }} Selected Patient{{ selectedResults.length == 1 ? "" : "s" }}
					</b-button>
				</b-col>
			</b-row>
		</transition>
		<transition name="fade" mode="out-in">
			<loading-indicator v-if="!initialLoaded" class="my-5" />
			<div v-else-if="hasSimilar">
				<b-table
					hover
					striped
					bordered
					small
					responsive
					:busy="loading"
					:fields="fields"
					@row-clicked="clicked"
					:items="results"
					class="mb-0 cursor-pointer"
				>
					<template v-slot:HEAD_actions="head">
						<b-form-checkbox @click.stop="selectAllResults" v-model="allResultsSelected" />
					</template>
					<template v-slot:[`cell(actions)`]="data">
						<b-form-checkbox @click.stop :key="data.item.id" :value="data.item" v-model="selectedResults" />
					</template>
					<template v-slot:[`cell(date_of_birth)`]="data">
						<span v-if="data.value">{{ $filters.formatDate(data.value) }}</span>
						<span v-else class="text-muted">&mdash;</span>
					</template>
					<template v-slot:[`cell(sex)`]="data">
						<span v-if="data.value">{{ data.value }}</span>
						<span v-else class="text-muted">&mdash;</span>
					</template>
					<template v-slot:[`cell(marital_status)`]="data">
						<span v-if="data.value">{{ data.value }}</span>
						<span v-else class="text-muted">&mdash;</span>
					</template>
					<template v-slot:[`cell(created)`]="data">
						<span v-if="data.value">{{ $filters.formatTimestamp(data.value) }}</span>
						<span v-else class="text-muted">&mdash;</span>
					</template>
					<template v-slot:[`cell(cases_count)`]="data">
						<span v-if="data.value">{{ $filters.formatNumber(data.value) }}</span>
						<span v-else class="text-muted">None</span>
					</template>
				</b-table>
			</div>
			<empty-result v-else icon="check">
				<span>No similar patients found</span>
				<template #content>
					<p>Patients with matching or very similar names can be merged together to remove duplicates.</p>
					<p>
						Merging will cause any of the associated cases to be reassigned to this patient, and the other
						patients will be removed.
					</p>
				</template>
			</empty-result>
		</transition>
	</div>
</template>

<script type="text/javascript">
export default {
	name: "SimilarPatients",
	props: {
		patientId: {
			default: null,
		},
		fields: {
			type: Array,
			default: () => [
				{
					key: "actions",
				},
				{
					key: "first_name",
					label: "First Name",
					sortable: true,
					isRowHeader: true,
				},
				// {
				// 	key: 'middle_name',
				// 	label: 'Middle Name',
				// 	sortable: true
				// },
				{
					key: "last_name",
					label: "Last Name",
					sortable: true,
					isRowHeader: true,
				},
				{
					key: "date_of_birth",
					label: "Date of Birth",
					sortable: true,
				},
				{
					key: "sex",
					label: "Gender",
					sortable: true,
				},
				{
					key: "marital_status",
					label: "Marital Status",
					sortable: true,
				},
				{
					key: "cases_count",
					label: "Cases",
					sortable: true,
				},
				{
					key: "created",
					label: "Created",
					sortable: true,
				},
			],
		},
		confirmMergeText: {
			type: String,
			default: "Merge all selected patients into the currently viewed one? This action is NOT reversible!",
		},
	},
	data() {
		return {
			loading: true,
			initialLoaded: false,
			allResultsSelected: false,
			selectedResults: [],
			results: [],
		};
	},
	computed: {
		hasSimilar() {
			return this.results && this.results.length > 0;
		},
	},
	mounted() {
		this.refresh();
	},
	watch: {
		selectedResults(a, b) {
			this.allResultsSelected = this.results.length === a.length ? true : false;
		},
	},
	methods: {
		async refresh() {
			try {
				this.loading = true;

				const response = await this.$store.dispatch("patients/getSimilar", {
					id: this.patientId,
				});

				this.results = response;
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Error getting related patient details",
				});
			} finally {
				this.loading = false;
				this.initialLoaded = true;
			}
		},
		selectAllResults() {
			this.allResultsSelected = !this.allResultsSelected;
			this.selectedResults = !this.allResultsSelected ? this.results : [];
		},
		clearSelectedResults() {
			this.allResultsSelected = false;
			this.selectedResults = [];
		},
		async mergeResults() {
			if (!confirm(this.confirmMergeText)) {
				return false;
			}

			try {
				this.loading = true;

				const response = await this.$store.dispatch("patients/merge", {
					id: this.patientId,
					ids: this.selectedResults.map((entity) => entity.id),
				});

				this.clearSelectedResults();
				this.refresh();

				this.$emit("saved", response);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Merge Failed",
					message: "Error merging patients.",
				});
			} finally {
				this.loading = false;
			}
		},
		clicked(row) {
			this.$emit("clicked", row);
		},
	},
};
</script>
