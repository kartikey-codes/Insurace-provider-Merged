<template>
	<b-input-group title="Assign case to user">
		<b-input-group-addon is-text>
			<font-awesome-icon v-if="assigning" icon="circle-notch" fixed-width spin />
			<font-awesome-icon v-else icon="user" fixed-width />
		</b-input-group-addon>
		<b-form-select
			name="case_assigned_to"
			v-if="canAssign"
			v-model="caseEntity.assigned_to"
			required="required"
			:disabled="inputDisabled"
			:options="users"
			value-field="id"
			text-field="full_name"
			@change="assign"
		>
			<template #first>
				<option :value="null">(Unassigned - Open Queue)</option>
			</template>
		</b-form-select>
		<b-form-input
			v-else
			type="text"
			:readonly="true"
			:value="caseEntity.assigned_to_user ? caseEntity.assigned_to_user.full_name : '(Open Queue)'"
		/>
	</b-input-group>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "AssignCase",
	props: {
		caseEntity: {
			type: Object,
			default: () => {
				return {
					id: null,
					assigned: null,
					assigned_to: null,
					assigned_to_user: null,
				};
			},
		},
		disabled: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		canAssign() {
			if (this.caseEntity.completed) {
				return false;
			}

			return true;
		},
		currentlyAssignedTo() {
			if (this.caseEntity.assigned_to_user && this.caseEntity.assigned_to_user.id) {
				return this.caseEntity.assigned_to_user.id;
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
	data() {
		return {
			assigning: false,
			assignedTo: null,
		};
	},
	mounted() {
		this.assignedTo = this.caseEntity.assigned_to;
	},
	methods: {
		async assign() {
			try {
				this.assigning = true;

				const response = await this.$store.dispatch("cases/assign", {
					id: this.caseEntity.id,
					user_id: this.caseEntity.assigned_to,
				});

				this.assignedTo = response.assigned_to;
				this.caseEntity.assigned_to = response.assigned_to;
				this.$emit("assigned", response);

				if (response.assigned_to_user && response.assigned_to_user.full_name) {
					this.$store.dispatch("notify", {
						title: "Case Assigned",
						message: `Case reassigned to ${response.assigned_to_user.full_name}.`,
						variant: "primary",
					});
				} else {
					this.$store.dispatch("notify", {
						title: "Case Unassigned",
						message: `Case was unassigned and returned to the open queue.`,
						variant: "primary",
					});
				}
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Assign Failed",
					message: "Failed to reassign case.",
				});
			} finally {
				this.assigning = false;
			}
		},
	},
};
</script>
