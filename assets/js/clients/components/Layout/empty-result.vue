<template>
	<transition appear enter-active-class="fadeIn" leave-active-class="fadeOut">
		<div :class="containerClass">
			<h4 :class="headerClass">
				<div>
					<font-awesome-icon v-if="icon" :icon="icon" :size="iconSize" :class="iconClasses" />
				</div>
				<slot></slot>
			</h4>
			<slot name="content"></slot>
		</div>
	</transition>
</template>

<script type="text/javascript">
export default {
	name: "EmptyResult",
	props: {
		icon: {
			default: "search",
		},
		iconSize: {
			default: "2x",
		},
		containerClass: {
			default: "bg-white text-muted rounded text-center p-5 shadow-sm mb-0",
		},
		headerClass: {
			default: "text-uppercase font-weight-bold text-muted",
		},
		iconClasses: {
			default: "mb-4 text-muted",
		},
	},
};
</script>
