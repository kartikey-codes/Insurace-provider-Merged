<template>
	<b-row>
		<b-col cols="12" sm="6" lg="6" xl="4">
			<b-form-group label="Active" label-for="active" label-cols-lg="4">
				<b-form-select
					v-model="filters.active"
					v-bind="{ disabled }"
					:options="activeOptions"
					value-field="value"
					text-field="text"
				/>
			</b-form-group>
		</b-col>
		<b-col cols="12" sm="6" lg="6" xl="4">
			<b-form-group label="Facility" label-for="facility_id" label-cols-lg="4">
				<b-form-select
					name="facility_id"
					v-model="filters.facility_id"
					:disabled="disabled"
					:options="facilities"
					value-field="id"
					text-field="name"
				>
					<template #first>
						<!-- this slot appears above the options from 'options' prop -->
						<option :value="null">(All)</option>
					</template>
				</b-form-select>
			</b-form-group>
		</b-col>
		<b-col cols="12" sm="6" lg="6" xl="4">
			<b-form-group label="NPI" label-for="npi_number" label-cols-lg="4">
				<b-form-input type="text" name="npi_number" v-model="filters.npi_number" :disabled="disabled" lazy />
			</b-form-group>
		</b-col>
	</b-row>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "ClientEmployeeFilters",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					active: null,
					facility_id: null,
					npi_number: null,
				};
			},
		},
		disabled: {
			type: Boolean,
			default: false,
		},
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
			facilities: "facilities/active",
			loadingFacilities: "facilities/loadingActive",
		}),
	},
};
</script>
