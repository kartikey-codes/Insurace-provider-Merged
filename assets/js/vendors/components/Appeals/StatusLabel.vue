<template>
	<b-badge
		pill
		:variant="variant"
		class="text-uppercase font-weight-bold"
	>
		<span
			v-if="label"
			v-text="label"
		/>
		<span v-else>Unknown</span>
	</b-badge>
</template>

<script>
import { mapGetters } from "vuex";

export default {
	name: "AppealStatusLabel",
	props: {
		value: {
			type: Object,
			required: true,
			default: () => {
				return {
					id: null,
					appeal_status: null
				};
			},
		},
	},
	computed: {
		label() {
			const value = this.value.appeal_status;
			return this.appealStatuses[value] || value || 'Unknown';
		},
		variant() {
			switch (this.value.appeal_status) {
				case "Open":
					return "light";
					break;
				case "Submitted":
					return "info";
					break;
				case "Assigned":
					return "primary";
					break;
				case "Completed":
					return "success";
					break;
				case "Returned":
					return "warning";
					break;
				case "Cancelled":
					return "danger";
					break;
				case "Closed":
					return "success";
					break;
				default:
					return "dark";
					break;
			}
		},
		...mapGetters({
			appealStatuses: "appeals/statuses"
		})
	},
};
</script>
