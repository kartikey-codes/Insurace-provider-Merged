<template>
	<b-modal
		centered
		:id="id"
		:size="size"
		:title="title"
		header-bg-variant="primary"
		header-text-variant="white"
		ref="closeCaseModal"
		@ok.prevent="onSubmit"
		@shown="shown"
		:ok-disabled="saving"
	>
		<b-form id="closeCaseModal" @submit.prevent="onSubmit">
			<b-alert show variant="light">
				<font-awesome-icon icon="info-circle" fixed-width />
				<span class="ml-2">
					Cases should be closed when all appeal opportunities have been exhausted and a verdict has been
					reached.
				</span>
			</b-alert>

			<b-form-group
				id="caseOutcome"
				label="Outcome"
				description="Used to calculate favorable and unfavorable percentages"
				label-for="case_outcome_id"
				label-cols-lg="4"
				class="mb-4"
			>
				<loading-indicator v-if="loadingCaseOutcomes" />
				<b-form-radio-group
					name="case_outcome_id"
					v-else
					stacked
					v-model="case_outcome_id"
					:options="caseOutcomes"
					value-field="id"
					text-field="name"
				/>
			</b-form-group>

			<b-form-group
				id="settledAmount"
				description="Used to calculate dollars reimbursed"
				label="Settled Amount"
				label-for="settledAmount"
				label-cols-lg="4"
			>
				<b-input-group>
					<b-input-group-prepend is-text>
						<font-awesome-icon icon="dollar-sign" fixed-width />
					</b-input-group-prepend>
					<b-form-input name="settled_amount" v-model="settled_amount" type="text" autocomplete="off" />
				</b-input-group>
			</b-form-group>

			<template #modal-ok>
				<font-awesome-icon v-if="saving" icon="circle-notch" spin fixed-width />
				<span v-else>Close Case</span>
			</template>
		</b-form>
	</b-modal>
</template>

<script>
import { mapGetters } from "vuex";

export default {
	name: "CloseCaseModal",
	props: {
		id: {
			type: String,
			default: "closeCaseModal",
		},
		caseEntity: {
			type: Object,
			default: () => {
				return {
					id: null,
					closed: null,
					case_outcome_id: null,
					disputed_amount: null,
					reimbursement_amount: null,
					settled_amount: null,
				};
			},
		},
		title: {
			type: String,
			default: "Close Case",
		},
		size: {
			type: String,
			default: "lg",
		},
	},
	data() {
		return {
			saving: false,
			case_outcome_id: this.caseEntity.case_outcome_id,
			settled_amount: null,
		};
	},
	computed: mapGetters({
		caseOutcomes: "caseOutcomes/all",
		loadingCaseOutcomes: "caseOutcomes/loadingAll",
	}),
	mounted() {
		if (this.caseEntity.settled_amount !== null) {
			this.settled_amount = this.caseEntity.settled_amount;
		} else if (this.caseEntity.reimbursement_amount !== null) {
			this.settled_amount = this.caseEntity.reimbursement_amount;
		}
	},
	methods: {
		shown(e) {
			if (this.caseOutcomes.length > 0 && this.case_outcome_id == null) {
				this.case_outcome_id = this.caseOutcomes[0].id;
			}
		},
		cancel(e) {
			this.$refs.closeCaseModal.hide();
		},
		async onSubmit(e) {
			e.preventDefault();

			try {
				this.saving = true;

				const response = await this.$store.dispatch("cases/close", {
					id: this.caseEntity.id,
					case_outcome_id: this.case_outcome_id,
					settled_amount: this.settled_amount,
				});

				this.$store.dispatch("updateState");

				this.$emit("closed", response);
				this.$refs.closeCaseModal.hide();
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Close Failed",
					message: "Failed to close case.",
				});
			} finally {
				this.saving = false;
			}
		},
		reset() {
			this.case_outcome_id = null;
		},
	},
	watch: {
		caseEntity: {
			immediate: false,
			handler(val) {
				this.case_outcome_id = val.case_outcome_id;
				this.settled_amount = val.settled_amount;
			},
		},
	},
};
</script>
