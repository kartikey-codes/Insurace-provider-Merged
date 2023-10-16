<template>
	<b-row>
		<b-col cols="12" sm="6" lg="6" xl="4">
			<b-form-group label="Active" label-for="active" label-cols-lg="4">
				<b-form-select
					name="active"
					v-model="filters.active"
					:disabled="disabled"
					:options="activeOptions"
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
			<b-form-group label="Type" label-for="facility_type_id" label-cols-lg="4">
				<b-form-select
					name="facility_type_id"
					v-model="filters.facility_type_id"
					:disabled="disabled"
					:options="facilityTypes"
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
			<b-form-group label="State" label-for="state" label-cols-lg="4">
				<b-form-select
					name="state"
					v-model="filters.state"
					:disabled="disabled"
					:options="states"
					value-field="abbreviation"
					text-field="name"
				>
					<template #first>
						<option :value="null">(All)</option>
					</template>
				</b-form-select>
			</b-form-group>
		</b-col>
		<b-col cols="12" sm="6" lg="6" xl="4">
			<b-form-group label="Owned" label-for="client_owned" label-cols-lg="4">
				<b-form-select
					name="client_owned"
					v-model="filters.client_owned"
					:disabled="disabled"
					:options="ownedOptions"
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
			<b-form-group label="Has Contract" label-for="has_contract" label-cols-lg="4">
				<b-form-select
					name="has_contract"
					v-model="filters.has_contract"
					:disabled="disabled"
					:options="hasContractOptions"
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
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "FacilityFilters",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					active: null,
					facility_type_id: null,
					state: null,
					owned: null,
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
			states: "states/states",
			facilityTypes: "facilityTypes/all",
		}),
	},
	data() {
		return {
			activeOptions: [
				{
					value: 1,
					name: "Active",
				},
				{
					value: 0,
					name: "Inactive",
				},
			],
			ownedOptions: [
				{
					value: 1,
					name: "Owned",
				},
				{
					value: 0,
					name: "Not Owned",
				},
			],
			hasContractOptions: [
				{
					value: 1,
					name: "Yes",
				},
				{
					value: 0,
					name: "No",
				},
			],
		};
	},
};
</script>
