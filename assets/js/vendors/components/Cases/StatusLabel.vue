<template>
	<b-badge :pill="pill" :variant="variant">
		<span v-if="label" v-text="label" />
		<span v-else>Unknown</span>
	</b-badge>
</template>

<script>
import { mapGetters } from "vuex";

export default {
	name: "CaseStatusLabel",
	props: {
		value: {
			type: Object,
			required: true,
			default: () => {
				return {
					status: null,
				};
			},
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
			const value = this.value.status;
			return this.caseStatuses.find((status) => status.value == value)?.name || "Unknown";
		},
		variant() {
			if (this.noColor) {
				return "light";
			}

			switch (this.value.status) {
				case "Open":
					return "primary";
					break;
				case "Closed":
					return "success";
					break;
				default:
					return "light";
					break;
			}
		},
		...mapGetters({
			caseStatuses: "cases/statuses",
		}),
	},
};
</script>
