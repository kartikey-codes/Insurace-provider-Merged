<template>
	<div>
		<loading-indicator v-if="showLoading" class="my-5" />
		<div v-else-if="!error && !isEmpty">
			<b-table
				hover
				striped
				bordered
				responsive
				no-local-sorting
				:busy="loading"
				:fields="fields"
				:items="data"
				@row-clicked="clickedRow"
				@row-selected="selectedRow"
				:sort-desc.sync="sortDesc"
				:sort-by.sync="sortBy"
				@sort-changed="sorted"
				:selectable="selecting"
				select-mode="multi"
				selectedVariant="primary"
				class="mb-0 cursor-pointer nowrap"
			>
				<template v-slot:[`cell(name)`]="data">
					<span v-if="data.value">{{ data.value }}</span>
					<span v-else class="text-uppercase font-weight-bold text-danger">
						<font-awesome-icon icon="exclamation-triangle" fixed-width />
						<span>Missing</span>
					</span>
				</template>
				<template v-slot:[`cell(phone)`]="data">
					<span v-if="data.value">{{ $filters.formatPhone(data.value) }}</span>
					<span v-else class="text-muted-lighter">&mdash;</span>
				</template>
				<template v-slot:[`cell(fax)`]="data">
					<span v-if="data.value">{{ $filters.formatPhone(data.value) }}</span>
					<span v-else class="text-muted-lighter">&mdash;</span>
				</template>
				<template v-slot:[`cell(email)`]="data">
					<span v-if="data.value">{{ data.value }}</span>
					<span v-else class="text-muted-lighter">&mdash;</span>
				</template>
				<template v-slot:[`cell(street_address_1)`]="data">
					<span v-if="data.value">{{ data.value }}</span>
					<span v-else class="text-muted-lighter">&mdash;</span>
				</template>
				<template v-slot:[`cell(city)`]="data">
					<span v-if="data.value">{{ data.value }}</span>
					<span v-else class="text-muted-lighter">&mdash;</span>
				</template>
				<template v-slot:[`cell(state)`]="data">
					<span v-if="data.value">{{ data.value }}</span>
					<span v-else class="text-muted-lighter">&mdash;</span>
				</template>
				<template v-slot:[`cell(zip)`]="data">
					<span v-if="data.value">{{ data.value }}</span>
					<span v-else class="text-muted-lighter">&mdash;</span>
				</template>
				<template v-slot:[`cell(created)`]="data">
					<span v-if="data.value">{{ $filters.formatTimestamp(data.value) }}</span>
					<span v-else class="text-danger">&mdash;</span>
				</template>
				<template v-slot:[`cell(status)`]="data">
					<span v-if="data.value">{{ data.value }}</span>
					<span v-else class="text-muted-lighter">&mdash;</span>
				</template>
				<template v-slot:[`cell(active)`]="data">
					<b-badge pill v-if="data.value" variant="success">Active</b-badge>
					<span v-else class="text-muted-lighter">&mdash;</span>
				</template>
			</b-table>

			<pagination class="my-5" v-if="initialLoaded" v-model="page" :data="pagination" :loading="loading" />
		</div>
		<b-container v-else-if="!error">
			<empty-result v-if="!error" class="my-5">
				No clients were found matching your criteria
				<template #content>
					<b-button variant="primary" @click="refresh()" class="mt-4">Refresh</b-button>
				</template>
			</empty-result>
		</b-container>
		<b-container v-else>
			<error-alert class="m-5">{{ error }}</error-alert>
		</b-container>
	</div>
</template>

