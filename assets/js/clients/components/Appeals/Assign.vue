<template>
	<b-input-group title="Assign Appeal to user">
		<b-input-group-addon is-text>
			<font-awesome-icon v-if="assigning" icon="circle-notch" fixed-width spin />
			<font-awesome-icon v-else icon="user" fixed-width />
		</b-input-group-addon>
		<b-form-select
			name="appeal_assigned_to"
			v-model="assignedTo"
			required="required"
			:disabled="!canAssign || inputDisabled"
			:options="users"
			value-field="id"
			text-field="full_name"
			@change="assign"
		>
			<template #first>
				<option :value="null">(Unassigned - Open Queue)</option>
			</template>
		</b-form-select>
	</b-input-group>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "AssignAppeal",
	props: {
		appeal: {
			required: true,
			type: Object,
			default: () => {
				return {
					id: null,
					assigned: null,
					assigned_to: null,
					assigned_to_user: null,
					completed: null,
					closed: null,
				};
			},
		},
		disabled: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			assigning: false,
			assignedTo: null,
		};
	},
	computed: {
		canAssign() {
			return this.appeal.completed || this.appeal.closed ? false : true;
		},
		currentlyAssignedTo() {
			if (this.appeal.assigned_to_user && this.appeal.assigned_to_user.id) {
				return this.appeal.assigned_to_user.id;
			}

			return false;
		},
		inputDisabled() {
			if (this.disabled) return true;
			if (this.assigning) return true;
			if (!this.users || !this.users.length > 0) return true;

			return false;
		},
		...mapGetters({
			users: "users/active",
			loadingUsers: "users/loadingActive",
		}),
	},
	mounted() {
		this.assignedTo = this.appeal.assigned_to;
	},
	methods: {
		async assign() {
			try {
				this.assigning = true;

				const response = await this.$store.dispatch("appeals/assign", {
					id: this.appeal.id,
					user_id: this.assignedTo,
				});

				this.assignedTo = response.assigned_to;
				this.$emit("assigned", response);

				if (response.assigned_to_user && response.assigned_to_user.full_name) {
					this.$store.dispatch("notify", {
						title: "Appeal Assigned",
						message: `Appeal reassigned to ${response.assigned_to_user.full_name}.`,
						variant: "primary",
					});
				} else {
					this.$store.dispatch("notify", {
						title: "Appeal Unassigned",
						message: `Appeal was unassigned and returned to the open queue.`,
						variant: "primary",
					});
				}
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Assign Failed",
					message: "Failed to assign appeal",
				});
			} finally {
				this.assigning = false;
			}
		},
	},
};
</script>
