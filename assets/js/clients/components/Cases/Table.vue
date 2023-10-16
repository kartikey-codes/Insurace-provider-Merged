<template>
	<DataTable v-bind="{ fields, sortAliases, ...$attrs }" v-on="$listeners" @sorted.once="sorted">
		<template v-slot:[`cell(status)`]="{ value, item }">
			<div v-if="value && item">
				<case-status-label :value="item" />

				<b-badge v-if="item.unable_to_complete" pill variant="warning" title="Unable To Complete">
					<font-awesome-icon icon="exclamation-triangle" />
				</b-badge>
			</div>
			<div v-else class="font-weight-bold text-uppercase text-danger">Missing</div>
		</template>

		<template v-slot:[`cell(case_type.name)`]="{ value }">
			<div v-if="value" class="w-10 text-truncate" :title="value">{{ value }}</div>
			<div v-else class="text-uppercase font-weight-bold text-danger">Missing</div>
		</template>

		<template v-slot:[`cell(client_employee.list_name)`]="{ value }">
			<div v-if="value" class="w-10 text-truncate" :title="value">{{ value }}</div>
			<div v-else class="text-uppercase font-weight-bold text-danger">Missing</div>
		</template>

		<template v-slot:[`cell(patient.list_name)`]="{ value }">
			<div v-if="value" class="w-10 text-truncate" :title="value">{{ value }}</div>
			<div v-else class="text-uppercase font-weight-bold text-danger">Missing</div>
		</template>

		<template v-slot:[`cell(facility.name)`]="{ value }">
			<div v-if="value" class="w-10 text-truncate" :title="value">{{ value }}</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(denial_type.name)`]="{ value }">
			<div v-if="value" class="w-10 text-truncate" :title="value">{{ value }}</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(appeals)`]="{ value }">
			<div v-if="value && value.length > 0" class="w-10 text-truncate">
				{{
					value
						.map((item) => {
							if (item.appeal_level && item.appeal_level.name) {
								return item.appeal_level.name;
							} else {
								return "(Missing Level)";
							}
						})
						.join(", ")
				}}
			</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(disciplines)`]="{ value }">
			<div v-if="value && value.length > 0" class="w-10 text-truncate">
				{{
					value
						.map((item) => {
							if (item && item.short_name) {
								return item.short_name;
							} else {
								return "(Missing Name)";
							}
						})
						.join(", ")
				}}
			</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(case_outcome.name)`]="{ value }">
			<div v-if="value" class="w-10 text-truncate" :title="value">{{ value }}</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(insurance_provider.name)`]="{ value }">
			<div v-if="value" class="w-10 text-truncate" :title="value">{{ value }}</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(insurance_type.name)`]="{ value }">
			<div v-if="value" class="w-10 text-truncate" :title="value">{{ value }}</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(insurance_number)`]="{ value }">
			<div v-if="value" class="w-10 text-truncate" :title="value">{{ value }}</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(admit_date)`]="{ value }">
			<div v-if="value" class="w-10 text-truncate" :title="value">{{ $filters.formatDate(value) }}</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(visit_number)`]="{ value }">
			<div v-if="value" class="w-10 text-truncate" :title="value">{{ value }}</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(total_claim_amount)`]="{ value }">
			<div v-if="value && value > 0" class="w-10 text-truncate" :title="value">
				{{ $filters.currency(value) }}
			</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(disputed_amount)`]="{ value }">
			<div v-if="value && value > 0" class="w-10 text-truncate" :title="value">
				{{ $filters.currency(value) }}
			</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(reimbursement_amount)`]="{ value }">
			<div v-if="value && value > 0" class="w-10 text-truncate" :title="value">
				{{ $filters.currency(value) }}
			</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(settled_amount)`]="{ value }">
			<div v-if="value && value > 0" class="w-10 text-truncate" :title="value">
				{{ $filters.currency(value) }}
			</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(assigned_to_user.full_name)`]="{ value }">
			<div v-if="value" class="w-10 text-truncate" :title="value">{{ value }}</div>
			<div v-else class="text-primary">Unassigned</div>
		</template>

		<template v-slot:[`cell(created)`]="{ value }">
			<div v-if="value">{{ $filters.formatTimestamp(value) }}</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>
	</DataTable>
</template>

<script>
import { mapGetters } from "vuex";
import DataTable from "@/clients/components/Layout/data-table.vue";
import CaseStatusLabel from "@/clients/components/Cases/StatusLabel.vue";

export default {
	name: "CaseTable",
	components: {
		CaseStatusLabel,
		DataTable,
	},
	props: {
		hideFacility: {
			type: Boolean,
			default: false,
		},
		hideInsuranceProvider: {
			type: Boolean,
			default: false,
		},
		hidePatient: {
			type: Boolean,
			default: false,
		},
		hideAssignedTo: {
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
					key: "status",
					label: "Status",
					sortable: false,
				},
				{
					key: "patient.list_name",
					sortKey: "Patients.last_name",
					label: "Patient",
					sortable: true,
					isRowHeader: true,
					class: this.hidePatient ? "d-none" : "",
				},
				{
					key: "facility.name",
					label: "Facility",
					sortable: true,
					class: this.hideFacility ? "d-none" : "",
				},
				{
					key: "assigned_to_user.full_name",
					sortKey: "AssignedToUser.last_name",
					label: "Assigned To",
					sortable: true,
					class: this.hideAssignedTo ? "d-none" : "",
				},
				{
					key: "denial_type.name",
					label: "Denial Type",
					sortable: true,
				},
				{
					key: "admit_date",
					label: "Admit Date",
					sortable: true,
				},
				{
					key: "appeals",
					sortKey: "AppealLevels.order_number",
					label: "Levels",
					sortable: true,
				},
				{
					key: "disciplines",
					label: "Disciplines",
					sortable: false,
				},
				{
					key: "client_employee.list_name",
					label: "Physician",
					sortable: true,
					isRowHeader: false,
				},
				{
					key: "case_outcome.name",
					label: "Outcome",
					sortable: true,
				},
				{
					key: "insurance_provider.name",
					label: "Insurance Provider",
					sortable: true,
					class: this.hideInsuranceProvider ? "d-none" : "",
				},
				{
					key: "insurance_type.name",
					label: "Insurance Type",
					sortable: true,
				},
				{
					key: "disputed_amount",
					label: "Disputed Amount",
					sortable: true,
				},
				{
					key: "created",
					label: "Added",
				},
			];
		},
		...mapGetters({
			sortAliases: "cases/sortAliases",
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
