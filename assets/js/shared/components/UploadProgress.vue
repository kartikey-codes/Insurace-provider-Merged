<template>
	<b-progress v-bind="{ max }" height="2rem" animated striped :variant="full ? 'success' : 'primary'">
		<b-progress-bar :value="value">
			<h5 v-if="full" class="my-0">{{ fullText }}</h5>
			<h5 v-else class="my-0">{{ fixedValue }}%</h5>
		</b-progress-bar>
	</b-progress>
</template>

<script>
export default {
	name: "UploadProgress",
	props: {
		value: {
			required: true,
			type: Number,
			default: 0,
		},
		max: {
			required: false,
			type: Number,
			default: 100,
		},
		fullText: {
			type: String,
			default: "Processing...",
		},
	},
	computed: {
		full() {
			return this.value >= this.max;
		},
		fixedValue() {
			return this.value.toFixed(0);
		},
	},
};
</script>
