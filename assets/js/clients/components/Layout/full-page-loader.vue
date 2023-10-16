<template>
	<div class="loader full-page">
		<div class="text-center text-white">
			<h3 class="mb-4 text-white" v-if="title" v-text="title" />
			<loading-indicator size="5x" icon-classes="text-white" />
		</div>
	</div>
</template>

<script>
export default {
	name: "FullPageLoader",
	props: {
		title: {
			type: String,
			default: "Loading",
		},
	},
};
</script>

<style scoped>
.loader.full-page {
	position: fixed;
	left: 0;
	top: 0;

	background: #eee;
	background: radial-gradient(rgb(0, 105, 217), rgb(0, 70, 140));
	background: -webkit-radial-gradient(rgb(0, 105, 217), rgb(0, 70, 140));

	width: 100%;
	height: 100%;

	opacity: 1;
	transition: opacity 1s ease-in-out;
	-moz-transition: opacity 1s ease-in-out;
	-webkit-transition: opacity 1s ease-in-out;

	display: -ms-flexbox;
	display: -webkit-box;
	display: flex;
	-ms-flex-align: center;
	-ms-flex-pack: center;
	-webkit-box-align: center;
	align-items: center;
	-webkit-box-pack: center;
	justify-content: center;
}
</style>
