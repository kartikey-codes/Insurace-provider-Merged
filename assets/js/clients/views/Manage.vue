<template>
	<div>
		<page-header class="d-none d-lg-block">
			<template #title>Manage</template>
		</page-header>

		<b-container fluid>
			<b-row>
				<b-col cols="12" lg="3" xl="2" class="d-none d-lg-block">
					<b-nav vertical pills class="my-4">
						<b-nav-item title="Agencies" active-class="active" :to="{ name: 'agencies' }">
							<span>Agencies</span>
						</b-nav-item>
						<b-nav-item title="Audit Reviewers" active-class="active" :to="{ name: 'auditReviewers' }">
							<span>Audit Reviewers</span>
						</b-nav-item>
						<b-nav-item title="Facilities" active-class="active" :to="{ name: 'facilities' }">
							<span>Facilities</span>
						</b-nav-item>
						<b-nav-item
							title="Insurance Providers"
							active-class="active"
							:to="{ name: 'insuranceProviders' }"
						>
							<span>Insurance Providers</span>
						</b-nav-item>
						<b-nav-item title="Physicians" active-class="active" :to="{ name: 'clientEmployees' }">
							<span>Physicians</span>
						</b-nav-item>
						<b-nav-item title="Services" active-class="active" :to="{ name: 'services' }">
							<span>Services</span>
						</b-nav-item>
					</b-nav>
				</b-col>
				<b-col cols="12" lg="9" xl="10">
					<router-view />
				</b-col>
			</b-row>
		</b-container>
	</div>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "ViewManage",
	components: {},
	data() {
		return {};
	},
	computed: mapGetters({}),
	methods: {},
};
</script>
