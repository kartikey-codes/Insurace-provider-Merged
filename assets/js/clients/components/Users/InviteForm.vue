<template>
	<validation-observer v-bind="$attrs" ref="observer" v-slot="{ invalid }">
		<b-form @submit.prevent="save">
			<slot name="before" v-bind="{ saving, invalid }"></slot>
			<b-row class="mb-4">
				<b-col cols="12">
					<validation-provider
						vid="email"
						name="Email"
						:rules="{ required: true, min: 3, max: 50 }"
						v-slot="validationContext"
					>
						<b-form-group label="Email Address" label-for="email" label-cols-lg="4">
							<b-form-input
								:autofocus="autofocus"
								name="email"
								size="lg"
								type="email"
								v-model="entity.email"
								:state="getValidationState(validationContext)"
								:disabled="saving"
							/>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>
				</b-col>
			</b-row>
			<b-row>
				<b-col cols="4" md="6" xl="4">
					<b-button block variant="light" type="button" @click.stop="cancel">Cancel</b-button>
				</b-col>
				<b-col cols="8" md="6" offset-xl="4" xl="4">
					<b-button block variant="primary" type="submit" :disabled="saving || invalid">
						<font-awesome-icon icon="circle-notch" v-if="saving" spin fixed-width />
						<span>Send Invite</span>
					</b-button>
				</b-col>
			</b-row>
		</b-form>
	</validation-observer>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import { formatErrors, getValidationState } from "@/validation";
import { sendInvite } from "@/clients/services/userInvites";

export default {
	name: "UserInviteForm",
	props: {
		autofocus: {
			type: Boolean,
			default: false,
		},
		value: {
			type: Object,
			default: () => {
				return {
					email: "",
				};
			},
		},
	},
	data() {
		return {
			saving: false,
			entity: this.value,
		};
	},
	methods: {
		getValidationState,
		cancel() {
			this.$emit("cancel");
		},
		reset() {
			this.entity = {
				email: "",
			};

			this.$refs.observer.reset();
		},
		async save(e) {
			try {
				this.saving = true;

				const request = {
					...this.entity,
				};

				this.$emit("save", request);
				const response = await sendInvite(request);

				this.$store.dispatch("notify", {
					variant: "primary",
					title: "User Invited",
					message: `Invite has been sent by email.`,
				});

				this.$emit("saved", response);
				this.reset();
			} catch (e) {
				if (e.response.data.errors) {
					this.$refs.observer.setErrors(formatErrors(e.response.data.errors));
				}

				this.$store.dispatch("apiError", {
					error: e,
					title: "Invite Failed",
					message:
						"Error sending user invite. Please check for errors and contact support if issue persists.",
					variant: "warning",
				});

				if (e.response.data.message) {
					this.$emit("error", e.response.data.message);
				}
			} finally {
				this.saving = false;
			}
		},
	},
	watch: {
		entity(val) {
			this.$emit("input", val);
		},
	},
};
</script>
