<template>
	<b-navbar toggleable="lg" type="dark" variant="primary" sticky>
		<b-navbar-brand
			to="/"
			exact
			:active="$route.name == 'dashboard'"
			title="Dashboard"
			class="d-flex align-items-center"
		>
			<img :src="logoSrc" width="92" height="42" class="my-0 py-0" />
			<span class="sr-only">Dashboard</span>
		</b-navbar-brand>

		<b-navbar-toggle
			target="nav_collapse"
			label="Toggle main navigation menu"
			title="Toggle main navigation menu"
		/>

		<b-collapse is-nav id="nav_collapse">
			<b-navbar-nav>
				<!-- Incoming -->
				<router-link
					:to="{ name: 'incomingDocuments' }"
					title="Incoming Documents"
					custom
					v-slot="{ href, navigate, isActive }"
				>
					<b-nav-item :href="href" @click="navigate" :active="isActive">
						<span>Incoming</span>
						<b-badge
							pill
							v-if="incomingDocumentCount && incomingDocumentCount > 0"
							variant="danger"
							v-text="incomingDocumentCount"
							size="sm"
						/>
					</b-nav-item>
				</router-link>

				<!-- Outgoing -->
				<router-link
					:to="{ name: 'outgoingDocuments' }"
					title="Outgoing Documents"
					custom
					v-slot="{ href, navigate, isActive }"
				>
					<b-nav-item :href="href" @click="navigate" :active="isActive">
						<span>Outgoing</span>
						<b-badge
							pill
							v-if="outgoingDocumentCount && outgoingDocumentCount > 0"
							variant="danger"
							v-text="outgoingDocumentCount"
							size="sm"
						/>
					</b-nav-item>
				</router-link>

				<!-- Cases -->
				<router-link :to="{ name: 'cases' }" title="Cases" custom v-slot="{ href, navigate, isActive }">
					<b-nav-item :href="href" @click="navigate" :active="isActive">
						<span>Cases</span>
						<b-badge
							pill
							v-if="openCaseCount && openCaseCount > 0"
							variant="info"
							v-text="openCaseCount"
							size="sm"
						/>
					</b-nav-item>
				</router-link>

				<!-- Case Requests -->
				<router-link
					:to="{ name: 'caseRequests' }"
					title="Case Requests"
					custom
					v-slot="{ href, navigate, isActive }"
				>
					<b-nav-item :href="href" @click="navigate" :active="isActive">
						<span>Requests</span>
						<b-badge
							pill
							v-if="openCaseRequestCount && openCaseRequestCount > 0"
							variant="info"
							v-text="openCaseRequestCount"
							size="sm"
						/>
					</b-nav-item>
				</router-link>

				<!-- Appeals -->
				<router-link :to="{ name: 'appeals' }" title="Appeals" custom v-slot="{ href, navigate, isActive }">
					<b-nav-item :href="href" @click="navigate" :active="isActive">
						<span>Appeals</span>
						<b-badge
							pill
							v-if="openAppealCount && openAppealCount > 0"
							variant="info"
							v-text="openAppealCount"
							size="sm"
						/>
					</b-nav-item>
				</router-link>

				<!-- Patients -->
				<router-link :to="{ name: 'patients' }" title="Patients" custom v-slot="{ href, navigate, isActive }">
					<b-nav-item :href="href" @click="navigate" :active="isActive">
						<span>Patients</span>
					</b-nav-item>
				</router-link>

				<!-- Manage -->
				<b-nav-item-dropdown menu-class="shadow">
					<!-- Using button-content slot -->
					<template #button-content>
						<span>Manage</span>
					</template>

					<!-- Agencies -->
					<router-link
						:to="{ name: 'agencies' }"
						title="Agencies"
						custom
						v-slot="{ href, navigate, isActive }"
					>
						<b-dropdown-item :href="href" @click="navigate" :active="isActive">
							<span>Agencies</span>
						</b-dropdown-item>
					</router-link>

					<!-- Audit Reviewers -->
					<router-link
						:to="{ name: 'auditReviewers' }"
						title="Audit Reviewers"
						custom
						v-slot="{ href, navigate, isActive }"
					>
						<b-dropdown-item :href="href" @click="navigate" :active="isActive">
							<span>Audit Reviewers</span>
						</b-dropdown-item>
					</router-link>

					<!-- Facilities -->
					<router-link
						:to="{ name: 'facilities' }"
						title="Facilities"
						custom
						v-slot="{ href, navigate, isActive }"
					>
						<b-dropdown-item :href="href" @click="navigate" :active="isActive">
							<span>Facilities</span>
						</b-dropdown-item>
					</router-link>

					<!-- Insurance Providers -->
					<router-link
						:to="{ name: 'insuranceProviders' }"
						title="Insurance Providers"
						custom
						v-slot="{ href, navigate, isActive }"
					>
						<b-dropdown-item :href="href" @click="navigate" :active="isActive">
							<span>Insurance Providers</span>
						</b-dropdown-item>
					</router-link>

					<!-- Physicians -->
					<router-link
						:to="{ name: 'clientEmployees' }"
						title="Physicians"
						custom
						v-slot="{ href, navigate, isActive }"
					>
						<b-dropdown-item :href="href" @click="navigate" :active="isActive">
							<span>Physicians</span>
						</b-dropdown-item>
					</router-link>

					<!-- Services -->
					<router-link
						:to="{ name: 'services' }"
						title="Services"
						exact
						custom
						v-slot="{ href, navigate, isActive }"
					>
						<b-dropdown-item :href="href" @click="navigate" :active="isActive">
							<span>Services</span>
						</b-dropdown-item>
					</router-link>
				</b-nav-item-dropdown>

				<!-- More -->
				<b-nav-item-dropdown menu-class="shadow">
					<!-- Using button-content slot -->
					<template #button-content>
						<span>More</span>
					</template>

					<!-- Calendar -->
					<router-link
						:to="{ name: 'calendar' }"
						title="Calendar"
						custom
						v-slot="{ href, navigate, isActive }"
					>
						<b-dropdown-item :href="href" @click="navigate" :active="isActive">
							<span>Calendar New</span>
						</b-dropdown-item>
					</router-link>

					<!-- Library -->
					<router-link :to="{ name: 'library' }" title="Library" custom v-slot="{ href, navigate, isActive }">
						<b-dropdown-item :href="href" @click="navigate" :active="isActive">
							<span>Library</span>
						</b-dropdown-item>
					</router-link>

					<!-- Reports -->
					<router-link
						:to="{ name: 'reports' }"
						title="Reports"
						exact
						custom
						v-slot="{ href, navigate, isActive }"
					>
						<b-dropdown-item :href="href" @click="navigate" :active="isActive">
							<span>Reports</span>
						</b-dropdown-item>
					</router-link>

					<b-dropdown-divider v-if="isAuthorized" />

					<!-- <b-dropdown-header>Settings</b-dropdown-header> -->

					<router-link v-if="isAuthorized" :to="{ name: 'organization' }" custom v-slot="{ href, navigate, isActive }">
						<b-dropdown-item :href="href" @click="navigate" :active="isActive">
							<div>Organization</div>
						</b-dropdown-item>
					</router-link>

					<router-link v-if="isAuthorized" :to="{ name: 'settings.integrations' }" custom v-slot="{ href, navigate, isActive }">
						<b-dropdown-item :href="href" @click="navigate" :active="isActive">
							<span>Integrations</span>
						</b-dropdown-item>
					</router-link>
				</b-nav-item-dropdown>
			</b-navbar-nav>

			<!-- Global Search -->
			<b-navbar-nav class="ml-auto mt-2 mt-lg-0">
				<client-selector class="mb-2 mb-lg-0 mr-lg-2" v-if="isAdmin" />

				<b-nav-item-dropdown right class="ml-0 ml-lg-2" menu-class="shadow">
					<!-- Using button-content slot -->
					<template #button-content>
						<div class="d-inline-flex align-items-center">
							<b-avatar size="sm" variant="light" class="mr-2" />
							<span v-text="userInitials" class="d-none d-lg-inline d-xl-none font-weight-bold" />
							<span v-text="userFullName" class="d-inline d-lg-none d-xl-inline font-weight-bold" />
						</div>
					</template>

					<router-link to="/" exact custom v-slot="{ href, navigate, isActive }">
						<b-dropdown-item :href="href" @click="navigate" :active="isActive">
							<font-awesome-icon icon="home" fixed-width />
							<span>Dashboard</span>
						</b-dropdown-item>
					</router-link>

					<router-link  :to="{ name: 'account' }" custom v-slot="{ href, navigate, isActive }">
						<b-dropdown-item :href="href" @click="navigate" :active="isActive">
							<font-awesome-icon icon="cog" fixed-width />
							<span>Account Settings</span>
						</b-dropdown-item>
					</router-link>

					<b-dropdown-divider v-if="isAuthorized" />

					<router-link v-if="isAuthorized" :to="{ name: 'users' }" exact custom v-slot="{ href, navigate, isActive }">
						<b-dropdown-item :href="href" @click="navigate" :active="isActive">
							<font-awesome-icon icon="users" fixed-width />
							<span>Users</span>
						</b-dropdown-item>
					</router-link>

					<router-link v-if="isAuthorized" :to="{ name: 'roles' }" exact custom v-slot="{ href, navigate, isActive }">
						<b-dropdown-item :href="href" @click="navigate" :active="isActive">
							<font-awesome-icon icon="graduation-cap" fixed-width />
							<span>Roles</span>
						</b-dropdown-item>
					</router-link>

					<b-dropdown-divider v-if="isAdmin || isVendor" />

					<b-dropdown-item v-if="isAdmin" href="/admin" exact>
						<div>
							<font-awesome-icon icon="external-link-alt" fixed-width />
							<span>Admin App</span>
						</div>
						<div class="text-muted small">Switch to Administration area</div>
					</b-dropdown-item>

					<b-dropdown-item v-if="isAdmin || isVendor" href="/vendor" exact>
						<div>
							<font-awesome-icon icon="external-link-alt" fixed-width />
							<span>Vendor App</span>
						</div>
						<div class="text-muted small">Switch to Vendor area</div>
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

