<template>
	<FullPageLoader v-if="showLoading" />
	<div v-else style="position: relative">
		<app-navigation />
		<loading-indicator v-if="loading" class="my-5" />
		<router-view v-else />
		<inactivity-detection @warn="warnInactive" @expired="inactive" />
	</div>
</template>

<script>
import { mapGetters } from "vuex";
import AppNavigation from "@/clients/components/Navigation/Navigation.vue";
import InactivityDetection from "@/shared/components/InactivityDetection.vue";
import FullPageLoader from "@/clients/components/Layout/full-page-loader.vue";

export default {
	name: "App",
	components: {
		AppNavigation,
		InactivityDetection,
		FullPageLoader,
	},
	created() {
		this.stateTimer = setInterval(() => {
			this.$store.dispatch("updateState");
		}, 60000); // 1 minute
	},
	computed: {
		showLoading() {
			return this.clientId ? false : true;
		},
		...mapGetters({
			incomingDocumentCount: "incomingDocuments/count",
			apiToken: "apiToken",
			user: "user",
			clientId: "clientId",
		}),
	},
	mounted() {
		this.$root.$on("select:client:selector", this.switchClients);
	},
	data() {
		return {
			stateTimer: null, // reference to timer
			loading: false,
		};
	},
	methods: {
		async switchClients(params) {
			this.loading = true;
			let entityId = params.id;
			let promise = params.promise;
			promise.finally(() => {
				this.loading = false;
				this.routerViewKeyIndex += 1;
				console.log("App: client changed to " + entityId + " by selector");
			});
		},
		warnInactive() {
			console.warn("Reached warning for inactivity");
		},
		inactive() {
			console.warn("Inactivity limit reached. Logging out.");
			window.location = "/logout?reason=inactive";
		},
	},
	destroyed() {
		clearInterval(this.stateTimer);
	},
	watch: {
		clientId: {
			immediate: false,
			handler(val) {
				this.$store.dispatch("updateState");
			},
		},
		incomingDocumentCount: {
			immediate: false,
			handler(val, oldVal) {
				// Do not notify on initial load or same value
				if (oldVal < 0 || val == oldVal) {
					return;
				}

				// Incoming document moved out or deleted
				if (val < oldVal) {
					return;
				}

				const difference = val - oldVal;
				const message =
					difference == 1
						? `There is ${difference} new incoming document.`
						: `There are ${difference} new incoming documents.`;

				this.$store.dispatch("notify", {
					variant: "primary",
					title: "Incoming Document",
					message: message,
					important: false,
				});
			},
		},
	},
};
</script>
