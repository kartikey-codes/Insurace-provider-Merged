<template>
	<loading-indicator v-if="loading" class="my-5" title="Fetching role..." />
	<validation-observer
		v-else
		tag="form"
		v-bind="$attrs"
		ref="observer"
		v-slot="{ validated, invalid }"
		@submit.prevent="save"
	>
		<b-card no-body>
			<slot name="header" v-bind="{ invalid, loading, saving, save, validated }"></slot>
			<b-card-body>
				<form-group
					name="name"
					label="Name"
					:rules="{ required: true, min: 2, max: 60 }"
					v-slot="{ state }"
					label-cols-lg="4"
					label-cols-xl="2"
					class="mb-4"
				>
					<b-form-input
						name="name"
						type="text"
						size="lg"
						v-model="entity.name"
						required
						placeholder="Required"
						:state="state"
						:disabled="saving"
					/>
				</form-group>
				<b-row>
					<b-col cols="12" md="6" lg="6">
						<form-group label="Members" name="users._ids" label-cols-lg="4">
							<b-form-checkbox-group
								stacked
								name="user_ids"
								v-model="entity.users._ids"
								:options="users"
								:disabled="saving || loadingUsers"
								value-field="id"
								text-field="full_name"
							/>
						</form-group>
					</b-col>
					<b-col cols="12" md="6" lg="6">
						<form-group label="Permissions" name="permissions._ids" label-cols-lg="4">
							<b-form-checkbox-group
								stacked
								name="permission_ids"
								v-model="entity.permissions._ids"
								:options="permissions"
								:disabled="saving || loadingPermissions"
								value-field="id"
								text-field="name"
							/>
						</form-group>
					</b-col>
				</b-row>
			</b-card-body>

			<slot name="footer" v-bind="{ cancel, invalid, saving, save }">
				<b-card-footer>
					<b-row>
						<b-col cols="12" md="6" lg="4" class="mb-4 mb-md-0">
							<b-button block variant="light" type="button" @click.prevent="cancel">Cancel</b-button>
						</b-col>
						<b-col cols="12" md="6" offset-lg="4" lg="4" class="mb-2 mb-md-0">
							<b-button block variant="primary" type="submit" :disabled="saving">
								<font-awesome-icon icon="circle-notch" v-if="saving" spin fixed-width />
								<font-awesome-icon icon="exclamation-triangle" v-else-if="invalid" fixed-width />
								<span>Save</span>
							</b-button>
						</b-col>
					</b-row>
				</b-card-footer>
			</slot>
		</b-card>
	</validation-observer>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import { save, get } from "@/clients/services/roles";
import { formatErrors, getValidationState } from "@/validation";

export default {
	name: "RoleForm",
	props: {
		id: {
			default: null,
		},
	},
	computed: mapGetters({
		users: "users/active",
		loadingUsers: "users/loadingActive",
		permissions: "permissions/all",
		loadingPermissions: "permissions/loadingAll",
	}),
	data() {
		return {
			loading: true,
			saving: false,
			entity: {
				id: this.id,
				name: "",
				permissions: {
					_ids: [],
				},
				users: {
					_ids: [],
				},
			},
		};
	},
	mounted() {
		this.$store.dispatch("permissions/getAll");

		if (this.id) {
			this.refresh();
		} else {
			this.loading = false;
		}
	},
	methods: {
		getValidationState,
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
				this.entity.permissions = {
					_ids: response.permissions.map((permission) => permission.id),
				};
				this.entity.users = {
					_ids: response.users.map((user) => user.id),
				};

				this.$emit("loaded", this.entity);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Error getting role details",
				});
			} finally {
				this.loading = false;
			}
		},
		async save() {
			try {
				this.saving = true;

				const response = await save(this.entity);

				this.$emit("saved", response);
				this.$emit("update:id", response.id);
			} catch (e) {
				if (e.response.data.errors) {
					this.$refs.observer.setErrors(formatErrors(e.response.data.errors));
				}

				this.$store.dispatch("apiError", {
					error: e,
					title: "Save Failed",
					message: "Failed to save role details. Please check for errors.",
					variant: "warning",
				});
			} finally {
				this.saving = false;
			}
		},
	},
};
</script>
