<template>
	<div>
		<page-header>
			<template #title>Subscription</template>
		</page-header>

		<b-card v-if="!isClientOwner">
			<b-alert show variant="warning" class="m-2 p-4 p-lg-5 shadow-sm">
				<h3 class="h3">
					<font-awesome-icon icon="exclamation-triangle" fixed-width />
					<span>Must be owner</span>
				</h3>
				<p class="mb-0">
					Subscription details may only be viewed or modified by the owner of your organization.
				</p>
			</b-alert>
		</b-card>

		<b-card v-else no-body class="shadow-sm my-4">
			<b-card-header> {{ appName }} Subscription </b-card-header>
			<b-card-body v-if="cancelled">
				<loading-indicator title="Signing out..." />
			</b-card-body>
			<b-card-body v-else-if="cancelling">
				<loading-indicator title="Cancelling your subscripton..." />
			</b-card-body>
			<b-card-body v-else-if="loading">
				<loading-indicator class="py-5" />
			</b-card-body>
			<b-card-body v-else-if="error">
				<b-alert show variant="danger">
					{{ error }}
				</b-alert>
			</b-card-body>
			<div v-else-if="!hasSubscription">
				<b-card-body>
					<b-alert show variant="warning" class="m-2 p-4 p-lg-5 shadow-sm">
						<h3 class="h3">
							<font-awesome-icon icon="exclamation-triangle" fixed-width />
							<span>Unable to retrieve subscription</span>
						</h3>
						<p class="mb-0">
							Your organization may not have an active subscription. Please complete the subscription
							setup to continue using the application.
						</p>
					</b-alert>
				</b-card-body>
				<b-card-footer class="text-right">
					<b-button href="/subscribe/client" variant="primary" size="lg"> Subscribe Now </b-button>
				</b-card-footer>
			</div>
			<b-card-body v-else class="p-4">
				<b-row>
					<b-col cols="12" lg="4" xl="6" class="mb-4">
						<h1 class="h3">
							{{ subscription.details.name }}
						</h1>
						<h2 class="h5 text-muted">
							{{ subscription.details.description }}
						</h2>
					</b-col>
					<b-col cols="12" lg="8" xl="6" class="mb-4">
						<b-list-group>
							<b-list-group-item
								v-if="subscription.details.recurringPrice"
								class="d-flex justify-content-between align-items-center"
							>
								<h6 class="text-muted mb-0">Rate</h6>
								<p class="mb-0 text-right">
									{{ $filters.currency(subscription.details.recurringPrice) }}
									per
									{{ subscription.details.recurringInterval }}
								</p>
							</b-list-group-item>
							<b-list-group-item class="d-flex justify-content-between align-items-center">
								<h6 class="text-muted mb-0">Start</h6>
								<p class="mb-0 text-right">
									{{
										$filters.formatUnixTimestamp(subscription.details.periodStart, {
											dateOnly: true,
										})
									}}
								</p>
							</b-list-group-item>
							<b-list-group-item class="d-flex justify-content-between align-items-center">
								<h6 class="text-muted mb-0">Next Billing</h6>
								<div class="text-right">
									<p class="mb-0">
										{{
											$filters.formatUnixTimestamp(subscription.details.periodEnd, {
												dateOnly: true,
											})
										}}
									</p>
									<p v-if="subscription.details.periodEnd" class="small text-muted mb-0">
										{{ $filters.fromNow(subscription.details.periodEnd) }}
									</p>
								</div>
							</b-list-group-item>
						</b-list-group>
					</b-col>
				</b-row>
			</b-card-body>
			<b-card-footer v-if="hasSubscription && !cancelled" class="text-right">
				<b-button
					v-if="!error"
					variant="danger"
					@click="cancelSubscription"
					:disabled="cancelling || cancelled || loading"
				>
					<loading-indicator size="1x" v-if="cancelling" />
					<span v-else>Cancel Subscription</span>
				</b-button>
			</b-card-footer>
		</b-card>
	</div>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "ViewSubscription",
	components: {},
	data() {
		return {
			error: false,
			cancelled: false,
			cancelling: false,
		};
	},
	computed: {
		hasSubscription() {
			if (this.cancelled) {
				return false;
			}

			return this.subscription && this.subscription.details ? true : false;
		},
		...mapGetters({
			appName: "appName",
			isClientOwner: "isClientOwner",
			user: "user",
			loading: "subscription/loading",
			subscription: "subscription/subscription",
		}),
	},
	mounted() {
		this.refresh();
	},
	methods: {
		async refresh() {
			try {
				this.error = false;

				await this.$store.dispatch("subscription/get");
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Subscription Missing",
					message: "Failed to retrieve subscription details. Please contact support if the issue persists.",
					variant: "danger",
				});

				this.error = e;
			}
		},
		async cancelSubscription() {
			const message = "Are you sure you want to cancel? You will be signed out.";

			if (!confirm(message)) {
				return false;
			}

			this.cancelled = true;

			try {
				this.cancelling = true;

				const response = await this.$store.dispatch("subscription/cancel");

				if (response.cancelled) {
					this.cancelled = true;
				}
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Subscription Cancel Failed",
					message: "Failed to cancel subscription. Please contact support if the issue persists.",
				});
			} finally {
				this.cancelling = false;
			}
		},
	},
};
</script>
