<template>
	<b-form @submit.prevent>
		<b-card>
			<b-row>
				<b-col sm="6" md="4">
					<b-form-group id="name" label="Name" label-for="name" label-cols-lg="4">
						<b-form-input v-model="localValue.name" :disabled="disabled" />
					</b-form-group>
				</b-col>
				<b-col sm="6" md="4">
					<b-form-group id="active" label="Active" label-for="active" label-cols-lg="4">
						<b-form-select
							v-model="localValue.active"
							:disabled="disabled"
							:options="activeOptions"
							value-field="value"
							text-field="name"
						>
							<template #first>
								<!-- this slot appears above the options from 'options' prop -->
								<option :value="null">(All)</option>
							</template>
						</b-form-select>
					</b-form-group>
				</b-col>
				<b-col sm="6" md="4">
					<b-form-group id="state" label="State" label-for="state" label-cols-lg="4">
						<b-form-select
							v-model="localValue.state"
							:disabled="disabled"
							:options="states"
							value-field="abbreviation"
							text-field="name"
						>
							<template #first>
								<!-- this slot appears above the options from 'options' prop -->
								<option :value="null">(All)</option>
							</template>
						</b-form-select>
					</b-form-group>
				</b-col>
			</b-row>
		</b-card>
	</b-form>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "VendorFilters",
	props: {
		value: {
			type: Object,
			default: () => {
				return {};
			},
		},
		disabled: {
			type: Boolean,
			default: false,
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
		...mapGetters({
			states: "states/states",
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
		};
	},
};
</script>
