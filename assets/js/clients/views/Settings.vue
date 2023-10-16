<template>
	<div>
	  <page-header>
		<template #title> Settings </template>
	  </page-header>
  
	  <b-container fluid>
		<b-row>
		  <b-col cols="12" lg="3" xl="2" class="d-none d-lg-block">
			<b-nav vertical pills class="my-4">
			  <b-nav-item title="My Account" active-class="active" :to="{ name: 'account' }">
				<span>My Account</span>
			  </b-nav-item>
			  <b-nav-item title="Organization" active-class="active" :to="{ name: 'organization' }">
				<span>Organization</span>
			  </b-nav-item>
			  <b-nav-item  title="Integrations" active-class="active" :to="{ name: 'settings.integrations' }">
				<span>Demo Integrations</span>
			  </b-nav-item>
			  <b-nav-item  title="Users" active-class="active" :to="{ name: 'users' }">
				<span>Users</span>
			  </b-nav-item>
			  <b-nav-item  title="Roles" active-class="active" :to="{ name: 'roles' }">
				<span>Roles</span>
			  </b-nav-item>
			  <b-nav-item  title="Licenses" active-class="active" :to="{ name: 'licenses' }">
				<span>Licenses</span>
			  </b-nav-item>
			  <b-nav-item title="Subscription" active-class="active" :to="{ name: 'subscription' }">
				<span>Subscription</span>
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
  
  <script>
  import axios from "axios";
  
  export default {
	name: "Settings",
	components: {},
	data() {
	  return {
		isAuthorized: false,
	  };
	},
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
	},
  };
  </script>
  