<template>
	<b-badge
		v-if="!avatar"
		:pill="pill"
		:variant="value.status_variant"
		v-bind="$attrs"
		:class="icon ? 'text-center' : ''"
	>
		<span v-if="!icon">{{ value.appeal_status }}</span>
		<font-awesome-icon v-else :icon="value.status_icon" class="mx-0 px-0" />
	</b-badge>
	<b-avatar v-else :variant="value.status_variant" v-bind="$attrs" :class="icon ? 'text-center' : ''">
		<font-awesome-icon :icon="value.status_icon" class="mx-0 px-0" />
	</b-avatar>
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
					appeal_status: null,
					status_icon: "",
					status_variant: "",
				};
			},
		},
		avatar: {
			type: Boolean,
			default: false,
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
		...mapGetters({
			appealStatuses: "appeals/statuses",
		}),
	},
};
</script>
