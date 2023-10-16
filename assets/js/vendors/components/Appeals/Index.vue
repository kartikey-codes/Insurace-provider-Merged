<template>
	<div>
		<loading-indicator v-if="showLoading" class="my-5" />
		<div v-else-if="!error && !empty">
			<b-table
				hover
				striped
				bordered
				responsive
				no-local-sorting
				:busy="loading"
				:fields="fields"
				:items="data"
				@row-clicked="clickedRow"
				:sort-desc.sync="sortDesc"
				:sort-by.sync="sortBy"
				@sort-changed="sorted"
				class="mb-0 cursor-pointer nowrap"
			>
				<template v-slot:[`cell(appeal_status)`]="data">
					<span v-if="data.value && data.item">
						<appeal-status-label :value="data.item" />
					</span>
					<span v-else class="font-weight-bold text-uppercase text-danger">Missing</span>
				</template>

				<template v-slot:[`cell(case_type.name)`]="{ value }">
					<span v-if="value">{{ value }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(case.denial_type.name)`]="{ value }">
					<span v-if="value">{{ value }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(appeal_type.name)`]="{ value }">
					<span v-if="value">{{ value }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(appeal_level.name)`]="{ value }">
					<span v-if="value">{{ value }}</span>
					<span v-else class="font-weight-bold text-uppercase text-danger">
						<font-awesome-icon icon="exclamation-triangle" fixed-width />
						<span>Missing</span>
					</span>
				</template>

				<template v-slot:[`cell(case.patient.list_name)`]="{ value }">
					<span v-if="value">{{ value }}</span>
					<span v-else class="font-weight-bold text-uppercase text-danger">
						<font-awesome-icon icon="exclamation-triangle" fixed-width />
						<span>Missing</span>
					</span>
				</template>

				<template v-slot:[`cell(case.facility.name)`]="{ value }">
					<span v-if="value">{{ value }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(defendable)`]="{ value }">
					<span v-if="value === true || value === 1">Yes</span>
					<span v-if="value === false || value === 0">No</span>
					<span v-if="value === '' || value === null || value === undefined" class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(assigned_to_user.full_name)`]="{ value }">
					<span v-if="value">{{ value }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(case.case_outcome.name)`]="{ value }">
					<span v-if="value">{{ value }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(case.insurance_provider.name)`]="{ value }">
					<span v-if="value">{{ value }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(case.insurance_type.name)`]="data">
					<span v-if="data && data.value">{{ data.value }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(case.insurance_plan)`]="{ value }">
					<span v-if="value">{{ value }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(case.insurance_number)`]="{ value }">
					<span v-if="value">{{ value }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(case.closed)`]="{ value }">
					<span v-if="value">{{ $filters.formatTimestamp(value) }}</span>
					<b-badge variant="dark" v-else>Open</b-badge>
				</template>

				<template v-slot:[`cell(case.visit_number)`]="{ value }">
					<span v-if="value">{{ value }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(admit_date)`]="{ value }">
					<span v-if="value">{{ $filters.formatDate(value) }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(discharge_date)`]="{ value }">
					<span v-if="value">{{ $filters.formatDate(value) }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(letter_date)`]="{ value }">
					<span v-if="value">{{ $filters.formatDate(value) }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(received_date)`]="{ value }">
					<span v-if="value">{{ $filters.formatDate(value) }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(due_date)`]="{ value }">
					<span v-if="value">{{ $filters.formatDate(value) }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(total_claim_amount)`]="{ value }">
					<span v-if="value > 0">{{ $filters.currency(value) }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(disputed_amount)`]="{ value }">
					<span v-if="value && value > 0">{{ $filters.currency(value) }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(reimbursement_amount)`]="{ value }">
					<span v-if="value && value > 0">{{ $filters.currency(value) }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(settled_amount)`]="{ value }">
					<span v-if="value && value > 0">{{ $filters.currency(value) }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(created)`]="{ value }">
					<span v-if="value">{{ $filters.formatTimestamp(value) }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>

				<template v-slot:[`cell(modified)`]="{ value }">
					<span v-if="value">{{ $filters.formatTimestamp(value) }}</span>
					<span v-else class="text-muted">&mdash;</span>
				</template>
			</b-table>

			<pagination class="my-5" v-if="initialLoaded" v-model="page" :data="pagination" :loading="loading" />
		</div>
		<b-container v-else-if="!error">
			<empty-result class="my-5">
				No appeals were found
				<template #content>
					<b-button variant="light" @click="refresh()" class="mt-4">Refresh</b-button>
				</template>
			</empty-result>
		</b-container>
		<b-container v-else>
			<error-alert class="my-5">{{ error }}</error-alert>
		</b-container>
	</div>
</template>

<script type="text/javascript">
import AppealStatusLabel from "@/vendors/components/Appeals/StatusLabel.vue";

export default {
	name: "AppealIndex",
	components: {
		AppealStatusLabel,
	},
	props: {
		filters: {
			type: Object,
			default: () => {},
		},
		fields: {
			type: Array,
			default: () => [
				// {
				// 	key: "case_id",
				// 	label: "Case ID",
				// 	sortable: true,
				// 	isRowHeader: true,
				// },
				{
					key: "appeal_status",
					label: "Status",
					sortable: false,
				},
				{
					key: "case.patient.list_name",
					label: "Patient",
					sortable: true,
					isRowHeader: true,
				},
				{
					key: "case.case_type.name",
					label: "Case Type",
					sortable: true,
				},
				{
					key: "appeal_level.name",
					label: "Level",
					sortable: true,
				},
				// {
				// 	key: "case.facility.name",
				// 	label: "Facility",
				// 	sortable: true,
				// },
				// {
				// 	key: "case.visit_number",
				// 	label: "Visit Number",
				// 	sortable: true,
				// },
				{
					key: "case.denial_type.name",
					label: "Denial Type",
					sortable: true,
				},
				// {
				// 	key: "assigned_to_user.full_name",
				// 	label: "Assigned To",
				// 	sortable: true,
				// },
				{
					key: "appeal_type.name",
					label: "Appeal Type",
					sortable: true,
				},
				{
					key: "defendable",
					label: "Defendable",
					sortable: true,
				},
				{
					key: "case.insurance_provider.name",
					label: "Insurance Provider",
					sortable: true,
				},
				{
					key: "case.insurance_type.name",
					label: "Insurance Type",
					sortable: true,
				},
				// {
				// 	key: "case.insurance_plan",
				// 	label: "Insurance Plan",
				// 	sortable: true,
				// },
				// {
				// 	key: "case.insurance_number",
				// 	label: "Insurance Number",
				// 	sortable: true,
				// },
				// {
				// 	key: "created",
				// 	label: "Created",
				// 	sortable: true,
				// },
				{
					key: "due_date",
					label: "Due",
					sortable: true,
				},
				{
					key: "modified",
					label: "Last Updated",
					sortable: true,
				},
			],
		},
	},
	data() {
		return {
			data: [],
			pagination: {},
			page: 1,
			loading: true,
			error: false,
			initialLoaded: false,
			sortBy: undefined, // For table to sync with
			sortDesc: undefined, // For table to sync with
			sortField: undefined, // For API request
			sortDirection: undefined, // For API request
			sortAliases: {
				"case.case_type": "CaseTypes.name",
				"case.case_type.name": "CaseTypes.name",
				appeal_level: "AppealLevels.name",
				"appeal_level.name": "AppealLevels.name",
				appeal_type: "AppealTypes.name",
				"appeal_type.name": "AppealTypes.name",
				client: "Clients.name",
				"client.name": "Clients.name",
				"case.patient": "Patients.last_name",
				"case.patient.list_name": "Patients.last_name",
				"case.patient.date_of_birth": "Patients.date_of_birth",
				"case.case_type.name": "CaseTypes.name",
				"case.facility": "Facilities.name",
				"case.facility.name": "Facilities.name",
				"case.denial_type": "DenialTypes.name",
				"case.denial_type.name": "DenialTypes.name",
				"case.case_outcome": "CaseOutcomes.name",
				"case.case_outcome.name": "CaseOutcomes.name",
				"case.visit_number": "Cases.visit_number",
				"case.insurance_provider": "InsuranceProviders.name",
				"case.insurance_provider.name": "InsuranceProviders.name",
				"case.insurance_type": "InsuranceTypes.name",
				"case.insurance_type.name": "InsuranceTypes.name",
				completed_by_user: "CompletedByUser.first_name",
				"completed_by_user.full_name": "CompletedByUser.first_name",
				assigned_to_user: "AssignedToUser.first_name",
				"assigned_to_user.full_name": "AssignedToUser.first_name",
				created_by_user: "CreatedByUser.first_name",
				"created_by_user.full_name": "CreatedByUser.first_name",
				modified_by_user: "ModifiedByUser.first_name",
				"modified_by_user.full_name": "ModifiedByUser.first_name",
			},
		};
	},
	computed: {
		empty() {
			if (!this.data) {
				return true;
			}

			if (!this.data.length) {
				return true;
			}

			return this.data.length <= 0;
		},
		showLoading() {
			if (!this.initialLoaded) {
				return true;
			}

			if (this.data.length == 0 && this.loading) {
				return true;
			}

			return false;
		},
	},
	watch: {
		loading(newVal) {
			this.$emit("update:loading", newVal);
		},
		page(newVal, oldVal) {
			this.$emit("paged", newVal, oldVal);
			this.refresh(newVal);
		},
	},
	mounted() {
		this.refresh();
	},
	methods: {
		async refresh(page = null) {
			try {
				if (page != null) {
					this.page = page;
				}

				this.loading = true;
				this.error = false;

				var requestFilters = Object.assign({}, this.filters);
				var requestParams = Object.assign({}, requestFilters);

				requestParams.page = this.page;
				if (this.sortField) {
					requestParams.sort = this.sortField;
				}
				if (this.sortDirection) {
					requestParams.direction = this.sortDirection;
				}

				const response = await this.$store.dispatch("appeals/index", requestParams);

				this.data = response.data || [];
				this.pagination = response.pagination || {};

				if (response.pagination.page) {
					this.page = response.pagination.page;
				}

				if (response.pagination.direction == "desc") {
					this.sortDesc = true;
				}
				if (response.pagination.direction == "asc") {
					this.sortDesc = false;
				}

				this.$emit("refreshed", this.data, this.pagination);
			} catch (e) {
				console.error(e);

				if (e.response && e.response.data && e.response.data.message) {
					this.error = e.response.data.message;
				}
			} finally {
				this.loading = false;
				this.initialLoaded = true;
			}
		},
		sorted(params) {
			// Allow aliasing sort fields for API
			if (this.sortAliases[params.sortBy]) {
				this.sortField = this.sortAliases[params.sortBy];
			} else {
				this.sortField = params.sortBy;
			}

			this.sortDirection = params.sortDesc ? "desc" : "asc";
			this.refresh();

			this.$emit("sorted", this.sortField, this.sortDirection);
		},
		clickedRow(row) {
			this.$emit("clicked", row);
		},
	},
};
</script>
