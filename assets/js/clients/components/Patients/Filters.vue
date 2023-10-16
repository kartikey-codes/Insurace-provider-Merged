<template>
	<b-form @submit.prevent>
		<b-row>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Medical Record Number" label-for="medical_record_number" label-cols-lg="4">
					<b-form-input
						name="medical_record_number"
						v-model="filters.medical_record_number"
						:disabled="disabled"
						lazy
					/>
				</b-form-group>
			</b-col>

			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Date of Birth" label-for="date_of_birth" label-cols-lg="4">
					<b-form-input
						name="date_of_birth"
						type="date"
						v-model="filters.date_of_birth"
						:disabled="disabled"
					/>
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Gender" label-for="sex" label-cols-lg="4">
					<b-form-select
						name="sex"
						v-model="filters.sex"
						:disabled="disabled"
						:options="sexes"
						value-field="value"
						text-field="name"
					>
						<template #first>
							<option :value="null">(All)</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Marital Status" label-for="marital_status" label-cols-lg="4">
					<b-form-select
						name="marital_status"
						v-model="filters.marital_status"
						:disabled="disabled"
						:options="maritalStatuses"
						value-field="value"
						text-field="name"
					>
						<template #first>
							<option :value="null">(All)</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
		</b-row>
	</b-form>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "PatientFilters",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					date_of_birth: null,
					sex: null,
					marital_status: null,
					medical_record_number: null,
				};
			},
		},
		disabled: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		filters: {
			get() {
				return this.value;
			},
			set(val) {
				this.$emit("input", val);
			},
		},
		...mapGetters({
			sexes: "patients/sexes",
			maritalStatuses: "patients/maritalStatuses",
		}),
	},
};
</script>
