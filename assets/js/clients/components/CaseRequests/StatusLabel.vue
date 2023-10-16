<template>
	<b-badge :pill="pill" :variant="variant" v-bind="$attrs" :class="icon ? 'text-center' : ''">
		<span v-if="!icon" v-text="label" />
		<font-awesome-icon v-else :icon="statusIcon" class="mx-0 px-0" />
	</b-badge>
</template>

<script>
import { mapGetters } from "vuex";

export default {
	name: "CaseRequestStatusLabel",
	props: {
		value: {
			type: Object,
			required: true,
			default: () => {
				return {
					id: null,
					status_label: null,
				};
			},
		},
		icon: {
			type: Boolean,
			default: false,
		},
		noColor: {
			type: Boolean,
			default: false,
		},
		pill: {
			type: Boolean,
			default: true,
		},
	},
	computed: {
		label() {
			const value = this.value.status_label;
			return this.statuses[value] || value || "Unknown";
		},
		variant() {
			if (this.noColor) {
				return "light";
			}

			switch (this.value.status_label) {
				case "Open":
					return "primary";
					break;
				case "Unable To Complete":
					return "warning";
					break;
				case "Completed":
					return "success";
					break;
				default:
					return "dark";
					break;
			}
		},
		statusIcon() {
			switch (this.value.status_label) {
				case "Open":
					return "envelope-open";
					break;
				case "Unable To Complete":
					return "exclamation-triangle";
					break;
				case "Completed":
					return "check-circle";
					break;
				default:
					return "info-circle";
					break;
			}
		},
		...mapGetters({
			statuses: "caseRequests/statuses",
		}),
	},
};
</script>
