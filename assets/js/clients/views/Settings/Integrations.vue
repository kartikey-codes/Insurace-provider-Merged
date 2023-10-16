<template>
	<div>
	  <page-header>
		<template #title>Integrations</template>
	  </page-header>
  
	  <b-alert show variant="light" class="mt-4">
		<font-awesome-icon icon="info-circle" fixed-width class="mr-3" />
		<span>
		  Integration settings are required to utilize some features of {{ appName }}. These are primarily
		  connections to third-party service providers.
		</span>
	  </b-alert>
  
	  <loading-indicator v-if="loading && !loaded" class="my-5" />
	  <b-tabs v-else class="my-4">
		<b-tab title="Mirth Connect" active class="bg-white border border-top-0 rounded-bottom shadow-sm">
		  <mirth-connect @saved="refresh" />
		</b-tab>
		<b-tab title="FTP Server" class="bg-white border border-top-0 rounded-bottom shadow-sm">
		  <ftp-configuration @saved="refresh" />
		</b-tab>
	  </b-tabs>
	</div>
  </template>
  
  <script>
  import { mapGetters } from "vuex";
  import MirthConnect from "@/clients/components/Integrations/MirthConnect.vue";
  import FtpConfiguration from "@/clients/components/Integrations/FtpConfiguration.vue"; // Replace with the actual path
  
  export default {
	name: "ViewSettingsIntegrations",
	components: {
	  MirthConnect,
	  FtpConfiguration,
	},
	data() {
	  return {
		loaded: false,
	  };
	},
	computed: {
	  ...mapGetters({
		appName: "appName",
		integrations: "integrations/all",
		loading: "integrations/loadingAll",
	  }),
	},
	mounted() {
	  this.refresh();
	},
	methods: {
	  async refresh() {
		await this.$store.dispatch("integrations/getAll");
		this.loaded = true;
	  },
	},
  };
  </script>
  