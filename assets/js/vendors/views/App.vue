<template>
	<full-page-loader v-if="showLoading" />
	<div v-else style="position: relative;">
		<navigation />
		<loading-indicator
			v-if="loading"
			class="my-5"
		/>
		<router-view
			v-else
			:key="routerViewKey"
		/>
		<inactivity-detection
			@warn="warnInactive"
			@expired="inactive"
		/>
	</div>
</template>

<script>
import { mapGetters } from "vuex";
import Navigation from "@/vendors/components/Navigation.vue";
import InactivityDetection from "@/shared/components/InactivityDetection.vue";
import FullPageLoader from "@/clients/components/Layout/full-page-loader.vue";

export default {
	name: "App",
	components: {
		Navigation,
		InactivityDetection,
		FullPageLoader
	},
	created() {
		//this.$store.dispatch("updateState");

		this.stateTimer = setInterval(() => {
			//this.$store.dispatch("updateState");
		}, this.stateInterval);
	},
	computed: {
		showLoading() {
			return this.vendorId ? false : true;
		},
		routerViewKey() {
			return this.routerViewKeyIndex + ", " + this.$route.fullPath;
		},
		...mapGetters({
			user: "user",
			vendorId: "vendorId"
		}),
	},
	mounted() {
		this.$root.$on("select:vendor:selector", (params) => {
			this.loading = true;
			let entityId = params.id;
			let promise = params.promise;
			promise.finally(() => {
				this.loading = false;
				this.routerViewKeyIndex += 1;
				console.log(
					"App: vendor changed to " + entityId + " by selector"
				);
			});
		});		
	},
	data() {
		return {
			stateTimer: null, // reference to timer
			stateInterval: 30000, // milliseconds between updates
			routerViewKeyIndex: 0,
			loading: false,
		};
	},
	methods: {
		warnInactive() {
			console.warn("Reached warning for inactivity");
		},
		inactive() {
			console.warn("Inactivity limit reached. Logging out.");
			window.location = "/logout?reason=inactive";
		},
	},
	watch: {
		vendorId: {
			immediate: false,
			handler(val) {
				//this.$store.dispatch("updateState");
			},
		},
	},
	destroyed() {
		clearInterval(this.stateTimer);
	},
};
</script>
