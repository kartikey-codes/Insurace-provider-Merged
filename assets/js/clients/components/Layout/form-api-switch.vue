<template>
	<b-form-checkbox v-model="checked" v-bind="{ disabled }" switch>
		<slot name="label">{{ label }}</slot>
	</b-form-checkbox>
</template>

<script>
export default {
	name: "FormApiSwitch",
	props: {
		value: {
			type: Boolean,
			default: false,
		},
		label: {
			type: String,
			default: "",
		},
		disabled: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			checked: this.value,
		};
	},
	watch: {
		checked: function (val) {
			this.$emit("input", val);
		},
	},
};
</script>
