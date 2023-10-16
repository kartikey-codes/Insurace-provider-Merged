<template>
	<div>
		<slot
			v-bind="{
				empty,
				filters,
				loading,
				refresh,
				results,
				search,
				total,
			}"
		/>
	</div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from "vue";

const props = defineProps({
	action: {
		type: Function,
		default: async () => {},
		required: true,
	},
	filters: {
		type: Object,
		default: () => {
			return {};
		},
	},
	search: {
		type: String,
		default: "",
	},
});

const loading = ref(false);
const total = ref(0);
const results = ref([]);

const refresh = async () => {
	try {
		loading.value = true;

		const request = {
			search: props.search !== "" ? props.search : null,
			...props.filters,
		};

		const response = await props.action(request);

		results.value = response.data;
		total.value = response.data.length || 0;
	} finally {
		loading.value = false;
	}
};

watch(
	() => props.filters,
	() => {
		refresh();
	},
	{
		deep: true,
		immediate: false,
	}
);

const empty = computed(() => results.value.length <= 0);

onMounted(() => {
	refresh();
});
</script>
