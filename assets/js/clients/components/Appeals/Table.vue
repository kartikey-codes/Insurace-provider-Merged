<template>
	<DataTable v-bind="$attrs" v-on="$listeners" :fields="fields" :sort-aliases="sortAliases" @sorted.once="sorted">
		<template v-slot:[`cell(appeal_status)`]="{ item, value }">
			<span v-if="value">
				<appeal-status-label :value="item" />
				<b-badge v-if="item.unable_to_complete" pill variant="warning" title="Unable To Complete">
					<font-awesome-icon icon="exclamation-triangle" />
				</b-badge>
			</span>
			<span v-else class="font-weight-bold text-uppercase text-danger">Missing</span>
		</template>

		<template v-slot:[`cell(case_type.name)`]="{ value }">
			<span v-if="value">{{ value }}</span>
			<span v-else class="font-weight-bold text-uppercase text-danger">
				<font-awesome-icon icon="exclamation-triangle" fixed-width />
				<span>Missing</span>
			</span>
		</template>

		<template v-slot:[`cell(case.denial_type.name)`]="{ value }">
			<span v-if="value">{{ value }}</span>
			<span v-else class="text-muted">
				<span>&mdash;</span>
			</span>
		</template>

		<template v-slot:[`cell(appeal_type.name)`]="{ value }">
			<span v-if="value">{{ value }}</span>
			<span v-else class="text-muted">
				<span>&mdash;</span>
			</span>
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
			<span v-else class="text-primary">Unassigned</span>
		</template>

		<template v-slot:[`cell(priority)`]="{ value }">
			<span v-if="value" class="text-success">
				<font-awesome-icon icon="check-circle" />
				<span class="sr-only">Priority</span>
			</span>
			<span v-else class="text-muted">&mdash;</span>
		</template>

		<template v-slot:[`cell(case.case_outcome.name)`]="{ value }">
			<span v-if="value">{{ value }}</span>
			<span v-else class="text-muted">&mdash;</span>
		</template>

		<template v-slot:[`cell(agency.name)`]="{ value }">
			<span v-if="value">{{ value }}</span>
			<span v-else class="text-muted">&mdash;</span>
		</template>

		<template v-slot:[`cell(case.insurance_provider.name)`]="{ value }">
			<span v-if="value">{{ value }}</span>
			<span v-else class="text-muted">&mdash;</span>
		</template>

		<template v-slot:[`cell(case.insurance_type.name)`]="{ value }">
			<span v-if="value">{{ value }}</span>
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
			<span v-if="value && value > 0">{{ $filters.currency(value) }}</span>
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

		<template v-slot:[`cell(audit_identifier)`]="{ value }">
			<span v-if="value">{{ value }}</span>
			<span v-else class="text-muted">
				<span>&mdash;</span>
			</span>
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
import AppealStatusLabel from "@/clients/components/Appeals/StatusLabel.vue";

export default {
	name: "AppealTable",
	components: {
		AppealStatusLabel,
		DataTable,
	},
	props: {
		hideAssignedTo: {
			type: Boolean,
			default: false,
		},
		hidePatient: {
			type: Boolean,
			default: false,
		},
		hideStatus: {
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
					key: "appeal_status",
					label: "Status",
					sortable: false,
					class: this.hideStatus ? "d-none" : "",
				},
				{
					key: "case.patient.list_name",
					label: "Patient",
					sortable: true,
					isRowHeader: true,
					class: this.hidePatient ? "d-none" : "",
				},
				// {
				// 	key: "case.case_type.name",
				// 	label: "Case Type",
				// 	sortable: true,
				// },
				{
					key: "appeal_level.name",
					label: "Level",
					sortable: true,
				},
				{
					key: "appeal_type.name",
					label: "Appeal Type",
					sortable: true,
				},
				{
					key: "case.facility.name",
					label: "Facility",
					sortable: true,
				},
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
				{
					key: "assigned_to_user.full_name",
					label: "Assigned To",
					sortable: true,
					class: this.hideAssignedTo ? "d-none" : "",
				},
				{
					key: "due_date",
					label: "Due",
					sortable: true,
				},
				{
					key: "priority",
					label: "Priority",
					sortable: true,
				},
				{
					key: "defendable",
					label: "Defendable",
					sortable: true,
				},
				{
					key: "agency.name",
					label: "Agency",
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
				{
					key: "case.insurance_plan",
					label: "Insurance Plan",
					sortable: true,
				},
				{
					key: "case.insurance_number",
					label: "Insurance Number",
					sortable: true,
				},
				{
					key: "audit_identifier",
					label: "Audit ID",
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
			sortAliases: "appeals/sortAliases",
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
