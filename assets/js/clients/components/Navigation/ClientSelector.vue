<template>
	<loading-indicator v-if="showLoading" class="my-1" size="2x" />
	<b-nav-item-dropdown v-else right title="Select client" :disabled="disabled" menu-class="shadow">
		<template #button-content>
			<div class="d-inline-flex align-items-center">
				<b-avatar size="sm" variant="light" class="mr-2">
					<font-awesome-icon icon="building" class="mx-0" />
				</b-avatar>
				<span class="font-weight-bold mr-1">{{ $filters.truncatedString(activeClientName, 16) }}</span>
			</div>
		</template>
		<b-dropdown-header>Select client...</b-dropdown-header>
		<b-dropdown-item
			v-for="client in clients"
			:key="client.id"
			@click="clicked(client)"
			:active="client.id == adminClientId"
		>
			<div class="d-flex justify-content-between align-items-center">
				<div class="text-left">{{ $filters.truncatedString(client.name, 32) }}</div>
				<div class="ml-4 text-right">
					<b-badge pill :variant="client.subscription_active ? 'success' : 'light'" class="text-center">
						<font-awesome-icon icon="credit-card" class="mx-0" />
						<span v-if="client.licenses">{{ client.licenses }}</span>
					</b-badge>
				</div>
			</div>
		</b-dropdown-item>
	</b-nav-item-dropdown>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "GlobalClientSelector",
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
			return !this.clients || this.clients.length <= 0;
		},
		activeClientName() {
			for (var i = 0; i < this.clients.length; i++) {
				if (this.clients[i].id === this.adminClientId) {
					return this.clients[i].name;
				}
			}
			return this.adminClientId ? "Client " + this.adminClientId + " (inactive)" : "";
		},
		...mapGetters({
			isAdmin: "isAdmin",
			adminClientId: "adminClientId",
			clients: "clients/active",
			loading: "clients/loadingActive",
		}),
	},
	mounted() {
		this.$root.$on("update:client", (response) => {
			this.refresh();
		});
		this.$root.$on("select:client:view", (response) => {
			this.refresh();
		});
		this.refresh();
	},
	methods: {
		refresh() {
			this.$store.dispatch("clients/getActive");
		},
		async clicked(entity) {
			let promise = this.$store.dispatch("setAdminClientId", entity.id);
			this.$root.$emit("select:client:selector", {
				id: entity.id,
				promise: promise,
			});
		},
	},
};
</script>
