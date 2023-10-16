<template>
	<b-row>
		<b-col cols="12" sm="6" lg="6" xl="4">
			<b-form-group label="Active" label-for="active" label-cols-lg="4">
				<b-form-select name="active" v-model="filters.active" v-bind="{ disabled }" :options="activeOptions" />
			</b-form-group>
		</b-col>
		<b-col cols="12" sm="6" lg="6" xl="4">
			<b-form-group label="Default Type" label-for="default_insurance_type_id" label-cols-lg="4">
				<b-form-select
					name="default_insurance_type_id"
					v-model="filters.default_insurance_type_id"
					:disabled="disabled"
					:options="insuranceTypes"
					value-field="id"
					text-field="name"
				>
					<template #first>
						<option :value="null">(All)</option>
					</template>
				</b-form-select>
			</b-form-group>
		</b-col>
		<b-col cols="12" sm="6" lg="6" xl="4">
			<b-form-group label="Has Appeal Level" label-for="appeal_level_id" label-cols-lg="4">
				<b-form-select
					name="appeal_level_id"
					v-model="filters.appeal_level_id"
					:disabled="disabled"
					:options="appealLevels"
					value-field="id"
					text-field="name"
				>
					<template #first>
						<option :value="null">(All)</option>
					</template>
				</b-form-select>
			</b-form-group>
		</b-col>
		<b-col cols="12" sm="6" lg="6" xl="4">
			<b-form-group label="Has Type" label-for="insurance_type_id" label-cols-lg="4">
				<b-form-select
					name="insurance_type_id"
					v-model="filters.insurance_type_id"
					:disabled="disabled"
					:options="insuranceTypes"
					value-field="id"
					text-field="name"
				>
					<template #first>
						<option :value="null">(All)</option>
					</template>
				</b-form-select>
			</b-form-group>
		</b-col>
	</b-row>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "InsuranceProviderFilters",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					default_insurance_type_id: null,
					appeal_level_id: null,
					insurance_type_id: null,
					active: null,
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
			appealLevels: "appealLevels/all",
			insuranceTypes: "insuranceTypes/all",
		}),
	},
	data() {
		return {
			activeOptions: [
				{ text: "(All)", value: null },
				{ text: "Yes", value: true },
				{ text: "No", value: false },
			],
		};
	},
};
</script>
