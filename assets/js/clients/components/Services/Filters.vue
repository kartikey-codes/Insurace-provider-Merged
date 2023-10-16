<template>
	<b-row>
		<b-col cols="12" sm="6" lg="6" xl="4">
			<b-form-group label="Active" label-for="active" label-cols-lg="4">
				<b-form-select name="active" v-model="filters.active" v-bind="{ disabled }" :options="activeOptions" />
			</b-form-group>
		</b-col>
		<b-col cols="12" sm="6" lg="6" xl="4">
			<b-form-group label="Owned" label-for="owned" label-cols-lg="4">
				<b-form-select name="owned" v-model="filters.owned" v-bind="{ disabled }" :options="ownedOptions" />
			</b-form-group>
		</b-col>
	</b-row>
</template>

<script>
export default {
	name: "ServiceFilters",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					active: null,
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
	},
	data() {
		return {
			activeOptions: [
				{ text: "(All)", value: null },
				{ text: "Yes", value: true },
				{ text: "No", value: false },
			],
			ownedOptions: [
				{ text: "(All)", value: null },
				{ text: "Yes", value: true },
				{ text: "No", value: false },
			],
		};
	},
};
</script>
