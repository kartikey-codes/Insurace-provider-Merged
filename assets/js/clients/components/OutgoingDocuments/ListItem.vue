<template>
	<b-card no-body v-bind="$attrs">
		<b-card-header>
			<b-row class="d-flex align-items-center">
				<b-col cols="9" sm="8" lg="9" class="mb-0">
					<font-awesome-icon icon="user" fixed-width class="text-muted mr-2" />
					<h2 class="h6 mb-0 font-weight-bold d-inline">
						{{ patientName }}
					</h2>
				</b-col>
				<b-col cols="3" sm="4" lg="3">
					<b-progress max="100" class="mb-0" :title="value.status_label">
						<b-progress-bar
							:animated="value.progress_indeterminate"
							:value="value.progress_percent"
							:variant="value.progress_variant"
						>
							<span>{{ value.status_label }}</span>
						</b-progress-bar>
					</b-progress>
				</b-col>
			</b-row>
		</b-card-header>
		<b-card-body>
			<b-row class="d-flex align-items-top">
				<b-col cols="12" sm="6" lg="8" class="mb-4 mb-sm-0 text-left">
					<b-row>
						<b-col cols="12" lg="6" class="mb-2 mb-lg-0">
							<div class="d-flex justify-content-start align-items-top">
								<div class="mr-3">
									<b-button @click="download" title="Download Packet">
										<font-awesome-icon icon="file-download" fixed-width />
									</b-button>
								</div>
								<div>
									<h3 class="h6 mb-0">
										{{ appealLevel }}
									</h3>
									<p class="small text-muted mb-0">
										Updated {{ $filters.fromNow(value.modified) }}
										<span v-if="value.modified_by_user"
											>by {{ value.modified_by_user.full_name }}</span
										>
									</p>
								</div>
							</div>
						</b-col>
						<b-col cols="12" lg="6">
							<p v-if="value.delivery_method" class="text-muted mb-0" title="Delivery Method">
								<font-awesome-icon
									v-if="value.delivery_method_icon"
									:icon="value.delivery_method_icon"
									fixed-width
								/>
								<span v-if="value.delivery_method && value.delivery_method_label" class="mb-0">
									{{ value.delivery_method_label }}
								</span>
							</p>
							<div v-if="!hideAgency">
								<p v-if="value.agency" class="text-muted mb-0" title="Agency">
									<font-awesome-icon icon="building" fixed-width />
									<span v-if="value.agency && value.agency.name" class="mb-0">
										{{ value.agency.name }}
									</span>
								</p>
								<p
									v-else
									class="text-warning font-weight-bold font-italic mb-0"
									title="No Agency Provided"
								>
									<font-awesome-icon icon="building" fixed-width />
									<span class="mb-0">No Agency</span>
								</p>
							</div>
						</b-col>
					</b-row>
				</b-col>
				<b-col cols="12" sm="6" lg="4" class="text-left text-sm-right">
					<b-button
						variant="primary"
						@click="markDelivered"
						:disabled="isDelivered || isCancelled"
						class="mb-0"
					>
						<span v-if="isDelivered">Delivered</span>
						<span v-else>Mark Delivered</span>
					</b-button>
					<b-dropdown right class="mb-0">
						<template #button-content>
							<font-awesome-icon icon="cog" />
						</template>
						<b-dropdown-item
							v-if="!hideViewAppeal"
							:to="{
								name: 'appeals.view',
								params: { id: value.case_id, appeal_id: value.appeal_id },
							}"
						>
							View Appeal
						</b-dropdown-item>
						<b-dropdown-item
							v-if="value.agency_id && !hideAgency"
							:to="{
								name: 'agencies.view',
								params: { id: value.agency_id },
							}"
						>
							View Agency
						</b-dropdown-item>

						<b-dropdown-divider v-if="!(hideAgency && hideViewAppeal)" />

						<b-dropdown-item @click="retry" :disabled="!value.can_retry"> Retry </b-dropdown-item>
						<b-dropdown-item @click="cancel" :disabled="!value.can_cancel"> Cancel </b-dropdown-item>
					</b-dropdown>
				</b-col>
			</b-row>
		</b-card-body>
	</b-card>
</template>

<script>
export default {
	name: "OutgoingDocumentListItem",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					id: null,
					case_id: null,
					appeal_id: null,
					agency_id: null,
					delivery_method: null,
					delivery_method_label: "",
					delivery_method_icon: "",
					status_message: "",
					can_cancel: false,
					can_retry: false,
					progress_indeterminate: false,
					progress_percent: 0,
					progress_variant: "",
					agency: {
						id: null,
						name: null,
					},
					case: {
						id: null,
						patient_id: null,
						patient: {
							id: null,
							full_name: null,
						},
					},
					appeal: {
						id: null,
						appeal_level_id: null,
						appeal_level: {
							id: null,
							name: null,
						},
					},
					created_by_user: {
						id: null,
						full_name: null,
					},
				};
			},
		},
		hideViewAppeal: {
			type: Boolean,
			default: false,
		},
		hideAgency: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		patientName() {
			return this.value.case?.patient?.full_name ?? "(Missing Name)";
		},
		appealLevel() {
			return this.value.appeal?.appeal_level?.name ?? "(Missing Level)";
		},
		isDelivered() {
			return this.value.status_message == "DELIVERED";
		},
		isCancelled() {
			return this.value.status_message == "CANCELLED";
		},
	},
	methods: {
		async markDelivered() {
			const response = await this.$store.dispatch("outgoingDocuments/delivered", {
				id: this.value.id,
			});

			this.$store.dispatch("notify", {
				variant: "primary",
				title: "Documents Delivered",
				message: `Document for ${this.patientName} has been marked as delivered`,
			});

			this.$emit("updated", response);
		},
		async cancel() {
			const response = await this.$store.dispatch("outgoingDocuments/cancel", {
				id: this.value.id,
			});

			this.$store.dispatch("notify", {
				variant: "warning",
				title: "Documents Cancelled",
				message: `Document for ${this.patientName} has been marked as cancelled`,
			});

			this.$emit("updated", response);
		},
		async retry() {
			const response = await this.$store.dispatch("outgoingDocuments/retry", {
				id: this.value.id,
			});

			this.$store.dispatch("notify", {
				variant: "primary",
				title: "Documents Requeued",
				message: `Document for ${this.patientName} has been marked as new and requeued for sending`,
			});

			this.$emit("updated", response);
		},
		async download() {
			return await this.$store.dispatch("outgoingDocuments/download", {
				id: this.value.id,
			});
		},
	},
};
</script>
