<template>
	<b-jumbotron bg-variant="white" text-variant="dark" class="m-0 px-0 py-3 rounded-0 shadow-sm">
		<b-container v-bind="{ fluid }">
			<b-row>
				<b-col
					:cols="hasSearch ? 12 : 6"
					:md="hasSearch ? 12 : 6"
					:lg="hasSearch ? 4 : 6"
					class="text-center text-lg-left d-flex align-items-center"
				>
					<div
						class="d-flex align-items-center"
						:class="{
							'mb-4 mb-md-4 mb-lg-0': hasSearch && hasButtons,
							'mb-0 mb-md-0 mb-lg-0': !hasSearch && !hasButtons,
							'mb-0 mb-md-0 mb-lg-0': hasButtons && !hasSearch,
						}"
					>
						<h1 class="h5 d-inline-block my-0 py-0 font-weight-bold text-dark">
							<slot name="title"></slot>
						</h1>
						<b-badge
							v-if="hasTotal"
							pill
							variant="light"
							class="d-inline-block ml-4 my-0 text-monospace text-muted shadow-sm"
						>
							<slot name="total"></slot>
						</b-badge>
					</div>
				</b-col>
				<b-col cols="12" md="6" lg="4" class="mb-4 mb-md-0 mb-lg-0" v-if="hasSearch">
					<slot name="search"></slot>
				</b-col>
				<b-col
					v-if="hasButtons"
					:cols="hasSearch ? 12 : 6"
					:md="hasSearch ? 6 : 6"
					:lg="hasSearch ? 4 : 6"
					class="text-right text-md-right text-lg-right"
				>
					<slot name="buttons"></slot>
				</b-col>
			</b-row>
		</b-container>
	</b-jumbotron>
</template>

<script type="text/javascript">
export default {
	name: "Header",
	props: {
		fluid: {
			type: Boolean,
			default: true,
		},
	},
	computed: {
		hasButtons() {
			return !!this.$slots["buttons"];
		},
		hasSearch() {
			return !!this.$slots["search"];
		},
		hasTotal() {
			return !!this.$slots["total"];
		},
	},
};
</script>
