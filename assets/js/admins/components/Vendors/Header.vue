<template>
	<div>
		<b-row class="d-flex align-items-center">
			<b-col md="12">

				<h2 class="d-inline h1 mb-0 font-weight-bold text-uppercase">
					<span v-if="vendor.name">
						{{ vendor.name }}
					</span>
					<span
						v-else
						class="text-danger"
					>
						<font-awesome-icon
							icon="exclamation-triangle"
							fixed-width
						/>
						Missing Name
					</span>

					<b-badge
						pill
						v-if="!vendor.active"
						variant="secondary"
					>
						{{ vendor.status }}
					</b-badge>
				</h2>

				<h4
					v-if="vendor.full_address"
					class="mb-2 h5 text-muted font-weight-bold text-uppercase"
				>
					<span v-if="vendor.full_address">
						{{ vendor.full_address }}
					</span>
					<span
						v-else
						class="text-danger"
					>
						<font-awesome-icon
							icon="exclamation-triangle"
							fixed-width
						/>
						No Address
					</span>
				</h4>

			</b-col>
		</b-row>
	</div>
</template>

<script type="text/javascript">
export default {
	name: "VendorHeader",
	props: {
		vendor: {
			type: Object,
			default: () => {},
		},
	},
	data() {
		return {};
	},

	methods: {},
};
</script>
