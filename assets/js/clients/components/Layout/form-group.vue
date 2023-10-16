<template>
	<b-form-group :label="label" :label-for="name" v-bind="$attrs">
		<validation-provider :vid="name" :name="label" :rules="rules" v-slot="validationContext">
			<slot
				name="default"
				v-bind="{
					name,
					label,
					validationContext,
					state: getValidationState(validationContext),
					invalid: validationContext.invalid,
				}"
			>
			</slot>
			<b-form-invalid-feedback v-for="error in validationContext.errors" :key="error">
				{{ error }}
			</b-form-invalid-feedback>
		</validation-provider>
	</b-form-group>
</template>

<script type="text/javascript">
import { formatErrors, getValidationState } from "@/validation";

export default {
	name: "FormGroup",
	props: {
		name: {
			type: String,
			default: "",
			required: true,
		},
		label: {
			type: String,
			default: "",
			required: true,
		},
		rules: {
			type: Object,
			default: () => {
				return {
					required: false,
				};
			},
		},
	},
	methods: {
		getValidationState,
	},
};
</script>
