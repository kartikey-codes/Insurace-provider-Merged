<template>
	<b-row>
		<b-col cols="12" sm="6" lg="6" xl="4">
			<b-form-group id="name" label="Name" label-cols-lg="4">
				<b-form-input v-model="filters.name" v-bind="{ disabled }" lazy />
			</b-form-group>
		</b-col>
	</b-row>
</template>

<script>
export default {
	name: "RoleFilters",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					name: "",
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
};
</script>
