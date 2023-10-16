<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'insuranceProviders' }" v-text="`Insurance Providers /`" />
				<span>{{ entity.name }}</span>
			</template>
			<template #buttons>
				<b-button
					variant="primary"
					:to="{
						name: 'insuranceProviders.edit',
						params: { id: $route.params.id },
					}"
				>
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
				<b-col v-if="entity.full_address" cols="12" class="mb-2">
					<p class="text-muted mb-0" title="Address">
						<font-awesome-icon icon="location-dot" fixed-width />
						{{ entity.full_address }}
					</p>
				</b-col>
				<b-col cols="12" sm="6" lg="6" class="mb-4">
					<b-badge pill variant="secondary" v-if="entity.active == false">Inactive</b-badge>
					<b-badge pill variant="info" v-if="entity.default_insurance_type" title="Default Type">
						{{ entity.default_insurance_type.name }}
					</b-badge>
				</b-col>
				<b-col cols="12" sm="6" lg="6" class="mb-4 text-sm-right">
					<p class="small text-muted mb-0">
						<span> Last updated {{ $filters.fromNow(entity.modified) }} </span>
						<span v-if="entity.modified_by_user?.full_name">
							by {{ entity.modified_by_user.full_name }}
						</span>
					</p>
				</b-col>
			</b-row>

			<b-card no-body class="shadow-sm mb-4">
				<b-tabs card active-nav-item-class="font-weight-bold">
					<b-tab lazy title="Details">
						<b-row>
							<b-col cols="12" md="6" class="mb-4">
								<b-card no-body class="shadow-sm">
									<b-card-header class="font-weight-bold text-uppercase text-muted">
										Appeal Levels
									</b-card-header>
									<b-list-group flush v-if="entity.appeal_levels && entity.appeal_levels.length > 0">
										<b-list-group-item
											v-for="appealLevel in entity.appeal_levels"
											:key="appealLevel.id"
											class="py-3"
										>
											<h6 class="h6 font-weight-bold mb-0">
												{{ appealLevel._joinData?.label || appealLevel.name }}
											</h6>
											<p class="mb-0">{{ appealLevel.description }}</p>
											<p
												v-if="appealLevel._joinData.days_to_respond"
												class="small text-muted mb-0"
											>
												{{ appealLevel._joinData.days_to_respond }} days to respond
											</p>
											<div v-if="appealLevel.agencies?.length > 0">
												<p v-for="agency in appealLevel.agencies" :key="agency.id" class="mb-0">
													<router-link
														:to="{ name: 'agencies.view', params: { id: agency.id } }"
													>
														<span v-if="agency.name">
															{{ agency.name }}
														</span>
														<span v-else> Agency Name Missing </span>
													</router-link>
												</p>
											</div>
										</b-list-group-item>
									</b-list-group>
									<b-card-body v-else>
										<b-alert show variant="light" class="mb-0">
											No appeal levels assigned.
										</b-alert>
									</b-card-body>
								</b-card>
							</b-col>
							<b-col cols="12" md="6" class="mb-4">
								<b-card no-body class="shadow-sm">
									<b-card-header class="font-weight-bold text-uppercase text-muted">
										Types
									</b-card-header>
									<b-list-group
										flush
										v-if="entity.insurance_types && entity.insurance_types.length > 0"
									>
										<b-list-group-item
											v-for="insuranceType in entity.insurance_types"
											:key="insuranceType.id"
											class="py-2"
										>
											{{ insuranceType.name }}
										</b-list-group-item>
									</b-list-group>
									<b-card-body v-else>
										<b-alert show variant="light" class="mb-0">
											No insurance types assigned.
										</b-alert>
									</b-card-body>
								</b-card>
							</b-col>
						</b-row>
					</b-tab>
					<b-tab no-body lazy active title="Cases">
						<case-index
							ref="caseList"
							hide-insurance-provider
							:filters="caseFilters"
							@clicked="viewCase"
							empty-description="No cases found under this insurance provider or matching your search."
						/>
					</b-tab>
				</b-tabs>
			</b-card>
		</div>
	</div>
</template>

<script type="text/javascript">
import { get as GetEntity, destroy as DeleteEntity } from "@/clients/services/insuranceProviders";
import CaseIndex from "@/clients/components/Cases/Index.vue";

export default {
	name: "ViewInsuranceProvider",
	components: {
		CaseIndex,
	},
	data() {
		return {
			loading: true,
			entity: {
				id: null,
				name: null,
				default_insurance_type: null,
				appeal_levels: [],
				insurance_types: [],
			},
		};
	},
	computed: {
		caseFilters() {
			return {
				insurance_provider_id: this.$route.params.id,
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
		viewCase(caseEntity) {
			this.$router.push({
				name: "cases.view",
				params: {
					id: caseEntity.id,
				},
			});
		},
		async refresh() {
			try {
				this.loading = true;
				this.entity = await GetEntity(this.$route.params.id);
			} catch (e) {
				this.$router.push({
					name: "insuranceProviders",
				});

				this.$nextTick(function () {
					this.$store.dispatch("notify", {
						variant: "danger",
						title: "Insurance Provider Not Found",
						message: `Insurance provider details could not be found.`,
					});
				});
			} finally {
				this.loading = false;
			}
		},
		async destroy() {
			if (!confirm("Are you sure you want to delete this insurance provider?")) {
				return;
			}

			try {
				this.deleting = true;
				const response = await DeleteEntity(this.$route.params.id);
				this.$store.dispatch("insuranceProviders/getActive");
				this.$router.push({ name: "insuranceProviders" });

				this.$nextTick(function () {
					this.$store.dispatch("notify", {
						variant: "primary",
						title: "Insurance Provider Deleted",
						message: `Insurance provider ${response.data.name} was deleted.`,
					});
				});
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Delete Failed",
					message: "Error deleting insurance provider.",
				});
			} finally {
				this.deleting = false;
			}
		},
		viewProvider(provider) {
			this.$router.push({
				name: "insuranceProviders.view",
				params: {
					id: provider.id,
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
