<template>
	<div>
		<slot
			name="default"
			v-bind="{
				doSearch,
				doSort,
				download,
				downloading,
				empty,
				filters,
				hasMultiplePages,
				hasNextPage,
				hasPrevPage,
				loading,
				nextPage,
				page,
				pages,
				prevPage,
				refresh,
				results,
				search,
				total,
			}"
		></slot>
	</div>
</template>

<script setup>
import { computed, ref, onMounted, watch, h, nextTick } from "vue";
import { downloadResponseAsFile } from "@/shared/helpers/downloadHelper";

// Events
const emit = defineEmits(["total", "sort", "sortDescending"]);

// Props
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
	perPage: {
		type: Number,
		default: 0,
	},
	search: {
		type: String,
		default: "",
	},
	sort: {
		type: String,
		default: null,
	},
	sortDescending: {
		type: Boolean,
		default: null,
	},
	downloadFileName: {
		type: String,
		default: "Export.csv",
	},
});

// Data
const apiPerPage = ref(props.perPage);
const apiSort = ref(props.sort);
const apiSortDescending = ref(props.sortDescending);

const downloading = ref(false);
const page = ref(1);
const hasMultiplePages = ref(false);
const hasNextPage = ref(false);
const hasPrevPage = ref(false);
const loading = ref(true);
const total = ref(0);
const pages = ref(1);
const results = ref([]);

// Computed
const empty = computed(() => results.value.length <= 0);

const sortParams = computed({
	get() {
		return {
			sort: apiSort.value,
			sortDescending: apiSortDescending.value,
		};
	},
	set(params) {
		if (params == sortParams.value) {
			return;
		}

		apiSort.value = params.sort;
		apiSortDescending.value = params.sortDescending;
		emit("sort", apiSort.value);
		emit("sortDescending", apiSortDescending.value);
	},
});

// Methods
const prevPage = () => page.value--;
const nextPage = () => page.value++;

const doSearch = async () => {
	page.value = 1;
	await refresh();
};

const doSort = (params) => {
	sortParams.value = params;
};

const getRequest = () => {
	const hasDirection = apiSortDescending.value === null ? false : true;
	const direction = apiSortDescending.value == true ? "desc" : "asc";

	const request = {
		page: page.value,
		limit: apiPerPage.value > 0 ? apiPerPage.value : null,
		search: props.search !== "" ? props.search : null,
		sort: apiSort.value !== "" ? apiSort.value : null,
		direction: hasDirection ? direction : null,
		...props.filters,
	};

	return request;
};

const download = async (options) => {
	try {
		downloading.value = true;

		const request = getRequest();

		request.page = null;
		request.limit = null;
		request.download = true;

		const response = await props.action(request);

		downloadResponseAsFile({
			name: options.fileName || props.downloadFileName,
			contents: response,
			contentType: "text/csv",
		});
	} finally {
		await nextTick();
		downloading.value = false;
	}
};

const refresh = async () => {
	try {
		loading.value = true;

		const request = getRequest();
		const response = await props.action(request);

		hasMultiplePages.value = response.pagination.pageCount > 1 ? true : false;
		hasNextPage.value = response.pagination.nextPage;
		hasPrevPage.value = response.pagination.prevPage;
		page.value = response.pagination.page;
		pages.value = response.pagination.pageCount;
		apiPerPage.value = response.pagination.perPage;
		results.value = response.data;
		total.value = response.pagination.count;
	} finally {
		await nextTick();
		loading.value = false;
	}
};

// Watchers
watch(
	page,
	() => {
		refresh();
	},
	{
		immediate: false,
	}
);

watch(
	() => props.perPage,
	() => {
		refresh();
	},
	{
		immediate: false,
	}
);

watch(
	() => props.filters,
	() => {
		page.value = 1;
		refresh();
	},
	{
		deep: true,
		immediate: false,
	}
);

watch(
	sortParams,
	(value) => {
		refresh();
	},
	{
		immediate: false,
	}
);

watch(
	() => props.sort,
	(value) => {
		apiSort.value = value;
	},
	{
		immediate: false,
	}
);

watch(
	() => props.sortDescending,
	(value) => {
		apiSortDescending.value = value;
	},
	{
		immediate: false,
	}
);

watch(
	total,
	(value) => {
		emit("total", value);
	},
	{
		immediate: false,
	}
);

// Mounted
onMounted(() => {
	refresh();
});
</script>
