<template>
	<b-row>
		<b-col cols="12" class="mb-4">
			<h3 class="font-weight-bold text-accent">{{ serviceName }}</h3>
		</b-col>

		<b-col cols="12" md="12" lg="12" xl="8" class="mb-4">
			<div v-if="appealStatus == 'Open'">
				<p>Submit this appeal to {{ serviceName }} to have a professional deliver a response for this level.</p>
				<p class="text-muted">
					You will be notified by email when the appeal is completed or notes have been added.
				</p>
			</div>
			<div v-else-if="appealStatus == 'Submitted'">
				<b-alert show variant="info">
					We received your appeal and we're matching it up with a {{ serviceName }} professional.
				</b-alert>
				<p class="text-muted">
					You will be notified by email once they've completed the response or require any additional
					documentation.
				</p>
			</div>
			<div v-else-if="appealStatus == 'Assigned'">
				<b-alert show variant="primary">
					Your appeal has been assigned to a {{ serviceName }} professional.
				</b-alert>
				<p class="text-muted">
					You will receive an email when the response is completed or notes have been added.
				</p>
			</div>
			<div v-else-if="appealStatus == 'Completed'">
				<b-alert show variant="success">
					Your response has been completed by a {{ serviceName }} professional.
				</b-alert>
				<p class="text-muted">Check the "Files" tab to download your response.</p>
			</div>
			<div v-else-if="appealStatus == 'Returned'">
				<b-alert show variant="danger">
					Our {{ serviceName }} professional was unable to complete your response.
				</b-alert>
				<p>Please check the appeal notes and upload any missing documentation.</p>
			</div>
			<div v-else-if="appealStatus == 'Cancelled'">
				<b-alert show variant="warning"> This appeal has been cancelled. </b-alert>
				<p class="mb-0">This appeal has been voided and must be reopened to progress any further.</p>
			</div>
			<div v-else-if="appealStatus == 'Closed'">
				<p class="font-weight-bold text-primary">This appeal has been closed.</p>
				<p class="mb-0">No further action is necessary.</p>
			</div>
			<div v-else>
				<p class="font-weight-bold text-warning">Your appeal is in an unknown status right now.</p>
				<p class="mb-0">Please contact us to have this corrected. We're sorry for the issue.</p>
			</div>
		</b-col>
		<b-col cols="12" md="12" lg="12" xl="3" offset-xl="1" class="mb-4">
			<div v-if="appealStatus == 'Open' || appealStatus == 'Submitted' || appealStatus == 'Assigned'">
				<b-button
					block
					variant="primary"
					size="lg"
					@click="submitAppeal"
					:disabled="busy || submitting || !canSubmit"
				>
					<font-awesome-icon icon="sync" spin v-if="submitting" fixed-width />
					<span v-if="canSubmit">Submit</span>
					<span v-else>Submitted</span>
				</b-button>

				<b-button
					block
					variant="light"
					@click="reopenAppeal"
					:disabled="busy || reopening"
					v-if="canReopen"
					class="mt-4"
				>
					<font-awesome-icon icon="sync" spin v-if="busy" fixed-width />
					<span>Cancel</span>
				</b-button>
			</div>
			<div v-else-if="appealStatus == 'Completed'">
				<b-button
					block
					variant="primary"
					size="lg"
					@click="closeAppeal"
					:disabled="busy || closing || !canClose"
				>
					<font-awesome-icon icon="sync" spin v-if="closing" fixed-width />
					<span>Close</span>
				</b-button>
			</div>
			<div v-else-if="appealStatus == 'Returned'">
				<b-button
					block
					variant="primary"
					size="lg"
					@click="submitAppeal"
					:disabled="busy || submitting || !canSubmit"
				>
					<font-awesome-icon icon="sync" spin v-if="submitting" fixed-width />
					<span>Re-Submit</span>
				</b-button>
			</div>
			<div v-else-if="appealStatus == 'Closed' || appealStatus == 'Cancelled'">
				<b-button
					block
					variant="primary"
					size="lg"
					@click="reopenAppeal"
					:disabled="busy || reopening || caseClosed"
					v-if="canReopen"
				>
					<font-awesome-icon icon="sync" spin v-if="busy" fixed-width />
					<span v-if="!caseClosed">Reopen</span>
					<span v-else>Closed</span>
				</b-button>
			</div>
		</b-col>
		<b-col v-if="progress > 0" cols="12" class="mb-4">
			<p class="mb-1 font-weight-bold small text-muted">Audit Progress</p>
			<b-progress :value="progress" :max="100" class="mb-1" />
		</b-col>
	</b-row>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "AppealStages",
	props: {
		caseEntity: {
			required: true,
			type: Object,
			default: () => {
				return {
					id: null,
					closed: null,
				};
			},
		},
		appeal: {
			required: true,
			type: Object,
			default: () => {
				return {
					id: null,
					closed: null,
					appeal_status: null,
					can_cancel: null,
					can_close: null,
					can_delete: null,
					can_reopen: null,
					can_submit: null,
				};
			},
		},
	},
	data() {
		return {
			busy: false,
			submitting: false,
			reopening: false,
			closing: false,
		};
	},
	computed: {
		/**
		 * Stages
		 */
		appealStatus() {
			return this.appeal.appeal_status;
		},
		progress() {
			switch (this.appeal.appeal_status) {
				case "Open":
					return 10;
				case "Submitted":
					return 25;
				case "Assigned":
					return 50;
				case "Completed":
					return 80;
				case "Returned":
					return 80;
				case "Closed":
					return 100;
				case "Cancelled":
					return 0;
				default:
					return 0;
			}
		},
		canSubmit() {
			return this.appeal.can_submit;
		},
		canReopen() {
			return this.appeal.can_reopen;
		},
		canClose() {
			return this.appeal.can_close;
		},
		caseClosed() {
			return this.caseEntity.closed ? true : false;
		},
		...mapGetters({
			serviceName: "vendorServiceName",
		}),
	},
	methods: {
		async submitAppeal() {
			const message = `Submit to ${this.serviceName}?`;
			if (!confirm(message)) {
				return false;
			}

			try {
				this.busy = true;
				this.submitting = true;

				const response = await this.$store.dispatch("appeals/submit", {
					id: this.appeal.id,
				});

				this.$emit("update:appeal", response);
				this.$emit("submitted", response);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Submit Failed",
					message: "Failed to submit appeal",
				});
			} finally {
				this.busy = false;
				this.submitting = false;
			}
		},
		async reopenAppeal() {
			const message = `Are you sure? This will reset the status to 'Open' and cancel any progression.`;
			if (!confirm(message)) {
				return false;
			}

			try {
				this.busy = true;
				this.reopening = true;

				const response = await this.$store.dispatch("appeals/reopen", {
					id: this.appeal.id,
				});

				this.$emit("update:appeal", response);
				this.$emit("reopened", response);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Reopen Failed",
					message: "Failed to reopen appeal",
				});
			} finally {
				this.busy = false;
				this.reopening = false;
			}
		},
		async closeAppeal() {
			const message = `Close out this appeal as completed?`;
			if (!confirm(message)) {
				return false;
			}

			try {
				this.busy = true;
				this.closing = true;

				const response = await this.$store.dispatch("appeals/close", {
					id: this.appeal.id,
				});

				this.$emit("update:appeal", response);
				this.$emit("closed", response);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Close Failed",
					message: "Failed to close appeal",
				});
			} finally {
				this.busy = false;
				this.closing = false;
			}
		},
	},
};
</script>
