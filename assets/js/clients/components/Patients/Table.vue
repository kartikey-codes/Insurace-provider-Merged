<template>
	<DataTable v-bind="$attrs" v-on="$listeners" :fields="fields" :sort-aliases="sortAliases" @sorted.once="sorted">
		<template v-slot:[`cell(last_name)`]="{ value }">
			<div v-if="value" class="w-12 text-truncate" :title="value">{{ value }}</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(first_name)`]="{ value }">
			<div v-if="value" class="w-12 text-truncate" :title="value">{{ value }}</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(middle_name)`]="{ value }">
			<div v-if="value" class="w-12 text-truncate" :title="value">{{ value }}</div>
			<div v-else class="text-muted">&mdash;</div>
		</template>

		<template v-slot:[`cell(date_of_birth)`]="{ value, item }">
			<div v-if="value" class="d-flex justify-content-between align-items-center">
				<div>{{ $filters.formatDate(value) }}</div>
				<div class="small text-muted">{{ $filters.formatNumber(item.age) }}</div>
			</div>
			<span v-else class="text-muted">&mdash;</span>
		</template>

		<template v-slot:[`cell(medical_record_number)`]="{ value }">
			<span v-if="value">{{ value }}</span>
			<span v-else class="text-muted">&mdash;</span>
		</template>

		<template v-slot:[`cell(sex)`]="{ value }">
			<span v-if="value">{{ value }}</span>
			<span v-else class="text-muted">&mdash;</span>
		</template>

		<template v-slot:[`cell(marital_status)`]="{ value }">
			<span v-if="value">{{ value }}</span>
			<span v-else class="text-muted">&mdash;</span>
		</template>

		<template v-slot:[`cell(created)`]="{ value }">
			<span v-if="value">{{ $filters.formatTimestamp(value) }}</span>
			<span v-else class="text-muted">&mdash;</span>
		</template>
	</DataTable>
</template>

<script>
import { mapGetters } from "vuex";
import DataTable from "@/clients/components/Layout/data-table.vue";

export default {
	name: "PatientTable",
	components: {
		DataTable,
	},
	props: {
		fields: {
			type: Array,
			default: () => {
				return [
					// {
					// 	key: 'id',
					// 	label: 'ID',
					// 	sortable: true,
					// 	isRowHeader: true
					// },
					{
						key: "last_name",
						label: "Last Name",
						sortable: true,
						isRowHeader: true,
						thClass: "w-12",
					},
					{
						key: "first_name",
						label: "First Name",
						sortable: true,
						isRowHeader: true,
						thClass: "w-12",
					},
					{
						key: "middle_name",
						label: "Middle Name",
						sortable: true,
						thClass: "w-12",
					},
					{
						key: "date_of_birth",
						label: "Date of Birth",
						sortable: true,
						thClass: "w-10",
					},
					{
						key: "medical_record_number",
						label: "Medical Record Number",
						sortable: true,
						thClass: "w-10",
					},
					{
						key: "sex",
						label: "Gender",
						sortable: true,
						thClass: "w-10",
					},
					{
						key: "marital_status",
						label: "Marital Status",
						sortable: true,
						thClass: "w-10",
					},
					// {
					// 	key: "created",
					// 	label: "Created",
					// 	sortable: true,
					// },
				];
			},
		},
		sort: {
			type: String,
			default: "last_name",
		},
		sortDescending: {
			type: Boolean,
			default: false,
		},
	},
	computed: mapGetters({
		sortAliases: "patients/sortAliases",
	}),
	methods: {
		sorted(params) {
			this.$emit("update:sort", params.sort);
			this.$emit("update:sort-descending", params.sortDescending);
			this.$emit("sorted", params);
		},
	},
};
</script>
