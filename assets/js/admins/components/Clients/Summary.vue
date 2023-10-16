<template>
	<div>
		<b-row>
			<b-col cols="12" sm="12" md="12" lg="6" xl="4" class="mb-4">
				<h5 class="h6 font-weight-bold text-muted text-uppercase">Contact</h5>
				<div class="table-responsive">
					<table class="table table-sm table-headers-muted table-data-right mb-0">
						<tr>
							<th>Phone</th>
							<td>
								<span v-if="client.phone">
									<a
										:href="$filters.linkTel(client.phone)"
										class="font-weight-bold text-decoration-none"
										>{{ $filters.formatPhone(client.phone) }}</a
									>
								</span>
								<span v-else class="text-muted-lighter"> None </span>
							</td>
						</tr>
						<tr>
							<th>Fax</th>
							<td>
								<span v-if="client.fax">
									<a
										:href="$filters.linkTel(client.fax)"
										class="font-weight-bold text-decoration-none"
										>{{ $filters.formatPhone(client.fax) }}</a
									>
								</span>
								<span v-else class="text-muted-lighter"> None </span>
							</td>
						</tr>
						<th>Group Email</th>
						<td>
							<span v-if="client.email">
								<a :href="'mailto:' + client.email" class="font-weight-bold text-decoration-none">{{
									client.email
								}}</a>
							</span>
							<span v-else class="text-muted-lighter"> None </span>
						</td>
					</table>
				</div>
			</b-col>
			<b-col cols="12" sm="12" md="12" lg="6" xl="4" class="mb-4">
				<h5 class="h6 font-weight-bold text-muted text-uppercase">Contact Person</h5>
				<div class="table-responsive">
					<table class="table table-sm table-headers-muted table-data-right mb-0">
						<tr>
							<th>First Name</th>
							<td>
								<span v-if="client.contact_first_name">
									{{ client.contact_first_name }}
								</span>
								<span v-else class="text-muted-lighter"> None </span>
							</td>
						</tr>
						<tr>
							<th>Last Name</th>
							<td>
								<span v-if="client.contact_last_name">
									{{ client.contact_last_name }}
								</span>
								<span v-else class="text-muted-lighter"> None </span>
							</td>
						</tr>
						<tr>
							<th>Title</th>
							<td>
								<span v-if="client.contact_title">
									{{ client.contact_title }}
								</span>
								<span v-else class="text-muted-lighter"> None </span>
							</td>
						</tr>
						<tr>
							<th>Email</th>
							<td>
								<span v-if="client.contact_email">
									<a
										:href="'mailto:' + client.contact_email"
										class="font-weight-bold text-decoration-none"
										>{{ client.contact_email }}</a
									>
								</span>
								<span v-else class="text-muted-lighter"> None </span>
							</td>
						</tr>
						<tr>
							<th>Phone</th>
							<td>
								<span v-if="client.contact_phone">
									<a
										:href="$filters.linkTel(client.contact_phone)"
										class="font-weight-bold text-decoration-none"
										>{{ $filters.formatPhone(client.contact_phone) }}</a
									>
								</span>
								<span v-else class="text-muted-lighter"> None </span>
							</td>
						</tr>
						<tr>
							<th>Fax</th>
							<td>
								<span v-if="client.contact_fax">
									<a
										:href="$filters.linkTel(client.contact_fax)"
										class="font-weight-bold text-decoration-none"
										>{{ $filters.formatPhone(client.contact_fax) }}</a
									>
								</span>
								<span v-else class="text-muted-lighter"> None </span>
							</td>
						</tr>
					</table>
				</div>
			</b-col>
			<b-col cols="12" sm="12" md="12" lg="6" xl="4" class="mb-4">
				<h5 class="h6 font-weight-bold text-muted text-uppercase">Details</h5>
				<div class="table-responsive">
					<table class="table table-sm table-headers-muted table-data-right mb-0">
						<tr>
							<th>Status</th>
							<td>
								<span v-if="client.status">
									{{ client.status }}
								</span>
								<span v-else class="text-muted-lighter"> None </span>
							</td>
						</tr>
						<tr>
							<th>Created</th>
							<td>
								<div
									v-if="client.created_by_user && client.created_by_user.full_name"
									class="small font-weight-bold text-uppercase text-muted"
								>
									{{ client.created_by_user.full_name }}
								</div>
								<div v-if="client.created" class="text-muted">
									{{ $filters.formatTimestamp(client.created) }}
								</div>
								<div v-else class="text-danger">Unknown</div>
							</td>
						</tr>
						<tr>
							<th>Modified</th>
							<td>
								<div
									v-if="client.modified_by_user && client.modified_by_user.full_name"
									class="small font-weight-bold text-uppercase text-muted"
								>
									{{ client.modified_by_user.full_name }}
								</div>

								<div v-if="client.modified" class="text-muted">
									{{ $filters.formatTimestamp(client.modified) }}
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
	name: "ClientSummary",
	props: {
		client: {
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
