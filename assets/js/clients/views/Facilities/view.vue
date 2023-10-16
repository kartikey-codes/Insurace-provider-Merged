<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'facilities' }" v-text="`Facilities /`" />
				<span>{{ entity.name }}</span>
			</template>
			<template #buttons>
				<b-button
					variant="primary"
					:to="{ name: 'facilities.edit', params: { id: $route.params.id } }"
					title="Edit"
				>
					<font-awesome-icon icon="edit" fixed-width />
					<span>Edit</span>
				</b-button>

				<b-dropdown variant="secondary" right title="More Options">
					<template #button-content>
						<font-awesome-icon icon="cog" fixed-width />
					</template>

					<b-dropdown-item :to="{ name: 'cases.add', query: { facility_id: $route.params.id } }">
						<font-awesome-icon icon="plus" fixed-width />
						<span>New Case</span>
					</b-dropdown-item>

					<b-dropdown-divider />D

					<b-dropdown-item @click="destroy" :disabled="loading" variant="danger">
						<font-awesome-icon icon="trash" fixed-width />
						<span>Delete</span>
					</b-dropdown-item>
				</b-dropdown>
			</template>
		</page-header>

		<loading-indicator v-if="showLoading" class="my-5" />
		<div v-else class="my-4">
			<b-row>
				<b-col v-if="entity.full_address" cols="12" class="mb-2">
					<p class="text-muted mb-0" title="Address">
						<font-awesome-icon icon="location-dot" fixed-width />
						{{ entity.full_address }}
					</p>
				</b-col>
				<b-col cols="12" sm="6" lg="6" class="mb-2">
					<b-badge pill variant="primary" v-if="entity.active">Active</b-badge>
					<b-badge pill variant="secondary" v-else>Inactive</b-badge>
					<b-badge pill v-if="entity.client_owned" variant="info"> Owned </b-badge>
					<b-badge pill v-if="entity.has_contract" variant="info"> Contracted </b-badge>
				</b-col>
				<b-col cols="12" sm="6" lg="6" class="mb-2 text-sm-right">
					<p class="small text-muted">
						<span> Last updated {{ $filters.fromNow(entity.modified) }} </span>
						<span v-if="entity.modified_by_user?.full_name">
							by {{ entity.modified_by_user.full_name }}
						</span>
					</p>
				</b-col>
			</b-row>

			<b-card no-body class="shadow-sm">
				<b-tabs card active-nav-item-class="font-weight-bold">
					<b-tab>
						<template #title>Details</template>
						<b-row>
							<b-col cols="12" sm="6" lg="6" class="mb-4">
								<h6 class="h6 text-uppercase font-weight-bold text-muted">Details</h6>
								<div class="table-responsive">
									<table class="table table-sm table-headers-muted table-data-right mb-0">
										<tr>
											<th>Type</th>
											<td>
												<span v-if="entity.facility_type && entity.facility_type.name">
													{{ entity.facility_type.name }}
												</span>
												<span v-else class="text-muted">&mdash;</span>
											</td>
										</tr>
										<tr>
											<th>Chain</th>
											<td>
												<span v-if="entity.chain_name">
													{{ entity.chain_name }}
												</span>
												<span v-else class="text-muted">&mdash;</span>
											</td>
										</tr>
										<tr>
											<th>Area</th>
											<td>
												<span v-if="entity.area_name">
													{{ entity.area_name }}
												</span>
												<span v-else class="text-muted">&mdash;</span>
											</td>
										</tr>
										<tr>
											<th>OU Number</th>
											<td>
												<span v-if="entity.ou_number">
													{{ entity.ou_number }}
												</span>
												<span v-else class="text-muted">&mdash;</span>
											</td>
										</tr>
										<tr>
											<th>Territory</th>
											<td>
												<span v-if="entity.territory">
													{{ entity.territory }}
												</span>
												<span v-else class="text-muted">&mdash;</span>
											</td>
										</tr>
										<tr>
											<th>RVP</th>
											<td>
												<span v-if="entity.rvp_name">
													{{ entity.rvp_name }}
												</span>
												<span v-else class="text-muted">&mdash;</span>
											</td>
										</tr>
									</table>
								</div>
							</b-col>
							<b-col cols="12" sm="6" lg="6" class="mb-4">
								<h6 class="h6 text-uppercase font-weight-bold text-muted">Contact</h6>
								<div class="table-responsive">
									<table class="table table-sm table-headers-muted table-data-right mb-0">
										<tr>
											<th>Phone</th>
											<td>
												<span v-if="entity.phone">
													<a
														:href="$filters.linkTel(entity.phone)"
														class="font-weight-bold text-decoration-none"
														>{{ $filters.formatPhone(entity.phone) }}</a
													>
												</span>
												<span v-else class="text-muted"> &mdash; </span>
											</td>
										</tr>
										<tr>
											<th>Fax</th>
											<td>
												<span v-if="entity.fax">
													<a
														:href="$filters.linkTel(entity.fax)"
														class="font-weight-bold text-decoration-none"
														>{{ $filters.formatPhone(entity.fax) }}</a
													>
												</span>
												<span v-else class="text-muted"> &mdash; </span>
											</td>
										</tr>
										<th>Email</th>
										<td>
											<span v-if="entity.email">
												<a
													:href="'mailto:' + entity.email"
													class="font-weight-bold text-decoration-none"
													>{{ entity.email }}</a
												>
											</span>
											<span v-else class="text-muted"> &mdash; </span>
										</td>
									</table>
								</div>
							</b-col>
							<b-col cols="12" sm="6" lg="6" class="mb-4">
								<h6 class="h6 text-uppercase font-weight-bold text-muted">Contract</h6>
								<div class="table-responsive">
									<table class="table table-sm table-headers-muted table-data-right mb-0">
										<tr>
											<th>Has Contract</th>
											<td>
												<div v-if="entity.has_contract">Yes</div>
												<div v-else>No</div>
											</td>
										</tr>
										<tr>
											<th>Start Date</th>
											<td>
												<div v-if="entity.contract_start_date">
													<p class="mb-0">
														{{ $filters.formatDate(entity.contract_start_date) }}
													</p>
													<p class="small text-muted mb-0">
														{{ $filters.fromNow(entity.contract_start_date) }}
													</p>
												</div>
												<span v-else class="text-muted"> &mdash; </span>
											</td>
										</tr>
										<tr>
											<th>End Date</th>
											<td>
												<div v-if="entity.contract_end_date">
													<p class="mb-0">
														{{ $filters.formatDate(entity.contract_end_date) }}
													</p>
													<p class="small text-muted mb-0">
														{{ $filters.fromNow(entity.contract_end_date) }}
													</p>
												</div>
												<span v-else class="text-muted"> &mdash; </span>
											</td>
										</tr>
										<tr>
											<th>Indemnification Days</th>
											<td>
												<span v-if="entity.indemnification_days">
													{{ entity.indemnification_days }}
												</span>
												<span v-else class="text-muted"> &mdash; </span>
											</td>
										</tr>
										<tr>
											<th>Max Return Work Days</th>
											<td>
												<span v-if="entity.max_return_work_days">
													{{ entity.max_return_work_days }}
												</span>
												<span v-else class="text-muted"> &mdash; </span>
											</td>
										</tr>
									</table>
								</div>
							</b-col>

							<b-col cols="12" sm="6" lg="6" class="mb-4">
								<h6 class="h6 text-uppercase font-weight-bold text-muted">Services</h6>

								<b-list-group v-if="entity.services && entity.services.length > 0">
									<b-list-group-item
										v-for="service in entity.services"
										:key="service.index"
										:to="{ name: 'services.view', params: { id: service.id } }"
									>
										{{ service.name }}
									</b-list-group-item>
								</b-list-group>
								<empty-result v-else> No services assigned </empty-result>
							</b-col>
						</b-row>
					</b-tab>
					<b-tab no-body active lazy>
						<template #title>Cases</template>
						<case-index
							ref="caseList"
							hide-facility
							:filters="caseFilters"
							@clicked="viewCase"
							empty-description="No cases have been created for this facility."
						/>
					</b-tab>
					<b-tab no-body lazy>
						<template #title>Add Case</template>
						<case-form flush :facility-id="$route.params.id" />
					</b-tab>
				</b-tabs>
			</b-card>
		</div>
	</div>
