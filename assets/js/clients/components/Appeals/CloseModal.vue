<template>
	<b-form @submit.prevent="save" ref="formElement">
		<b-modal id="modal" size="lg" title="Save &amp; Close Appeal" ref="modal" @ok="save">
			<b-container>
				<b-row>
					<b-col>
						<b-alert show variant="light">
							<font-awesome-icon icon="exclamation-triangle" fixed-width />
							<span>Please choose a user to assign the next stage of this appeal to.</span>
						</b-alert>
					</b-col>
				</b-row>
				<b-row>
					<b-col>
						<b-form-group id="assignTo" label="Assign To" label-for="assignTo" label-cols-lg="4">
							<loading-indicator size="2x" class="my-4" v-if="!activeUsers || !activeUsers.length" />
							<b-form-radio-group
								v-else
								stacked
								v-model="assigned"
								:options="activeUsers"
								value-field="id"
								text-field="full_name"
								required="required"
								:disabled="saving"
							>
								<template #first>
									<b-form-radio :value="null" class="mt-2 mb-4">Open Queue</b-form-radio>
								</template>
							</b-form-radio-group>
						</b-form-group>
					</b-col>
				</b-row>
			</b-container>
			<template #modal-footer>
				<b-container fluid class="px-0">
					<b-row no-gutters>
						<b-col cols="6" class="text-left">
							<b-button variant="light" text-variant="light" @click="hide" class="mr-2">Cancel</b-button>
						</b-col>
						<b-col cols="6" class="text-right">
							<b-button
								variant="primary"
								text-variant="light"
								type="submit"
								:disabled="saving"
								@click.prevent="save"
							>
								<font-awesome-icon v-if="saving" icon="circle-notch" spin fixed-width />
								<span>Save &amp; Close</span>
							</b-button>
						</b-col>
					</b-row>
				</b-container>
			</template>
		</b-modal>
	</b-form>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "CloseAppealStageModal",
	props: {
		appeal: {
			type: Object,
			default: () => {},
		},
		stage: {
			default: null,
		},
	},
	data() {
		return {
			assigned: null,
			saving: false,
		};
	},
	mounted() {
		if (this.appeal.assigned_to) {
			this.assigned = this.appeal.assigned_to;
		}
	},
	computed: mapGetters({
		activeUsers: "users/active",
	}),
	methods: {
		show() {
			this.$refs.modal.show();
		},
		hide() {
			this.$refs.modal.hide();
		},
		cancel() {
			this.$emit("cancel");
		},
		save() {
			this.$emit("submit", {
				user_id: this.assigned,
				save_only: true,
			});

			this.hide();
		},
	},
	watch: {
		assigned(val) {
			this.$emit("update:assigned", val);
		},
	},
};
</script>