<!-- <script>
import { mapGetters } from "vuex";
import ClientSelector from "@/clients/components/Navigation/ClientSelector.vue";


export default {
	name: "NavigationBar",
	components: {
		ClientSelector,
	},
	props: {},
	data() {
		return {
			logoSrc: "/img/RevKeep_tag_white_72dpi.png",
		};
	},
	computed: mapGetters({
		appName: "appName",
		clientName: "clientName",
		user: "user",
		userFullName: "userFullName",
		userInitials: "userInitials",
		isAdmin: "isAdmin",
		isVendor: "isVendor",
		incomingDocumentCount: "incomingDocuments/count",
		outgoingDocumentCount: "outgoingDocuments/countNew",
		caseTypes: "caseTypes/all",
		onlineUsers: "users/online",
		hasOnlineUsers: "users/anyOnline",
		openCaseCount: "openCaseCount",
		openCaseRequestCount: "openCaseRequestCount",
		openAppealCount: "openAppealCount",
	}),
	methods: {
		confirmSignout() {
			this.$store.dispatch("logOut");
		},
	},
};
</script> -->


<script>
import { mapGetters } from "vuex";
import axios from "axios";
import ClientSelector from "@/clients/components/Navigation/ClientSelector.vue";

export default {
  name: "NavigationBar",
  components: {
    ClientSelector,
  },
  props: {},
  data() {
    return {
      logoSrc: "/img/RevKeep_tag_white_72dpi.png",
      isAuthorized: false,
    };
  },
  computed: mapGetters({
    appName: "appName",
    clientName: "clientName",
    user: "user",
    userFullName: "userFullName",
    userInitials: "userInitials",
    isAdmin: "isAdmin",
    isVendor: "isVendor",
    incomingDocumentCount: "incomingDocuments/count",
    outgoingDocumentCount: "outgoingDocuments/countNew",
    caseTypes: "caseTypes/all",
    onlineUsers: "users/online",
    hasOnlineUsers: "users/anyOnline",
    openCaseCount: "openCaseCount",
    openCaseRequestCount: "openCaseRequestCount",
    openAppealCount: "openAppealCount",
  }),
  created() {
    this.fetchAuthorization();
  },
  methods: {
    fetchAuthorization() {
      axios
        .get("/client/userpermission") // Replace with the endpoint of AlwaysTrueController
        .then((response) => {
          console.log(response.data.success);
          this.isAuthorized = response.data.success;
        })
        .catch((error) => {
          console.error(error);
        });
    },
    confirmSignout() {
      this.$store.dispatch("logOut");
    },
  },
};
</script>
