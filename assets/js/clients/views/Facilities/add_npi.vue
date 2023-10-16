<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'facilities' }" v-text="`Facilities /`" />
				<span>Add From NPI Registry</span>
			</template>
			<template #buttons>
				<b-button variant="secondary" :to="{ name: 'facilities.add' }" title="Add Facility">
					<font-awesome-icon icon="pencil" fixed-width />
					<span>Manual Create</span>
				</b-button>
			</template>
		</page-header>

		<b-row class="my-4">
			<b-col cols="12" lg="4" xl="3">
				<b-card no-body class="shadow-sm mb-4">
					<b-form @submit.prevent="npiLookup">
						<b-card-body>
							<b-form-group label="State" label-for="state" label-cols="4" label-cols-lg="12">
								<b-form-select
									v-model="query.state"
									name="state"
									:options="states"
									value-field="abbreviation"
									text-field="name"
									:disabled="saving || searching"
									placeholder="Required"
									required
								/>
							</b-form-group>

							<b-form-group label="Name" label-for="name" label-cols="4" label-cols-lg="12">
								<b-input-group>
									<b-form-input
										name="name"
										type="text"
										v-model="query.name"
										:disabled="saving || searching"
										required
										placeholder="Required"
									/>
								</b-input-group>
							</b-form-group>
						</b-card-body>
						<b-card-footer class="text-right">
							<b-button variant="primary" type="submit" :disabled="searching || formInvalid">
								<font-awesome-icon v-if="searching" icon="circle-notch" spin fixed-width />
								<span v-if="searching">Searching...</span>
								<span v-else>Search</span>
							</b-button>
						</b-card-footer>
					</b-form>
				</b-card>
			</b-col>
			<b-col cols="12" lg="8" xl="9">
				<b-card no-body>
					<b-row v-if="!searched">
						<b-col cols="12">
							<empty-result>
								Search NPI
								<template #content>
									Enter a state and organization name to search facilities in the NPI registry.
								</template>
							</empty-result>
						</b-col>
					</b-row>
					<b-row v-else-if="hasError">
						<b-col cols="12">
							<error-alert>
								{{ error }}
								<template #content>
									<p class="mb-0">
										The NPI registry is maintained by CMS and may be experiencing technical
										difficulties.
									</p>
									<p class="mb-0">
										Please try again in a few moments, or report this issue to support if the
										problem persists.
									</p>
								</template>
							</error-alert>
						</b-col>
					</b-row>
					<b-row v-else-if="!isEmpty">
						<b-col cols="12">
							<b-card no-body>
								<b-card-header class="d-flex align-items-center">
									<h6 class="mb-0">{{ results.length }} Results</h6>
								</b-card-header>

								<b-list-group flush>
									<b-list-group-item v-for="npiResult in results" :key="npiResult.number">
										<NPIOrganization
											:value="npiResult"
											v-slot="{
												active,
												contactFullName,
												lastUpdated,
												name,
												number,
												fullPrimaryAddress,
												value,
											}"
										>
											<div class="d-flex justify-content-between align-items-start">
												<div class="d-flex justify-content-start align-items-top">
													<b-avatar
														icon
														:variant="active ? 'primary' : 'light'"
														class="mr-3 mt-2"
													>
														<font-awesome-icon icon="building" fixed-width />
													</b-avatar>
													<div>
														<p class="mb-1 small text-muted" title="NPI Number">
															#{{ number }}
														</p>
														<h6 class="h6 font-weight-bold mb-1">{{ name }}</h6>
														<p class="mb-1" title="Primary Address">
															<font-awesome-icon icon="location-dot" fixed-width />
															<span>{{ fullPrimaryAddress }}</span>
														</p>
														<p
															v-if="contactFullName"
															class="mb-0 small text-muted"
															title="Contact Person"
														>
															<font-awesome-icon icon="user" fixed-width />
															<span>{{ contactFullName }}</span>
														</p>

														<p
															v-if="lastUpdated"
															class="mt-1 mb-0 small text-muted"
															title="Last Updated"
														>
															<span>Last updated {{ lastUpdated }}</span>
														</p>

														<div>
															<b-badge
																v-for="identifier in value.identifiers"
																:key="identifier.code"
																pill
																variant="light"
															>
																{{ identifier.identifier }} - {{ identifier.desc }}
															</b-badge>
														</div>

														<div>
															<b-badge
																v-for="taxonomy in value.taxonomies"
																:key="taxonomy.code"
																pill
																variant="light"
															>
																{{ taxonomy.code }} - {{ taxonomy.desc }}
															</b-badge>
														</div>
													</div>
												</div>
												<b-button @click="selectedNpiResult(value)" variant="primary">
													<font-awesome-icon icon="plus" fixed-width />
													Add
												</b-button>
											</div>
										</NPIOrganization>
									</b-list-group-item>
								</b-list-group>
							</b-card>
						</b-col>
					</b-row>
					<b-row v-else-if="!searching && isEmpty">
						<b-col cols="12">
							<empty-result>
								No Results
								<template #content>
									No organizations were found in the NPI Registry matching what you provided.
								</template>
							</empty-result>
						</b-col>
					</b-row>
					<b-row v-else-if="searching">
						<b-col cols="12">
							<loading-indicator class="my-5" title="Searching registry..." />
						</b-col>
					</b-row>
				</b-card>
			</b-col>
		</b-row>
	</div>
