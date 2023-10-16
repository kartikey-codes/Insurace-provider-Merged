<template>
	<div>
		<b-form-invalid-feedback force-show v-for="error in value" :key="error">{{ error }}</b-form-invalid-feedback>
	</div>
</template>

<script type="text/javascript">
export default {
	name: "ValidationErrors",
	props: {
		value: {
			type: Array,
			default: [],
		},
	},
};
</script>
