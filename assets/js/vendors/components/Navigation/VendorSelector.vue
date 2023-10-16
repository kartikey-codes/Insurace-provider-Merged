<template>
	<loading-indicator v-if="showLoading" class="my-1" size="2x" />
	<b-nav-item-dropdown v-else right title="Select vendor" menu-class="shadow" :disabled="disabled">
		<template #button-content>
			<div class="d-inline-flex align-items-center">
				<b-avatar size="sm" variant="light" class="mr-2">
					<font-awesome-icon icon="building" class="mx-0" />
				</b-avatar>
				<span class="font-weight-bold mr-1">{{ activeVendorName }}</span>
			</div>
		</template>
		<b-dropdown-header>Select vendor...</b-dropdown-header>
		<b-dropdown-item
			v-for="vendor in vendors"
			:key="vendor.id"
			@click="clicked(vendor)"
			:active="vendor.id == adminVendorId"
		>
			<div class="d-flex justify-content-between align-items-center">
				<div class="text-left">{{ vendor.name }}</div>
				<div class="ml-4 text-right">
					<b-badge pill :variant="vendor.active ? 'success' : 'light'" class="text-center">
						<font-awesome-icon icon="check" class="mx-0" />
					</b-badge>
				</div>
			</div>
		</b-dropdown-item>
	</b-nav-item-dropdown>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "GlobalVendorSelector",
	props: {
		disabled: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		showLoading() {
			return this.loading && this.isEmpty;
		},
		isEmpty() {
			return !this.vendors || this.vendors.length <= 0;
		},
		activeVendorName() {
			for (var i = 0; i < this.vendors.length; i++) {
				if (this.vendors[i].id === this.adminVendorId) {
					return this.vendors[i].name;
				}
			}
			return this.adminVendorId ? "Vendor " + this.adminVendorId + " (inactive)" : "";
		},
		...mapGetters({
			isAdmin: "isAdmin",
			adminVendorId: "adminVendorId",
			vendors: "vendors/active",
			loading: "vendors/loadingActive",
		}),
	},
	mounted() {
		this.$root.$on("update:vendor", (response) => {
			this.refresh();
		});
		this.$root.$on("select:vendor:view", (response) => {
			this.refresh();
		});
		this.refresh();
	},
	methods: {
		refresh() {
			this.$store.dispatch("vendors/getActive");
		},
		async clicked(entity) {
			let promise = this.$store.dispatch("setAdminVendorId", entity.id);
			this.$root.$emit("select:vendor:selector", {
				id: entity.id,
				promise: promise,
			});
		},
	},
};
</script>