<script type="text/javascript">
export default {
	name: "ClientIndex",
	props: {
		selecting: {
			default: false,
		},
		selected: {
			default: () => [],
		},
		filters: {
			type: Object,
			default: () => {},
		},
		fields: {
			type: Array,
			default: () => [
				{
					key: "name",
					label: "Name",
					sortable: true,
					isRowHeader: true,
				},
				{
					key: "phone",
					label: "Phone",
					sortable: true,
				},
				{
					key: "fax",
					label: "Fax",
					sortable: true,
				},
				{
					key: "email",
					label: "Group Email",
					sortable: true,
				},
				{
					key: "street_address_1",
					label: "Address",
					sortable: true,
				},
				{
					key: "city",
					label: "City",
					sortable: true,
				},
				{
					key: "state",
					label: "State",
					sortable: true,
				},
				{
					key: "zip",
					label: "Zip",
					sortable: true,
				},
				{
					key: "created",
					label: "Added",
					sortable: true,
				},
				{
					key: "status",
					label: "Status",
					sortable: true,
				},
				{
					key: "active",
					label: "Active",
					sortable: true,
				},
				// {
				// 	key: 'show_details',
				// 	label: 'Details',
				// 	sortable: false
				// }
			],
		},
	},
	data() {
		return {
			data: [],
			pagination: {},
			page: 1,
			loading: true,
			error: false,
			initialLoaded: false,
			sortBy: undefined, // For table to sync with
			sortDesc: undefined, // For table to sync with
			sortField: undefined, // For API request
			sortDirection: undefined, // For API request
			sortAliases: {
				"created_by_user.full_name": "CreatedByUser.first_name",
				"modified_by_user.full_name": "ModifiedByUser.first_name",
			},
			selectedResults: [],
		};
	},
	computed: {
		isEmpty() {
			if (!this.data) {
				return true;
			}

			if (!this.data.length) {
				return true;
			}

			return this.data.length <= 0;
		},
		showLoading() {
			if (!this.initialLoaded) {
				return true;
			}

			if (this.data.length == 0 && this.loading) {
				return true;
			}

			return false;
		},
	},
	watch: {
		// Adding a watcher for filters doesn't work, needs a
		// deep watcher.

		loading(newVal) {
			this.$emit("update:loading", newVal);
		},
		page(newVal, oldVal) {
			this.$emit("paged", newVal, oldVal);
			this.refresh(newVal);
		},
		selectedResults(a, b) {
			this.$emit("update:selected", a);
		},
		selecting(newVal) {
			if (!newVal) {
				this.unselectAll();
			}
		},
	},
	mounted() {
		this.refresh();
	},
	methods: {
		async refresh(page = null) {
			if (page != null) {
				this.page = page;
			}

			var requestFilters = Object.assign({}, this.filters);
			var requestParams = Object.assign({}, requestFilters);

			requestParams.page = this.page;
			if (this.sortField) {
				requestParams.sort = this.sortField;
			}
			if (this.sortDirection) {
				requestParams.direction = this.sortDirection;
			}

			try {
				this.loading = true;
				this.error = false;

				const response = await this.$store.dispatch("clients/index", requestParams);

				this.data = response.data;
				this.pagination = response.pagination;

				if (response.pagination.page) {
					this.page = response.pagination.page;
				}

				if (response.pagination.direction == "desc") {
					this.sortDesc = true;
				}
				if (response.pagination.direction == "asc") {
					this.sortDesc = false;
				}

				this.$emit("refreshed", this.data, this.pagination);
			} catch (e) {
				console.error(e);

				this.error = e.response.data.message;
			} finally {
				this.loading = false;
				this.initialLoaded = true;
			}
		},
		sorted(params) {
			// Allow aliasing sort fields for API
			if (this.sortAliases[params.sortBy]) {
				this.sortField = this.sortAliases[params.sortBy];
			} else {
				this.sortField = params.sortBy;
			}

			this.sortDirection = params.sortDesc ? "desc" : "asc";
			this.refresh();

			this.$emit("sorted", this.sortField, this.sortDirection);
		},
		clickedRow(row) {
			if (this.selecting) {
				// Look for index in data by item ID
				var index = this.selectedResults
					.map(function (item) {
						return item.id;
					})
					.indexOf(row.id);

				// Push the item to selected results if not found
				if (index === -1) {
					this.selectedResults.push(row);
					this.$emit("selected", row);
				} else {
					// Remove if index found
					this.selectedResults.splice(index, 1);
					this.$emit("unselected", row);
				}
			} else {
				// Emit event (for viewing)
				this.$emit("clicked", row);
			}
		},
		selectedRow(rows) {
			// The entire list is passed
			this.selectedResults = rows;
		},
		selectAll() {
			this.selectedResults = this.data.slice(0);
		},
		unselectAll() {
			this.selectedResults = [];
		},
	},
};
</script>
