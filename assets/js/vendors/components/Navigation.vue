<template>
	<b-navbar toggleable="lg" type="dark" variant="dim" sticky>
		<b-navbar-brand to="/" exact :active="$route.name == 'dashboard'" title="Dashboard">
			<div class="d-flex align-items-center">
				<font-awesome-icon icon="home" fixed-width class="mr-2" />
				<span>{{ appName }}</span>
			</div>
		</b-navbar-brand>

		<b-navbar-toggle target="nav_collapse" />

		<b-collapse is-nav id="nav_collapse">
			<b-navbar-nav class="ml-auto mt-4 mt-lg-0">
				<vendor-selector class="mb-2 mb-lg-0 mr-lg-2" v-if="isAdmin" />

				<b-nav-item-dropdown right menu-class="shadow" class="ml-0 ml-lg-2">
					<!-- Using button-content slot -->
					<template #button-content>
						<div class="d-inline-flex align-items-center">
							<b-avatar size="sm" variant="light" class="mr-2" />
							<span v-text="user.full_name" class="font-weight-bold" />
						</div>
					</template>

					<router-link to="/" exact custom v-slot="{ href, navigate, isActive }">
						<b-dropdown-item :href="href" @click="navigate" :active="isActive">
							<font-awesome-icon icon="home" fixed-width />
							<span>Dashboard</span>
						</b-dropdown-item>
					</router-link>

					<b-dropdown-divider v-if="isClient || isAdmin" />

					<b-dropdown-item v-if="isAdmin" href="/admin" exact>
						<div>
							<font-awesome-icon icon="external-link-alt" fixed-width />
							<span>Admin App</span>
						</div>
						<div class="text-muted small">Switch to Administration area</div>
					</b-dropdown-item>

					<b-dropdown-item v-if="isClient" href="/client" exact>
						<div>
							<font-awesome-icon icon="external-link-alt" fixed-width />
							<span>Client App</span>
						</div>
						<div class="text-muted small">Switch to Client area</div>
					</b-dropdown-item>

					<b-dropdown-divider />

					<b-dropdown-item @click="confirmSignout">
						<font-awesome-icon icon="sign-out-alt" fixed-width />
						<span>Log Out</span>
					</b-dropdown-item>
				</b-nav-item-dropdown>
			</b-navbar-nav>
		</b-collapse>
	</b-navbar>
</template>

<script>
import { mapGetters } from "vuex";
import VendorSelector from "@/vendors/components/Navigation/VendorSelector.vue";

export default {
	name: "NavigationBar",
	components: {
		VendorSelector,
	},
	computed: mapGetters({
		appName: "appName",
		user: "user",
		isAdmin: "isAdmin",
		isClient: "isClient",
	}),
	methods: {
		confirmSignout() {
			this.$store.dispatch("logOut");
		},
	},
};
</script>