</template>

<script type="text/javascript">
import { get as GetFacility, destroy as DeleteFacility } from "@/clients/services/facilities";

import CaseForm from "@/clients/components/Cases/Form.vue";
import CaseIndex from "@/clients/components/Cases/Index.vue";

export default {
	name: "FacilityView",
	components: {
		CaseForm,
		CaseIndex,
	},
	data() {
		return {
			loading: true,
			entity: {
				id: null,
				name: null,
				active: null,
				full_address: null,
			},
			similar: [],
		};
	},
	computed: {
		caseFilters() {
			return {
				facility_id: this.$route.params.id,
			};
		},
		showLoading() {
			return this.loading && (!this.entity.id || this.entity.id == null);
		},
	},
	mounted() {
		this.refresh();
	},
	methods: {
		async refresh() {
			try {
				this.loading = true;
				this.entity = await GetFacility(this.$route.params.id);
			} catch (e) {
				this.$router.push({ name: "facilities" });
			} finally {
				this.loading = false;
			}
		},
		async destroy() {
			if (!confirm("Are you sure you want to delete this facility?")) {
				return;
			}

			try {
				this.deleting = true;
				const response = await DeleteFacility(this.$route.params.id);
				this.$store.dispatch("facilities/getActive");
				this.$router.push({ name: "facilities" });

				this.$nextTick(function () {
					this.$store.dispatch("notify", {
						variant: "primary",
						title: "Facility Deleted",
						message: `Facility ${response.data.name} was deleted.`,
					});
				});
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Delete Failed",
					message: "Failed to delete facility",
				});
			} finally {
				this.deleting = false;
			}
		},
		viewCase(caseEntity) {
			this.$router.push({
				name: "cases.view",
				params: {
					id: caseEntity.id,
				},
			});
		},
	},
	watch: {
		"$route.params.id": {
			handler(val) {
				this.entity = {
					id: null,
					name: null,
				};

				this.refresh();
			},
		},
	},
};
</script>
