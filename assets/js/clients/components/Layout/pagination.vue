<template>
	<b-pagination
		v-if="data && data.pageCount > 1"
		align="center"
		:total-rows="data.count"
		:limit="12"
		:per-page="data.perPage"
		v-model="page"
		:disabled="loading"
	>
		<template #first-text>
			<font-awesome-icon icon="fast-backward" fixed-width class="mx-0 px-0" />
		</template>
		<template #prev-text>
			<font-awesome-icon icon="step-backward" fixed-width class="mx-0 px-0" />
		</template>
		<template #next-text>
			<font-awesome-icon icon="step-forward" fixed-width class="mx-0 px-0" />
		</template>
		<template #last-text>
			<font-awesome-icon icon="fast-forward" fixed-width class="mx-0 px-0" />
		</template>
	</b-pagination>
</template>

<script type="text/javascript">
export default {
	name: "Pagination",
	props: {
		value: {
			default: 1,
		},
		loading: {
			default: false,
		},
		data: {
			type: Object,
			default: () => {},
		},
	},
	data() {
		return {
			page: 1,
		};
	},
	mounted() {
		this.page = this.value;
	},
	methods: {},
	watch: {
		page(val) {
			this.$emit("input", val);
		},
		value(val) {
			this.page = val;
		},
	},
};
</script>
