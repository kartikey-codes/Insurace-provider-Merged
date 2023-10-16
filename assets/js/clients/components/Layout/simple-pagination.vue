<template>
	<b-button-group v-bind="$attrs">
		<b-button @click="prevPage" :disabled="loading || !hasPrevPage" title="Previous Page">
			<font-awesome-icon icon="chevron-left" fixed-width />
			<span class="d-none d-sm-inline">Prev</span>
		</b-button>
		<b-button @click="nextPage" :disabled="loading || !hasNextPage" title="Next Page">
			<span class="d-none d-sm-inline">Next</span>
			<font-awesome-icon icon="chevron-right" fixed-width />
		</b-button>
	</b-button-group>
</template>

<script>
export default {
	name: "SimplePagination",
	props: {
		hasNextPage: {
			type: Boolean,
			default: false,
		},
		hasPrevPage: {
			type: Boolean,
			default: false,
		},
		loading: {
			type: Boolean,
			default: false,
		},
		prevPage: {
			type: Function,
			default: () => {},
		},
		nextPage: {
			type: Function,
			default: () => {},
		},
	},
};
</script>
