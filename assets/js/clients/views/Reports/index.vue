<template>
	<div>
		<page-header>
			<template #title> Reports </template>
		</page-header>

		<b-container fluid>
			<b-row>
				<b-col cols="12" lg="3" xl="2" class="mt-4">
					<b-nav vertical pills>
						<b-nav-item title="Overview" active-class="active" :to="{ name: 'reports' }" exact>
							<span>Overview</span>
						</b-nav-item>
						<b-nav-item
							title="User Performance"
							active-class="active"
							:to="{ name: 'reports.users' }"
							exact
						>
							<span>User Performance</span>
						</b-nav-item>
					</b-nav>
				</b-col>
				<b-col cols="12" lg="9" xl="10" class="my-4">
					<router-view />
				</b-col>
			</b-row>
		</b-container>
	</div>
</template>

<script>
export default {
	name: "ViewReportsIndex",
	mounted() {
		this.refresh();
	},
	methods: {
		async refresh() {
			await this.$store.dispatch("statistics/refresh");
		},
	},
};
</script>
