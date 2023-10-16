<template>
	<b-table-simple
		stacked="md"
		class="mb-0"
	>
		<b-thead>
			<b-tr>
				<b-th>Admit Date</b-th>
				<b-th>Discharge Date</b-th>
				<b-th>Visit ID</b-th>
				<b-th>Remove</b-th>
			</b-tr>
		</b-thead>
		<b-tbody>
			<b-tr
				v-for="(readmission, index) in localValue"
				:key="index"
			>
				<b-td stacked-heading="Admit Date">
					<b-form-input
						type="date"
						v-model="readmission.admit_date"
						required
					/>
				</b-td>
				<b-td stacked-heading="Discharge Date">
					<b-form-input
						type="date"
						v-model="readmission.discharge_date"
						required
					/>
				</b-td>
				<b-td stacked-heading="Visit Number">
					<b-form-input
						type="text"
						v-model="readmission.visit_number"
						required="required"
						placeholder="Visit Number"
					/>
				</b-td>
				<b-td stacked-heading="Remove">
					<b-button
						variant="outline-danger"
						@click="remove(readmission)"
					>
						<font-awesome-icon
							icon="times"
							fixed-width
						/>
					</b-button>
				</b-td>
			</b-tr>
		</b-tbody>
	</b-table-simple>
</template>

<script type="text/javascript">
export default {
	name: "CaseReadmissions",
	props: {
		value: {
			type: Array,
			default: () => [],
		},
	},
	computed: {
		localValue: {
			get() {
				return this.value;
			},
			set(val) {
				this.$emit("input", val);
			},
		},
	},
	methods: {
		add() {
			this.localValue.push({});
		},
		remove(readmission) {
			this.localValue.splice(this.localValue.indexOf(readmission), 1);
		},
	},
};
</script>
