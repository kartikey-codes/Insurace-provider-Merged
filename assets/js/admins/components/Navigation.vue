<template>
	<b-navbar toggleable="lg" type="dark" variant="dark">
		<b-navbar-brand to="/" class="mr-lg-4">
			<span>{{ appName }} Administration</span>
		</b-navbar-brand>

		<b-navbar-toggle target="nav-collapse" />

		<b-collapse id="nav-collapse" is-nav>
			<b-navbar-nav>
				<b-nav-item to="/clients" class="mr-lg-2">
					<span>Clients</span>
				</b-nav-item>
				<b-nav-item to="/vendors" class="mr-lg-2">
					<span>Vendors</span>
				</b-nav-item>
				<b-nav-item to="/users">
					<span>Users</span>
				</b-nav-item>
			</b-navbar-nav>

			<!-- Right aligned nav items -->
			<b-navbar-nav class="ml-auto">
				<b-nav-item-dropdown right menu-class="shadow">
					<!-- Using button-content slot -->
					<template #button-content>
						<div class="d-inline-flex align-items-center">
							<b-avatar size="sm" variant="light" class="mr-2" />
							<span v-text="$authUser.full_name" class="font-weight-bold" />
						</div>
					</template>

					<b-dropdown-item href="/client" exact>
						<div>
							<font-awesome-icon icon="external-link-alt" fixed-width />
							<span>Client App</span>
						</div>
						<div class="text-muted small">Switch to Client area</div>
					</b-dropdown-item>

					<b-dropdown-item href="/vendor" exact>
						<div>
							<font-awesome-icon icon="external-link-alt" fixed-width />
							<span>Vendor App</span>
						</div>
						<div class="text-muted small">Switch to Vendor area</div>
					</b-dropdown-item>

					<b-dropdown-divider />

					<b-dropdown-item href="/logout">
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

export default {
	name: "AdminsNavigation",
	computed: mapGetters({
		appName: "appName",
		isAdmin: "isAdmin",
		isVendor: "isVendor",
	}),
};
</script>
