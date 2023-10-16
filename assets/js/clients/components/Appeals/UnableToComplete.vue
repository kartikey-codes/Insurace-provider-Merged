<template>
	<b-form @submit.prevent="validate" ref="formElement">
		<b-card-body>
			<b-row>
				<b-col cols="12" class="mb-2">
					<b-form-group
						id="unable_to_complete"
						label="Unable To Complete?"
						label-for="unable_to_complete"
						label-cols-xl="4"
						description="Is this appeal unable to be completed?"
					>
						<b-form-radio-group
							:id="'utc-' + appeal.id"
							v-model="utc"
							:options="[
								{ name: 'Yes', value: true },
								{ name: 'No', value: false },
							]"
							class="mt-2"
							name="utc"
							value-field="value"
							text-field="name"
							required="required"
						/>
					</b-form-group>
				</b-col>
				<b-col v-show="showReasons" cols="12" class="mb-2">
					<transition name="fade" mode="out-in">
						<b-form-group
							:id="'utcReasons-' + appeal.id"
							label="Reasons"
							label-for="utc_reasons"
							label-cols-xl="4"
						>
							<loading-indicator v-if="loadingUtcReasons" class="my-5" />
							<empty-result v-else-if="utcReasons.length <= 0">
								No reasons created
								<template #content>
									Reasons can be added to track why an appeal is unable to be completed.
								</template>
							</empty-result>
							<b-form-checkbox-group
								v-else
								stacked
								:id="'utc_reasons-' + appeal.id"
								v-model="checkedReasonIds"
								:options="utcReasons"
								class="mt-2"
								name="utc_reasons"
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
					<b-button
						id="show-btn"
						type="button"
						block
						variant="light"
						@click="$bvModal.show('add-reason-modal')"
					>
						<font-awesome-icon icon="plus" fixed-width />
						Add Reason
					</b-button>
				</b-col>

				<b-col cols="12" offset-md="4" md="4" align="center" class="mb-2 mb-md-0">
					<b-button @click="save" type="button" block variant="primary" :disabled="saving">
						<font-awesome-icon icon="circle-notch" v-if="saving" spin fixed-width />
						<span>Save</span>
					</b-button>
				</b-col>
			</b-row>
		</b-card-footer>
		<b-modal id="add-reason-modal" hide-footer>
			<template #modal-title> New UTC Reason </template>
			<template #default="{ hide }">
				<utc-reason-form autofocus @saved="hide" @cancel="hide" />
			</template>
		</b-modal>
	</b-form>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import UtcReasonForm from "@/clients/components/UtcReasons/Form.vue";

export default {
	name: "AppealUnableToComplete",
	components: {
		UtcReasonForm,
	},
	props: {
		appeal: {
			required: true,
			type: Object,
			default: () => {
				return {
					id: null,
					unable_to_complete: null,
					utc_reasons: [],
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
			utc: this.appeal.unable_to_complete,
			checkedReasonIds: [],
			saving: false,
		};
	},
	mounted() {
		if (this.appeal.utc_reasons) {
			this.checkedReasonIds = this.appeal.utc_reasons.map((reason) => reason.id);
		}
	},
	computed: {
		showReasons() {
			if (this.utc === null || this.utc === false) {
				return false;
			}

			return true;
		},
		...mapGetters({
			utcReasons: "utcReasons/all",
			loadingUtcReasons: "utcReasons/loadingAll",
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
				const response = await this.$store.dispatch("appeals/setUnableToComplete", {
					id: this.appeal.id,
					unable_to_complete: this.utc,
					reason_ids: this.checkedReasonIds,
				});
				this.$emit("saved", response);
				this.$emit("update:appeal", response);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Update Failed",
					message: "Failed to update appeal",
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
				if (!this.appeal.utc_reasons) {
					return;
				}

				this.checkedReasonIds = this.appeal.utc_reasons.map((reason) => reason.id);
			},
		},
	},
};
</script>
