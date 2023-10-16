<template>
	<div>
		<app-navigation />
		<router-view />
	</div>
</template>

<script>
import { mapGetters } from "vuex";
import AppNavigation from "@/admins/components/Navigation.vue";

export default {
	name: "AdminsApp",
	components: {
		AppNavigation
	},
};
</script>
