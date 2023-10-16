<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'auditReviewers' }" v-text="`Audit Reviewers /`" />
				<span>{{ entity.full_name }}</span>
			</template>
			<template #buttons>
				<b-button variant="primary" :to="{ name: 'auditReviewers.edit', params: { id } }" title="Edit">
					<font-awesome-icon icon="edit" fixed-width />
					<span>Edit</span>
				</b-button>

				<b-dropdown variant="secondary" right title="More Options">
					<template #button-content>
						<font-awesome-icon icon="cog" fixed-width />
					</template>

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
				<b-col v-if="entity.agency && entity.agency.name" cols="12" class="mb-3">
					<p class="text-muted mb-0" title="Agency">
						<font-awesome-icon icon="building" fixed-width />
						<router-link
							:to="{
								name: 'agencies.view',
								params: { id: entity.agency.id },
							}"
						>
							{{ entity.agency.name }}
						</router-link>
					</p>
				</b-col>
				<b-col cols="12">
					<b-card-group class="shadow-sm mb-4">
						<b-card class="text-center">
							<p class="h4 font-weight-bold mb-1">
								<span v-if="!totalAppeals" class="text-muted">&mdash;</span>
								<span v-else>{{ totalAppeals }}%</span>
							</p>
							<p class="small text-muted mb-0">Total Appeals</p>
						</b-card>
						<b-card class="text-center">
							<p class="h4 font-weight-bold mb-1">
								<span v-if="!favorableAppealsPercent" class="text-muted">&mdash;</span>
								<span v-else>{{ favorableAppealsPercent }}%</span>
							</p>
							<p class="small text-muted mb-0">Favorable Appeals</p>
						</b-card>
					</b-card-group>
				</b-col>
			</b-row>

			<b-card no-body class="shadow-sm">
				<b-tabs card active-nav-item-class="font-weight-bold">
					<b-tab lazy>
						<template #title>Details</template>
						<b-row>
							<b-col cols="12" lg="6" class="mb-2">
								<h6 class="h6 text-muted text-uppercase font-weight-bold">Reviewer</h6>
								<dl class="mb-0">
									<div v-if="entity.agency && entity.agency.name" class="row">
										<dt class="col-5 text-muted h6 small">Agency</dt>
										<dd class="col-7">
											<router-link
												:to="{
													name: 'agencies.view',
													params: { id: entity.agency.id },
												}"
											>
												{{ entity.agency.name }}
											</router-link>
										</dd>
									</div>
									<div class="row">
										<dt class="col-5 text-muted h6 small">Title</dt>
										<dd class="col-7">
											<div v-if="entity.title">
												{{ entity.title }}
											</div>
											<div v-else class="text-muted">&mdash;</div>
										</dd>
									</div>
									<div class="row">
										<dt class="col-5 text-muted h6 small">Professional Degree</dt>
										<dd class="col-7">
											<div v-if="entity.professional_degree">
												{{ entity.professional_degree }}
											</div>
											<div v-else class="text-muted">&mdash;</div>
										</dd>
									</div>
									<div class="row">
										<dt class="col-5 text-muted h6 small">Email</dt>
										<dd class="col-7">
											<div v-if="entity.email">
												{{ entity.email }}
											</div>
											<div v-else class="text-muted">&mdash;</div>
										</dd>
									</div>
									<div class="row">
										<dt class="col-5 text-muted h6 small">Phone</dt>
										<dd class="col-7">
											<div v-if="entity.phone">
												{{ entity.phone }}
											</div>
											<div v-else class="text-muted">&mdash;</div>
										</dd>
									</div>
								</dl>
							</b-col>
							<b-col cols="12" lg="6" class="mb-2">
								<h6 class="mt-4 h6 text-muted text-uppercase font-weight-bold">Notes</h6>
								<div v-if="entity.notes">
									{{ entity.notes }}
								</div>
								<div v-else class="text-muted">&mdash;</div>
							</b-col>
						</b-row>
					</b-tab>
					<b-tab no-body active lazy>
						<template #title>Appeals</template>
						<appeal-index
							:filters="appealFilters"
							@clicked="viewAppeal"
							empty-description="No appeals found under this reviewer or matching your search."
						/>
					</b-tab>
				</b-tabs>
			</b-card>
		</div>
	</div>
</template>

<script>
import { get as GetEntity, destroy as DeleteEntity } from "@/clients/services/auditReviewers";
import AppealIndex from "@/clients/components/Appeals/Index.vue";

export default {
	name: "ViewAuditReviewer",
	components: {
		AppealIndex,
	},
	data() {
		return {
			id: this.$route.params.id,
			loading: true,
			deleting: false,
			entity: {
				id: null,
				agency_id: null,
				first_name: "",
				middle_name: "",
				last_name: "",
				title: "",
				phone: null,
				email: null,
				professional_degree: null,
				notes: null,
				active: true,
				agency: {
					id: null,
					name: null,
				},
			},
		};
	},
	computed: {
		appealFilters() {
			return {
				audit_reviewer_id: this.$route.params.id,
			};
		},
		showLoading() {
			return this.loading && (!this.entity.id || this.entity.id == null);
		},
		favorableAppealsPercent() {
			return 0;
		},
		totalAppeals() {
			return 0;
		},
	},
	mounted() {
		this.refresh();
	},
	methods: {
		async refresh() {
			try {
				this.loading = true;
				const response = await GetEntity(this.id);
				this.entity = response;
			} catch (e) {
				this.$router.push({ name: "auditReviewers" });
			} finally {
				this.loading = false;
			}
		},
		async destroy() {
			if (!confirm("Are you sure you want to delete this audit reviewer?")) {
				return;
			}

			try {
				this.deleting = true;
				const response = await DeleteEntity(this.id);
				this.$router.push({ name: "auditReviewers" });

				this.$nextTick(function () {
					this.$store.dispatch("notify", {
						variant: "primary",
						title: "Audit Reviewer Deleted",
						message: `Audit reviewer ${response.data.full_name} was deleted.`,
					});
				});
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Delete Failed",
					message: "Failed to delete audit reviewer",
				});
			} finally {
				this.deleting = false;
			}
		},
		viewAppeal(appeal) {
			this.$router.push({
				name: "appeals.view",
				params: {
					id: appeal.case_id,
					appeal_id: appeal.id,
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
