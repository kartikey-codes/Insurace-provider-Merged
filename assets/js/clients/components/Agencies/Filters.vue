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
			<b-form-group label="Contractor" label-for="third_party_contractor" label-cols-lg="4">
				<b-form-select
					v-model="filters.third_party_contractor"
					v-bind="{ disabled }"
					:options="contractorOptions"
					value-field="value"
					text-field="text"
				/>
			</b-form-group>
		</b-col>
		<b-col cols="12" sm="6" lg="6" xl="4">
			<b-form-group label="Delivery Method" label-for="outgoing_primary_method" label-cols-lg="4">
				<b-form-select
					v-model="filters.outgoing_primary_method"
					v-bind="{ disabled }"
					:options="outgoingMethods"
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

<script>
import { mapGetters } from "vuex";

export default {
	name: "AgencyFilters",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					active: null,
					third_party_contractor: null,
					outgoing_primary_method: null,
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
			contractorOptions: [
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
			outgoingMethods: "outgoingDocuments/availableMethods",
		}),
	},
};
</script>