</template>

<script>
import { mapGetters } from "vuex";
import NPIOrganization from "@/clients/components/NPI/NPIOrganization.vue";

export default {
	name: "ViewAddFacilityNPI",
	components: {
		NPIOrganization,
	},
	data() {
		return {
			error: "",
			query: {
				name: "",
				state: "",
			},
			results: [],
			saving: false,
			searching: false,
			searched: false,
		};
	},
	computed: {
		formInvalid() {
			if (this.query.name == "") {
				return true;
			}

			if (this.query.state == "") {
				return true;
			}

			return false;
		},
		hasError() {
			return this.error && this.error !== "";
		},
		isEmpty() {
			return this.results.length <= 0;
		},
		...mapGetters({
			states: "states/states",
			facilityTypes: "facilityTypes/all",
		}),
	},
	methods: {
		async npiLookup() {
			try {
				this.error = "";
				this.searching = true;
				this.searched = true;

				const response = await this.$store.dispatch("facilities/npiLookup", {
					name: this.query.name,
					state: this.query.state,
				});

				this.results = response;
			} catch (e) {
				this.error = e.response?.data?.message ?? "An error occurred";
			} finally {
				this.searching = false;
			}
		},
		selectedNpiResult(result) {
			if (!result) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Invalid NPI Result",
					message: "Sorry, there was an issue copying NPI details. Please enter manually.",
					variant: "warning",
					data: result,
				});
				return;
			}

			if (!confirm(`Add facility '${result.name}'?`)) {
				return;
			}

			// Try to set facility type as 'Other'
			// @todo Make this better
			const facilityTypeId = this.facilityTypes.find((facilityType) => facilityType.name == "Other")?.id ?? 1;

			var entity = {
				active: true,
				name: result.name,
				facility_type_id: facilityTypeId,
				npi_number: result.number ?? "",
				npi_manual: false,
			};

			const locationAddress = result.addresses.find((address) => address.address_purpose == "LOCATION");
			if (locationAddress) {
				// Contact
				entity.phone = locationAddress.telephone_number ?? "";
				entity.fax = locationAddress.fax_number ?? "";
				// Address
				entity.street_address_1 = locationAddress.address_1 ?? "";
				entity.street_address_2 = locationAddress.address_2 ?? "";
				entity.city = locationAddress.city ?? "";
				entity.state = locationAddress.state ?? "";
				entity.zip = locationAddress.postal_code ?? "";
			} else {
				console.warn("Unable to parse location address", locationAddress);
			}

			const primaryTaxonomy = result.taxonomies.find((taxonomy) => taxonomy.primary == true);
			if (primaryTaxonomy) {
				entity.primary_taxonomy = primaryTaxonomy.code ?? "";
			} else {
				console.warn("Unable to parse primary taxonomy", primaryTaxonomy);
			}

			this.createFromResult(entity);
			this.reset();
		},
		async createFromResult(result) {
			try {
				this.saving = true;

				const newEntity = await this.$store.dispatch("facilities/create", result);

				this.$router.push({
					name: "facilities.view",
					params: {
						id: newEntity.id,
					},
				});

				this.$nextTick(function () {
					this.$store.dispatch("notify", {
						title: "Facility Created",
						message: "Facility created from NPI Registry successfully.",
						variant: "primary",
					});
				});
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Unable to create facility",
					message: "This facility may have already been created or the name may have been previously used.",
					variant: "warning",
					data: result,
				});
			} finally {
				this.saving = false;
			}
		},
		reset() {
			this.error = "";
			this.results = [];
			this.searched = false;
		},
	},
};
</script>
