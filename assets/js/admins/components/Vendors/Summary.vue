<template>
	<div>
		<b-row>
			<b-col cols="12" md="6" class="mb-4">
				<h5 class="h6 font-weight-bold text-muted text-uppercase">Contact</h5>
				<div class="table-responsive">
					<table class="table table-sm table-headers-muted table-data-right mb-0">
						<tr>
							<th>Phone</th>
							<td>
								<span v-if="value.phone">
									<a
										:href="$filters.linkTel(value.phone)"
										class="font-weight-bold text-decoration-none"
										>{{ $filters.formatPhone(value.phone) }}</a
									>
								</span>
								<span v-else class="text-muted-lighter"> None </span>
							</td>
						</tr>
					</table>
				</div>
			</b-col>
			<b-col cols="12" md="6" class="mb-4">
				<h5 class="h6 font-weight-bold text-muted text-uppercase">Details</h5>
				<div class="table-responsive">
					<table class="table table-sm table-headers-muted table-data-right mb-0">
						<tr>
							<th>Owner</th>
							<td>
								<span v-if="value.owner && value.owner.full_name">{{ value.owner.full_name }}</span>
								<span v-else class="text-muted-lighter">None</span>
							</td>
						</tr>
						<tr>
							<th>Specialties</th>
							<td>
								<span v-if="value.specialties && value.specialties.length > 0">
									<b-list-group>
										<b-list-group-item
											v-for="specialty in value.specialties"
											:key="specialty.id"
											class="py-2"
										>
											{{ specialty.name }}
										</b-list-group-item>
									</b-list-group>
								</span>
								<span v-else class="text-muted-lighter">None</span>
							</td>
						</tr>
						<tr>
							<th>Created</th>
							<td>
								<div
									v-if="value.created_by_user && value.created_by_user.full_name"
									class="small font-weight-bold text-uppercase text-muted"
								>
									{{ value.created_by_user.full_name }}
								</div>
								<div v-if="value.created" class="text-muted">
									{{ $filters.formatTimestamp(value.created) }}
								</div>
								<div v-else class="text-danger">Unknown</div>
							</td>
						</tr>
						<tr>
							<th>Modified</th>
							<td>
								<div
									v-if="value.modified_by_user && value.modified_by_user.full_name"
									class="small font-weight-bold text-uppercase text-muted"
								>
									{{ value.modified_by_user.full_name }}
								</div>

								<div v-if="value.modified" class="text-muted">
									{{ $filters.formatTimestamp(value.modified) }}
								</div>
								<div v-else class="text-danger">Unknown</div>
							</td>
						</tr>
					</table>
				</div>
			</b-col>
		</b-row>
	</div>
</template>

<script type="text/javascript">
export default {
	name: "VendorSummary",
	props: {
		value: {
			required: true,
			type: Object,
			default: () => {
				return {
					id: null,
					name: null,
				};
			},
		},
	},
};
</script>
