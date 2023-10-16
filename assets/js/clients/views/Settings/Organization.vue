<template>
	<div>
		<page-header>
			<template #title>Organization</template>
		</page-header>

		<loading-indicator v-if="loading" class="my-5" />
		<div v-else class="my-4">
			<b-alert :show="!isClientAdmin" variant="warning">
				Only administrator users are allowed to change settings.
			</b-alert>

			<b-row>
				<b-col cols="12">
					<div class="d-flex justify-content-between align-items-center mb-4">
						<div class="media align-items-center">
							<svg
								xmlns="http://www.w3.org/2000/svg"
								width="48"
								height="48"
								fill="currentColor"
								class="bi bi-building align-self-start mr-3 text-primary"
								viewBox="0 0 16 16"
							>
								<path
									d="M4 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1ZM4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1ZM7.5 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1ZM4.5 8a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Z"
								/>
								<path
									d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V1Zm11 0H3v14h3v-2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V15h3V1Z"
								/>
							</svg>
							<div class="media-body">
								<h5 class="h5 mt-0 mb-0">{{ clientName }}</h5>
								<p v-if="clientData.full_address" class="h6 text-muted">
									{{ clientData.full_address }}
								</p>
							</div>
						</div>
					</div>
				</b-col>
			</b-row>

			<p class="small text-muted">
				Registered {{ $filters.fromNow(clientData.created) }} on
				{{ $filters.formatDate(clientData.created) }}
			</p>

			<b-row>
				<b-col cols="12" md="6" class="mb-4">
					<b-list-group>
						<b-list-group-item v-if="clientData.contact_full_name">
							<p class="font-weight-bold mb-0">{{ clientData.contact_full_name }}</p>
							<p class="small text-muted mb-0">Primary Contact</p>
						</b-list-group-item>
						<b-list-group-item v-if="clientData.email">
							<p class="font-weight-bold mb-0">{{ $filters.formatPhone(clientData.email) }}</p>
							<p class="small text-muted mb-0">Primary Email</p>
						</b-list-group-item>
						<b-list-group-item v-if="clientData.phone">
							<p class="font-weight-bold mb-0">{{ $filters.formatPhone(clientData.phone) }}</p>
							<p class="small text-muted mb-0">Primary Phone Number</p>
						</b-list-group-item>
						<b-list-group-item v-if="clientData.fax">
							<p class="font-weight-bold mb-0">{{ $filters.formatPhone(clientData.fax) }}</p>
							<p class="small text-muted mb-0">Primary Fax Number</p>
						</b-list-group-item>
						<b-list-group-item v-if="clientData.npi_number">
							<p class="font-weight-bold mb-0">{{ clientData.npi_number }}</p>
							<p class="small text-muted mb-0">NPI Number</p>
						</b-list-group-item>
					</b-list-group>
				</b-col>
				<b-col cols="12" md="6" class="mb-4">
					<b-list-group>
						<b-list-group-item
							:to="{ name: 'users' }"
							class="d-flex justify-content-between align-items-center py-4"
						>
							<div>
								<h6 class="mb-2">
									<font-awesome-icon icon="users" fixed-width />
									<span class="font-weight-bold">View Users </span>
								</h6>
								<p class="small text-muted mb-0">
									Create new user accounts or manage existing users in your organization.
								</p>
							</div>
							<font-awesome-icon icon="chevron-right" />
						</b-list-group-item>
						<b-list-group-item
							:to="{ name: 'roles' }"
							class="d-flex justify-content-between align-items-center py-4"
						>
							<div>
								<h6 class="mb-2">
									<font-awesome-icon icon="graduation-cap" fixed-width />
									<span class="font-weight-bold"> View Roles </span>
								</h6>
								<p class="small text-muted mb-0">
									Assign users to roles to grant permissions and control application access.
								</p>
							</div>
							<font-awesome-icon icon="chevron-right" />
						</b-list-group-item>

						<b-list-group-item
							:to="{ name: 'settings.integrations' }"
							class="d-flex justify-content-between align-items-center py-4"
						>
							<div>
								<h6 class="mb-2">
									<font-awesome-icon icon="sync" fixed-width />
									<span class="font-weight-bold">Set up Integrations </span>
								</h6>
								<p class="small text-muted mb-0">
									Manage connections to supported third-party service APIs.
								</p>
							</div>
							<font-awesome-icon icon="chevron-right" />
						</b-list-group-item>
					</b-list-group>
				</b-col>
			</b-row>
		</div>
	</div>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "ViewOrganization",
	components: {},
	data() {
		return {};
	},
	computed: {
		...mapGetters({
			user: "user",
			clientName: "clientName",
			isClientOwner: "isClientOwner",
			isClientAdmin: "isClientAdmin",
			loading: "clientSettings/loading",
			clientData: "clientSettings/data",
		}),
	},
	mounted() {
		this.refresh();
	},
	methods: {
		async refresh() {
			this.$store.dispatch("clientSettings/get");
		},
	},
};
</script>
