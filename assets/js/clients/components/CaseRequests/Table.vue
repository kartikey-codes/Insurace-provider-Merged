<template>
	<DataTable v-bind="$attrs" v-on="$listeners" :fields="fields" :sort-aliases="sortAliases" @sorted.once="sorted">
		<template v-slot:[`cell(status_label)`]="{ value, item }">
			<span v-if="value && item">
				<case-request-status-label :value="item" />
			</span>
			<span v-else class="font-weight-bold text-uppercase text-danger">Missing</span>
		</template>

		<template v-slot:[`cell(name)`]="{ value }">
			<span v-if="value">{{ value }}</span>
			<span v-else class="font-weight-bold text-uppercase text-danger">
				<font-awesome-icon icon="exclamation-triangle" fixed-width />
				<span>Missing</span>
			</span>
		</template>

		<template v-slot:[`cell(type_label)`]="{ value }">
			<span>{{ value }}</span>
		</template>

		<template v-slot:[`cell(priority)`]="{ value }">
			<span v-if="value" class="text-success">
				<font-awesome-icon icon="check-circle" />
				<span class="sr-only">Priority</span>
			</span>
			<span v-else class="text-muted">&mdash;</span>
		</template>

		<template v-slot:[`cell(due_date)`]="{ value }">
			<span v-if="value">{{ $filters.formatDate(value) }}</span>
			<span v-else class="text-muted">&mdash;</span>
		</template>

		<template v-slot:[`cell(assigned_to_user.full_name)`]="{ value }">
			<span v-if="value">{{ value }}</span>
			<span v-else class="text-primary">Unassigned</span>
		</template>

		<template v-slot:[`cell(insurance_provider.name)`]="{ value }">
			<span v-if="value">{{ value }}</span>
			<span v-else class="text-muted">&mdash;</span>
		</template>

		<template v-slot:[`cell(agency.name)`]="{ value }">
			<span v-if="value">{{ value }}</span>
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
	</DataTable>
</template>

<script>
import { mapGetters } from "vuex";
import DataTable from "@/clients/components/Layout/data-table.vue";
import CaseRequestStatusLabel from "@/clients/components/CaseRequests/StatusLabel.vue";

export default {
	name: "CaseRequestTable",
	components: {
		CaseRequestStatusLabel,
		DataTable,
	},
	props: {
		hideAssignedTo: {
			type: Boolean,
			default: false,
		},
		hideStatus: {
			type: Boolean,
			default: false,
		},
		hidePatient: {
			type: Boolean,
			default: false,
		},
		sort: {
			type: String,
			default: "created",
		},
		sortDescending: {
			type: Boolean,
			default: true,
		},
	},
	computed: {
		fields() {
			return [
				{
					key: "status_label",
					label: "Status",
					sortable: false,
					class: this.hideStatus ? "d-none" : "",
				},
				{
					key: "name",
					label: "Name",
					sortable: true,
					isRowHeader: true,
				},
				{
					key: "case.patient.list_name",
					label: "Patient",
					sortable: true,
					isRowHeader: true,
					class: this.hidePatient ? "d-none" : "",
				},
				{
					key: "type_label",
					sortKey: "request_type",
					label: "Type",
					sortable: true,
				},
				{
					key: "due_date",
					label: "Due",
					sortable: true,
				},
				{
					key: "assigned_to_user.full_name",
					label: "Assigned To",
					sortable: true,
					class: this.hideAssignedTo ? "d-none" : "",
				},
				{
					key: "priority",
					label: "Priority",
					sortable: true,
				},
				{
					key: "insurance_provider.name",
					sortKey: "InsuranceProviders.name",
					label: "Insurance",
					sortable: true,
				},
				{
					key: "agency.name",
					sortKey: "Agencies.name",
					label: "Agency",
					sortable: true,
				},
				{
					key: "created",
					label: "Created",
					sortable: true,
				},
				{
					key: "modified",
					label: "Updated",
					sortable: true,
				},
			];
		},
		...mapGetters({
			sortAliases: "caseRequests/sortAliases",
		}),
	},
	methods: {
		sorted(params) {
			this.$emit("update:sort", params.sort);
			this.$emit("update:sort-descending", params.sortDescending);
			this.$emit("sorted", params);
		},
	},
};
</script>
