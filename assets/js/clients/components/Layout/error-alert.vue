<template>
	<div :class="containerClass">
		<h4 :class="headerClass">
			<div>
				<font-awesome-icon v-if="icon" :icon="icon" :size="iconSize" :class="iconClasses" />
			</div>
			<slot></slot>
		</h4>
		<slot name="content"></slot>
	</div>
</template>

<script type="text/javascript">
export default {
	name: "ErrorAlert",
	props: {
		icon: {
			default: "exclamation-triangle",
		},
		iconSize: {
			default: "2x",
		},
		containerClass: {
			default: "alert alert-danger rounded text-center p-5 shadow-sm mb-0",
		},
		headerClass: {
			default: "text-uppercase font-weight-bold",
		},
		iconClasses: {
			default: "mb-4 text-danger",
		},
	},
	data() {
		return {};
	},
	methods: {},
};
</script>
