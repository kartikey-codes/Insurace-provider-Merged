<template>
	<b-jumbotron bg-variant="white" text-variant="dark" class="m-0 px-0 py-3 rounded-0 shadow-sm">
		<b-container v-bind="{ fluid }">
			<b-row>
				<b-col
					:cols="hasButtons ? 5 : 12"
					:md="hasButtons ? 6 : 12"
					:lg="hasButtons ? 6 : 12"
					class="text-center text-lg-left d-flex align-items-center"
				>
					<div class="d-flex align-items-center mb-0 mb-md-0 mb-lg-0">
						<slot name="beforeTitle"></slot>
						<h1 class="h5 d-inline-block my-0 py-0 font-weight-bold text-dark text-break">
							<slot name="title"></slot>
						</h1>
						<transition name="fade" mode="out-in">
							<b-badge v-if="loading && hasTotal" pill variant="light" class="ml-4 my-0 text-muted">
								<font-awesome-icon icon="spinner" spin fixed-width />
							</b-badge>
							<b-badge v-else-if="hasTotal" pill variant="light" class="ml-4 my-0 text-muted">
								{{ $filters.formatNumber(total) }}
							</b-badge>
						</transition>
						<slot name="afterTitle"></slot>
					</div>
				</b-col>
				<b-col v-if="hasButtons" :cols="7" :md="6" :lg="6" class="text-right text-md-right text-lg-right">
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
		loading: {
			type: Boolean,
			default: false,
		},
		total: {
			type: [Number, Boolean],
			default: false,
		},
	},
	computed: {
		hasButtons() {
			return !!this.$slots["buttons"];
		},
		hasTotal() {
			return this.total && this.total > 0;
		},
	},
};
</script>
