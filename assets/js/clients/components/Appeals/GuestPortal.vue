<template>
	<div>
		<div v-if="!adding">
			<b-card-body>
				<transition appear enter-active-class="fadeIn" leave-active-class="fadeOut">
					<div class="bg-white text-muted rounded text-center p-5 shadow-sm mb-0">
						<div class="d-flex justify-content-center align-items-center mb-4">
							<font-awesome-icon icon="users" size="4x" class="text-muted mr-4" />
							<img src="/img/logos/phoenix_medical_management.png" style="height: 80px; width: auto" />
						</div>
						<h4 class="text-uppercase font-weight-bold text-muted">Guest Access</h4>
						<p>
							Open a guest portal to allow third-party users without an account to upload files or add
							notes to this appeal.
						</p>
					</div>
				</transition>
			</b-card-body>
			<b-card-body v-if="hasPortals">
				<h6 class="text-muted">Portals</h6>
				<b-list-group>
					<b-list-group-item v-for="portal in appeal.guest_portals" :key="portal.id">
						<div class="py-2 d-flex justify-start align-items-top">
							<b-avatar
								rounded
								:variant="!portal.completed ? 'primary' : 'light'"
								class="mr-3 px-0 text-center"
							>
								<font-awesome-icon icon="folder-open" class="px-0 mx-0" />
							</b-avatar>
							<b-row class="flex-fill">
								<b-col cols="12" class="text-left">
									<h6 class="h6 font-weight-bold mb-1" title="Title">
										{{ portal.name }}
									</h6>
									<p v-if="portal.recipient_name" class="small mb-1 text-muted" title="Recipient">
										<font-awesome-icon icon="user" fixed-width class="d-none d-sm-inline" />
										{{ portal.recipient_name }}
									</p>
									<p v-if="portal.created" class="small mb-1 text-muted" title="Date Created">
										<font-awesome-icon icon="clock" fixed-width class="d-none d-sm-inline" />
										Created {{ $filters.fromNow(portal.created) }}
									</p>
								</b-col>
							</b-row>
						</div>
					</b-list-group-item>
				</b-list-group>
			</b-card-body>
			<b-card-footer class="text-right">
				<b-button variant="primary" @click="adding = true"> Create Portal </b-button>
			</b-card-footer>
		</div>
		<validation-observer v-else v-bind="$attrs" ref="observer" v-slot="{ invalid }">
			<b-form @submit.prevent="create">
				<b-card-body>
					<validation-provider
						vid="name"
						name="Name"
						:rules="{ required: true, min: 2, max: 100 }"
						v-slot="validationContext"
					>
						<b-form-group
							label="Name"
							description="The display name of this portal for the third-party viewer"
							label-for="name"
							label-cols="12"
							label-cols-lg="4"
						>
							<b-form-input
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

					<validation-provider
						vid="recipient_name"
						name="Recipient"
						:rules="{ required: true, min: 2, max: 100 }"
						v-slot="validationContext"
					>
						<b-form-group
							label="Recipient"
							description="The name of the intended person or company using this portal"
							label-for="recipient_name"
							label-cols="12"
							label-cols-lg="4"
						>
							<b-form-input
								name="recipient_name"
								type="text"
								v-model="entity.recipient_name"
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
						name="Notes"
						:rules="{ required: false, min: 1, max: 250 }"
						v-slot="validationContext"
					>
						<b-form-group label="Notes" label-for="description" label-cols-lg="4">
							<b-form-textarea
								name="description"
								rows="8"
								placeholder="Description displayed to the recipient..."
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
				</b-card-body>

				<b-card-footer>
					<b-row>
						<b-col cols="12" md="6" xl="4" class="mb-2 mb-md-0">
							<b-button block variant="light" @click.prevent="adding = false">Cancel</b-button>
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
	</div>
</template>

<script type="text/javascript">
import { getValidationState, formatErrors } from "@/validation";

export default {
	name: "AppealGuestPortal",
	props: {
		appeal: {
			type: Object,
			required: true,
			default: () => {
				return {
					id: null,
					guest_portals: [],
				};
			},
		},
		caseEntity: {
			type: Object,
			required: true,
			default: () => {
				return {
					id: null,
					patient_id: null,
					patient: {
						id: null,
						full_name: null,
					},
				};
			},
		},
	},
	data() {
		return {
			adding: false,
			saving: false,
			entity: {
				id: null,
				case_id: this.caseEntity.id,
				appeal_id: this.appeal.id,
				name: this.caseEntity.patient.name ?? "",
				recipient_name: "",
			},
		};
	},
	computed: {
		hasPortals() {
			return this.appeal.guest_portals && this.appeal.guest_portals.length > 0;
		},
	},
	methods: {
		getValidationState,
		async create() {
			try {
				this.saving = true;
				const response = await this.$store.dispatch("guestPortals/create", this.entity);
				this.$emit("saved", response);
				this.adding = false;
			} catch (e) {
				if (e.response.data.errors) {
					this.$refs.observer.setErrors(formatErrors(e.response.data.errors));
				}

				this.$store.dispatch("apiError", {
					error: e,
					title: "Portal Creation Failed",
					message: "Failed to create guest portal. Please check for errors and try again.",
				});
			} finally {
				this.saving = false;
			}
		},
	},
};
</script>
