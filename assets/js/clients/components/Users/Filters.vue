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
			<b-form-group label="Date of Birth" label-for="date_of_birth" label-cols-lg="4">
				<b-form-input name="date_of_birth" type="date" v-model="filters.date_of_birth" :disabled="disabled" />
			</b-form-group>
		</b-col>
	</b-row>
</template>

<script type="text/javascript">
export default {
	name: "UserFilters",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					active: null,
					date_of_birth: null,
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
	},
	data() {
		return {
			activeOptions: [
				{
					value: true,
					name: "Active",
				},
				{
					value: false,
					name: "Inactive",
				},
			],
		};
	},
};
</script>
