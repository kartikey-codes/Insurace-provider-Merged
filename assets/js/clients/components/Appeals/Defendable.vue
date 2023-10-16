<template>
	<b-form @submit.prevent="validate" ref="formElement">
		<b-card-body>
			<b-row>
				<b-col cols="12" class="mb-2">
					<b-form-group
						id="Defendable"
						label="Defendable at this level?"
						label-for="Defendable"
						label-cols-xl="4"
						description="In your professional opinion, is this case defendable at this level?"
					>
						<b-form-radio-group
							:id="'defendable-' + appeal.id"
							v-model="defendable"
							:options="defendableOptions"
							class="mt-2"
							name="defendable"
							value-field="value"
							text-field="name"
							required="required"
						/>
					</b-form-group>
				</b-col>
				<b-col v-show="showNotDefendableReasons" cols="12" class="mb-2">
					<transition name="fade" mode="out-in">
						<b-form-group
							v-if="showNotDefendableReasons"
							:id="'notDefendableReasons-' + appeal.id"
							label="Reasons Not Defendable"
							label-for="not_defendable_reasons"
							label-cols-xl="4"
						>
							<loading-indicator
								v-if="!notDefendableReasons || notDefendableReasons.length <= 0"
								class="my-5"
							/>
							<b-form-checkbox-group
								v-else
								stacked
								:id="'not_defendable_reasons-' + appeal.id"
								v-model="checkedReasonIds"
								:options="notDefendableReasons"
								class="mt-2"
								name="not_defendable_reasons"
								value-field="id"
								text-field="name"
							/>
						</b-form-group>
					</transition>
				</b-col>
			</b-row>
		</b-card-body>
		<b-card-footer>
			<b-row>
				<b-col cols="12" md="4" align="center" class="mb-2 mb-md-0">
					<b-button v-if="!disableCancel" @click="cancel" type="button" block variant="light"
						>Cancel</b-button
					>
				</b-col>

				<b-col cols="12" offset-md="4" md="4" align="center" class="mb-2 mb-md-0">
					<b-button @click="save" type="button" block variant="primary" :disabled="saving">
						<font-awesome-icon icon="circle-notch" v-if="saving" spin fixed-width />
						<span>Save</span>
					</b-button>
				</b-col>
			</b-row>
		</b-card-footer>
	</b-form>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "AppealDefendable",
	props: {
		appeal: {
			required: true,
			type: Object,
			default: () => {
				return {
					id: null,
					defendable: null,
				};
			},
		},
		disableCancel: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			defendable: this.appeal.defendable,
			checkedReasonIds: [],
			saving: false,
		};
	},
	mounted() {
		if (this.appeal.not_defendable_reasons) {
			this.checkedReasonIds = this.appeal.not_defendable_reasons.map((reason) => reason.id);
		}
	},
	computed: {
		showNotDefendableReasons() {
			if (this.defendable === null || this.defendable === true) {
				return false;
			}

			return true;
		},
		...mapGetters({
			defendableOptions: "appeals/defendableOptions",
			notDefendableReasons: "notDefendableReasons/all",
		}),
	},
	methods: {
		cancel(e) {
			this.$emit("cancel");
		},
		/**
		 * Validation
		 */
		validate(e) {
			if (this.$refs.formElement.checkValidity()) {
				return true;
			} else {
				this.$refs.submitButton.click();
			}

			return false;
		},
		/**
		 * Save
		 */
		async save() {
			try {
				this.saving = true;

				const response = await this.$store.dispatch("appeals/setDefendable", {
					id: this.appeal.id,
					defendable: this.defendable,
					reason_ids: this.checkedReasonIds,
				});

				this.$emit("saved", response);
				this.$emit("update:appeal", response);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Save Failed",
					message: "Failed to save appeal.",
				});
			} finally {
				this.saving = false;
			}
		},
	},
	watch: {
		appeal: {
			immediate: true,
			handler(value) {
				this.checkedReasonIds = this.appeal.not_defendable_reasons.map((reason) => reason.id);
			},
		},
	},
};
</script>
