<template>
	<div class="text-center mx-auto">
		<h4 v-if="title && title.length">{{ title }}</h4>
		<font-awesome-icon align="center" :icon="icon" fixed-width spin :size="size" :class="iconClasses" />
	</div>
</template>

<script>
export default {
	name: "LoadingIndicator",
	props: {
		size: {
			type: String,
			default: "5x",
		},
		icon: {
			type: String,
			default: "circle-notch",
		},
		title: {
			type: String,
			default: "",
		},
		iconClasses: {
			type: String,
			default: "text-muted",
		},
	},
};
</script>
