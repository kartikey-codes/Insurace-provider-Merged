<template>
	<loading-indicator v-if="loading" class="my-5" title="Fetching service..." />
	<validation-observer v-else v-bind="$attrs" ref="observer" v-slot="{ invalid }">
		<b-form @submit.prevent="save">
			<b-card no-body>
				<slot name="header"></slot>

				<b-card-body>
					<validation-provider
						vid="name"
						name="Name"
						:rules="{ required: true, min: 2, max: 60 }"
						v-slot="validationContext"
					>
						<b-form-group label="Name" label-for="name" label-cols-lg="4">
							<b-form-input
								autofocus
								name="name"
								type="text"
								size="lg"
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

					<validation-provider
						vid="description"
						name="Description"
						:rules="{ required: false, max: 255 }"
						v-slot="validationContext"
					>
						<b-form-group label="Description" label-for="description" label-cols-lg="4">
							<b-form-textarea
								name="description"
								type="text"
								rows="5"
								v-model="entity.description"
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

					<validation-provider
						vid="active"
						name="Active"
						:rules="{ required: false }"
						v-slot="validationContext"
					>
						<b-form-group
							label="Active"
							label-for="active"
							label-cols-lg="4"
							description="Inactive services will not show up in dropdown lists."
						>
							<b-form-checkbox name="active" v-model="entity.active" :disabled="saving"
								>Active</b-form-checkbox
							>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<validation-provider
						vid="client_owned"
						name="Owned"
						:rules="{ required: false }"
						v-slot="validationContext"
					>
						<b-form-group
							label="Owned"
							label-for="client_owned"
							label-cols-lg="4"
							description="Service is owned/provided by client."
						>
							<b-form-checkbox name="client_owned" v-model="entity.client_owned" :disabled="saving"
								>Owned</b-form-checkbox
							>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>
				</b-card-body>

				<b-card-body>
					<h6 class="text-muted">Optional</h6>
					<b-card no-body>
						<b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseFacilities
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
								>Facilities</b-button
							>
						</b-card-header>
						<b-collapse id="collapseFacilities" role="tabpanel">
							<b-card-body>
								<b-form-group label="Facilities" label-for="facility_ids" label-cols-lg="4">
									<b-form-checkbox-group
										stacked
										name="facility_ids"
										v-model="facility_ids"
										:options="facilities"
										:disabled="saving || loadingFacilities"
										value-field="id"
										text-field="name"
									/>
								</b-form-group>
							</b-card-body>
						</b-collapse>
					</b-card>
				</b-card-body>

				<b-card-footer>
					<b-row>
						<b-col cols="12" md="6" lg="4" class="mb-4 mb-md-0">
							<b-button block variant="light" type="button" @click.prevent="cancel">Cancel</b-button>
						</b-col>
						<b-col cols="12" md="6" offset-lg="4" lg="4" class="mb-2 mb-md-0">
							<b-button block variant="primary" type="submit" :disabled="saving">
								<font-awesome-icon icon="circle-notch" v-if="saving" spin fixed-width />
								<span>Save</span>
							</b-button>
						</b-col>
					</b-row>
				</b-card-footer>
			</b-card>
		</b-form>
	</validation-observer>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import { save, get } from "@/clients/services/services";
import { formatErrors, getValidationState } from "@/validation";

export default {
	name: "ServiceForm",
	props: {
		id: {
			default: null,
		},
	},
	computed: mapGetters({
		facilities: "facilities/active",
		loadingFacilities: "facilities/loadingAll",
	}),
	data() {
		return {
			loading: true,
			saving: false,
			entity: {
				id: this.id,
				name: "",
				description: "",
				active: true,
				client_owned: true,
				facilities: [],
			},
			facility_ids: [],
		};
	},
	mounted() {
		this.getFacilities();

		if (this.id) {
			this.refresh();
		} else {
			this.loading = false;
		}
	},
	methods: {
		getValidationState,
		async getFacilities() {
			await this.$store.dispatch("facilities/getAll");
		},
		cancel(e) {
			if (e) {
				e.preventDefault();
			}

			this.$emit("cancel");
		},
		async refresh() {
			try {
				this.loading = true;
				const response = await get(this.id);

				this.entity = response;
				this.facility_ids = response.facilities.map((facility) => facility.id);

				this.$emit("loaded", this.entity);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Error getting service details",
				});
			} finally {
				this.loading = false;
			}
		},
		async save() {
			try {
				this.saving = true;

				const response = await save({
					...this.entity,
					facilities: {
						_ids: this.facility_ids,
					},
				});

				this.$emit("saved", response);
				this.$emit("update:id", response.id);
			} catch (e) {
				if (e.response.data.errors) {
					this.$refs.observer.setErrors(formatErrors(e.response.data.errors));
				}

				this.$store.dispatch("apiError", {
					error: e,
					title: "Save Failed",
					message: "Failed to save service details. Please check for errors.",
					variant: "warning",
				});
			} finally {
				this.saving = false;
			}
		},
	},
};
</script>
