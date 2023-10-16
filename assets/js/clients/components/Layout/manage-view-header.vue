<template>
	<div v-bind="$attrs">
		<b-row v-if="showBreadcrumbs" class="d-block d-md-none">
			<b-col cols="12" class="my-2 my-md-0">
				<slot name="breadcrumbs"></slot>
			</b-col>
		</b-row>
		<b-row>
			<b-col
				:cols="hasButtons ? 12 : 12"
				:md="hasButtons ? 6 : 12"
				:lg="hasButtons ? 6 : 12"
				class="mb-2 mb-md-0 text-left text-lg-left d-flex align-items-center"
			>
				<slot name="beforeTitle"></slot>
				<h1 class="h4 d-inline-block my-0 py-0 font-weight-bold text-dark">
					<slot name="title"></slot>
				</h1>
				<slot name="afterTitle"></slot>
			</b-col>
			<b-col v-if="hasButtons" :cols="12" :md="6" :lg="6" class="text-left text-md-right text-lg-right">
				<slot name="buttons"></slot>
			</b-col>
		</b-row>
	</div>
</template>

<script type="text/javascript">
export default {
	name: "ManageViewHeader",
	props: {
		fluid: {
			type: Boolean,
			default: true,
		},
	},
	computed: {
		showBreadcrumbs() {
			return !!this.$slots["breadcrumbs"];
		},
		hasButtons() {
			return !!this.$slots["buttons"];
		},
		hasTotal() {
			return !!this.$slots["total"];
		},
	},
};
</script>
