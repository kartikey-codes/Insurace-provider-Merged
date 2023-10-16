<template>
	<div>
		<page-header>
			<template #title>Licenses</template>
		</page-header>

		<b-card v-if="!isClientOwner">
			<b-alert show variant="warning" class="m-2 p-4 p-lg-5 shadow-sm">
				<h3 class="h3">
					<font-awesome-icon icon="exclamation-triangle" fixed-width />
					<span>Must be owner</span>
				</h3>
				<p class="mb-0">Licenses may only be viewed or modified by the owner of your organization.</p>
			</b-alert>
		</b-card>

		<div v-else>
			<b-row class="my-4">
				<b-col cols="12" xl="6" class="mb-4">
					<b-card no-body class="shadow-sm">
						<b-card-body>
							<p class="text-muted">
								Additional licenses can be purchased to allow adding additional physicians. Charges will
								apply based on your subscription plan.
							</p>
							<p class="text-muted mb-4">
								Lowering your license count may cause existing physicians to be marked inactive and be
								disabled for use in cases.
							</p>
							<b-form-group
								label="New Total Licenses"
								label-cols="6"
								label-cols-md="4"
								label-cols-lg="5"
								label-cols-xl="4"
							>
								<b-input-group>
									<b-form-input
										v-model.number="newLicenses"
										type="number"
										min="1"
										max="999"
										:disabled="loading || updating"
									/>
								</b-input-group>
							</b-form-group>
						</b-card-body>
						<b-card-footer class="text-right">
							<b-button variant="primary" @click="submit" :disabled="updateDisabled">
								Update Licenses
							</b-button>
						</b-card-footer>
					</b-card>
				</b-col>

				<b-col cols="12" xl="6">
					<b-card-group class="shadow-sm">
						<b-card class="text-center mb-0">
							<p class="h4 font-weight-bold mb-1">
								<span v-if="!total" class="text-muted">&mdash;</span>
								<span v-else>{{ $filters.formatNumber(total) }}</span>
							</p>
							<p class="small text-muted mb-0">Total</p>
						</b-card>
						<b-card class="text-center mb-0">
							<p class="h4 font-weight-bold mb-1">
								<span v-if="!used" class="text-muted">&mdash;</span>
								<span v-else class="mx-0 px-0">
									{{ $filters.formatNumber(used) }}
								</span>
							</p>
							<p class="small text-muted mb-0">Used</p>
						</b-card>
						<b-card class="text-center mb-0">
							<p class="h4 font-weight-bold mb-1">
								<span v-if="!available" class="text-muted">&mdash;</span>
								<span v-else class="mx-0 px-0">
									{{ $filters.formatNumber(available) }}
								</span>
							</p>
							<p class="small text-muted mb-0">Available</p>
						</b-card>
					</b-card-group>
				</b-col>
			</b-row>
		</div>
	</div>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "ViewLicenses",
	components: {},
	data() {
		return {
			newLicenses: this.total || 1,
			updating: false,
		};
	},
	computed: {
		updateDisabled() {
			return this.loading || this.updating || this.total == this.newLicenses;
		},
		...mapGetters({
			user: "user",
			loading: "licenses/loading",
			total: "licenses/total",
			available: "licenses/available",
			used: "licenses/used",
			isClientOwner: "isClientOwner",
		}),
	},
	mounted() {
		this.refresh();
	},
	methods: {
		async refresh() {
			await this.$store.dispatch("licenses/get");
		},
		async submit() {
			try {
				this.updating = true;

				const response = await this.$store.dispatch("subscription/update", {
					quantity: this.newLicenses,
				});

				this.$store.dispatch("notify", {
					variant: "primary",
					title: "Licenses Updated",
					message: "Your licenses have been updated.",
				});

				this.refresh();
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Error Updating Subscription",
					message:
						"Error updating licenses and/or subscription. Please contact support if the issue persists.",
				});
			} finally {
				this.updating = false;
			}
		},
	},
	watch: {
		total: {
			immediate: true,
			handler(val) {
				this.newLicenses = val;
			},
		},
	},
};
</script>
