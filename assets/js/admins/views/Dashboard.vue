<template>
	<b-container class="my-4 my-lg-5 pb-5">
		<b-row>
			<b-col cols="12" class="mb-4">
				<h1 class="h3">Welcome, {{ user.first_name }}</h1>
				<p v-if="user.last_login" class="small text-muted mb-0">
					Your last login was {{ $filters.fromNow(user.last_login) }} on
					{{ $filters.formatTimestamp(user.last_login) }}.
				</p>
			</b-col>
		</b-row>
		<b-row class="mb-lg-5">
			<b-col cols="12" lg="6" class="mb-4 mb-lg-0">
				<h6 class="h6 text-muted">View All</h6>
				<b-list-group class="shadow-sm">
					<b-list-group-item action to="/clients" class="d-flex justify-content-between align-items-center">
						<div>
							<font-awesome-icon icon="list" fixed-width class="text-muted" />
							<span>Clients</span>
						</div>
						<font-awesome-icon icon="chevron-right" />
					</b-list-group-item>
					<b-list-group-item action to="/vendors" class="d-flex justify-content-between align-items-center">
						<div>
							<font-awesome-icon icon="list" fixed-width class="text-muted" />
							<span>Vendors</span>
						</div>
						<font-awesome-icon icon="chevron-right" />
					</b-list-group-item>
					<b-list-group-item action to="/users" class="d-flex justify-content-between align-items-center">
						<div>
							<font-awesome-icon icon="list" fixed-width class="text-muted" />
							<span>Users</span>
						</div>
						<font-awesome-icon icon="chevron-right" />
					</b-list-group-item>
				</b-list-group>
			</b-col>
			<b-col cols="12" lg="6" class="mb-4 mb-lg-0">
				<h6 class="h6 text-muted">Switch App Area</h6>
				<b-list-group class="shadow-sm">
					<b-list-group-item action href="/client" class="d-flex justify-content-between align-items-center">
						<div>
							<font-awesome-icon icon="external-link-alt" fixed-width class="text-muted" />
							<span>Client App</span>
						</div>
						<font-awesome-icon icon="chevron-right" />
					</b-list-group-item>
					<b-list-group-item action href="/vendor" class="d-flex justify-content-between align-items-center">
						<div>
							<font-awesome-icon icon="external-link-alt" fixed-width class="text-muted" />
							<span>Vendor App</span>
						</div>
						<font-awesome-icon icon="chevron-right" />
					</b-list-group-item>
				</b-list-group>
			</b-col>
		</b-row>
		<b-row class="mb-4">
			<b-col cols="12">
				<b-card no-body>
					<b-tabs card active-nav-item-class="font-weight-bold">
						<b-tab no-body>
							<template #title>Online ({{ onlineUsers.length }})</template>
							<b-list-group v-if="onlineUsers.length > 0" flush>
								<b-list-group-item
									v-for="user in onlineUsers"
									:key="user.id"
									class="d-flex justify-content-between align-items-center p-4"
								>
									<div>
										<div>
											<span class="font-weight-bold">{{ user.full_name }}</span>
											<b-badge v-if="user.admin" variant="primary">Admin</b-badge>
										</div>
										<div class="text-muted small">
											Last seen {{ $filters.fromNow(user.last_seen) }}
										</div>
									</div>
									<div>
										<div v-if="user.client && user.client.name">
											<b-badge variant="primary" style="width: 5em">Client</b-badge>
											<span class="text-muted">{{ user.client.name }}</span>
										</div>
										<div v-if="user.vendor && user.vendor.name">
											<b-badge variant="info" style="width: 5em">Vendor</b-badge>
											<span class="text-muted">{{ user.vendor.name }}</span>
										</div>
									</div>
								</b-list-group-item>
							</b-list-group>
							<b-spinner v-else-if="loadingOnline" variant="primary" class="d-flex mx-auto my-4" />
							<b-alert v-else show variant="light" class="p-4"> No users are online right now. </b-alert>
						</b-tab>
						<b-tab no-body>
							<template #title>Actions</template>
							<b-list-group flush>
								<b-list-group-item class="d-flex justify-content-between align-items-center p-4">
									<div>
										<div class="font-weight-bold">Assign Submitted Appeals To Vendors</div>
										<div class="small text-muted">
											Find submitted appeals and match to an appropriate vendor.
										</div>
										<div class="small text-danger">
											This process runs automatically. This option is for manually initiating the
											assignment process.
										</div>
									</div>
									<div>
										<b-spinner v-if="assigningAppeals" variant="primary" />
										<b-button
											v-else
											variant="primary"
											@click="assignAppeals"
											:disabled="assigningAppeals"
										>
											Assign
										</b-button>
									</div>
								</b-list-group-item>

								<b-list-group-item class="d-flex justify-content-between align-items-center p-4">
									<div>
										<div class="font-weight-bold">Test Document</div>
										<div class="small text-muted">
											View the test PDF document used to diagnose PDF generation.
										</div>
									</div>
									<div>
										<b-spinner v-if="assigningAppeals" variant="primary" />
										<b-button
											v-else
											variant="primary"
											href="/admin/api/documents/test"
											target="_blank"
										>
											Open
										</b-button>
									</div>
								</b-list-group-item>
							</b-list-group>
						</b-tab>
						<b-tab no-body>
							<template #title>Environment</template>
							<b-row class="mb-0">
								<b-col cols="12" class="px-4 py-2">
									<b-button
										@click="refreshStatistics()"
										:disabled="loadingStatistics"
										variant="light"
										size="sm"
										class="mb-0"
									>
										<font-awesome-icon icon="sync" :spin="loadingStatistics" fixed-width />
										<span>Refresh</span>
									</b-button>
								</b-col>
							</b-row>
							<b-card-body v-for="(values, name) in statistics" :key="name">
								<h5 class="text-muted">{{ name }}</h5>
								<b-list-group class="shadow-sm">
									<b-list-group-item
										v-for="(value, key, index) in values"
										:key="key"
										:index="index"
										class="d-flex justify-content-between align-items-center"
									>
										<div class="small text-muted">{{ key }}</div>
										<div>{{ value }}</div>
									</b-list-group-item>
								</b-list-group>
							</b-card-body>
						</b-tab>
					</b-tabs>
				</b-card>
			</b-col>
		</b-row>
	</b-container>
</template>

<script>
import { mapGetters } from "vuex";

export default {
	name: "AdminDashboard",
	components: {},
	computed: mapGetters({
		user: "user",
		loadingOnline: "users/loadingOnline",
		onlineUsers: "users/online",
		statistics: "statistics",
		loadingStatistics: "loadingStatistics",
		assigningAppeals: "assigningAppeals",
	}),
	mounted() {
		this.$store.dispatch("getStatistics");
		this.$store.dispatch("users/getOnline");
	},
	data() {
		return {};
	},
	methods: {
		refreshStatistics() {
			this.$store.dispatch("getStatistics");
		},
		async assignAppeals() {
			try {
				const response = await this.$store.dispatch("assignAppealsToVendors");

				this.$bvToast.toast(
					`${response.submitted} submitted appeals found, and ${response.assigned} were assigned to vendors.`,
					{
						title: "Assign Appeals",
						autoHideDelay: 5000,
						appendToast: true,
						solid: true,
						variant: "success",
					}
				);
			} catch (e) {
				console.error(e);
				alert(e.response.data.message);
			}
		},
	},
};
</script>
