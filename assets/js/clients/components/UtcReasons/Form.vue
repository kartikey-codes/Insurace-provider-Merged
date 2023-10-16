<template>
	<loading-indicator v-if="loading" class="my-5" />
	<validation-observer v-else v-bind="$attrs" ref="observer" v-slot="{ invalid }">
		<b-form @submit.prevent="save" class="px-0">
			<slot name="header"></slot>

			<b-card-body>
				<validation-provider
					vid="name"
					name="Name"
					:rules="{ required: true, min: 3 }"
					v-slot="validationContext"
				>
					<b-form-group label="Name" label-for="name" label-cols="12" label-cols-lg="4">
						<b-form-input
							:autofocus="autofocus"
							name="name"
							type="text"
							v-model="entity.name"
							required
							placeholder="Required"
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
			</b-card-body>

			<b-card-footer>
				<b-row>
					<b-col cols="12" md="6" xl="4" class="mb-2 mb-md-0">
						<b-button block variant="light" @click="cancel">Cancel</b-button>
					</b-col>
					<b-col cols="12" md="6" offset-xl="4" xl="4" class="mb-2 mb-md-0">
						<b-button block variant="primary" type="submit" :disabled="saving || invalid">
							<font-awesome-icon icon="circle-notch" v-if="saving" spin fixed-width />
							<span>Save</span>
						</b-button>
					</b-col>
				</b-row>
			</b-card-footer>
		</b-form>
	</validation-observer>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import { getValidationState } from "@/validation";

export default {
	name: "UtcReasonForm",
	props: {
		autofocus: {
			type: Boolean,
			default: false,
		},
		id: {
			default: null,
		},
	},
	data() {
		return {
			loading: true,
			saving: false,
			entity: {
				id: this.id || null,
				name: "",
			},
		};
	},
	mounted() {
		if (this.id) {
			this.refresh();
		} else {
			this.loading = false;
		}
	},
	methods: {
		getValidationState,
		cancel() {
			this.$emit("cancel");
		},
		async refresh() {
			try {
				this.loading = true;

				const response = await this.$store.dispatch("utcReasons/get", {
					id: this.id,
				});

				this.entity = response;
				this.$emit("loaded", response);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Error getting UTC reason details",
				});
			} finally {
				this.loading = false;
			}
		},
		async save(e) {
			try {
				this.saving = true;

				const response = await this.$store.dispatch("utcReasons/save", this.entity);

				this.$emit("saved", response);
				this.$emit("update:id", response.id);
			} catch (e) {
				if (e.response.data.errors) {
					this.$refs.observer.setErrors(formatErrors(e.response.data.errors));
				}

				this.$store.dispatch("apiError", {
					error: e,
					title: "Save Failed",
					message: "Error saving UTC reason details. Please check for errors.",
					variant: "warning",
				});
			} finally {
				this.saving = false;

				this.$store.dispatch("utcReasons/getAll");
			}
		},
	},
};
</script>
