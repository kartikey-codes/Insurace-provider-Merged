<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'services' }" v-text="`Services /`" />
				<span>{{ entity.name }}</span>
			</template>
			<template #buttons>
				<b-button variant="primary" :to="{ name: 'services.edit', params: { id } }" title="Edit">
					<font-awesome-icon icon="edit" fixed-width />
					<span>Edit</span>
				</b-button>

				<b-dropdown variant="secondary" right title="More Options">
					<template #button-content>
						<font-awesome-icon icon="cog" fixed-width />
					</template>

					<b-dropdown-item @click="destroy" :disabled="loading || deleting" variant="danger">
						<font-awesome-icon icon="trash" fixed-width />
						<span>Delete</span>
					</b-dropdown-item>
				</b-dropdown>
			</template>
		</page-header>

		<loading-indicator v-if="loading" class="my-5" />
		<div v-else class="my-4">
			<b-row>
				<b-col v-if="entity.description" cols="12" class="mb-2">
					<p class="text-muted mb-0">
						{{ entity.description }}
					</p>
				</b-col>
				<b-col cols="12" sm="6" lg="6" class="mb-4">
					<b-badge pill variant="primary" v-if="entity.active">Active</b-badge>
					<b-badge pill variant="secondary" v-else>Inactive</b-badge>
					<b-badge pill variant="info" v-if="entity.client_owned">Owned</b-badge>
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
			<b-row>
				<b-col cols="12">
					<b-card no-body>
						<b-card-header class="font-weight-bold">Assigned Facilities</b-card-header>
						<b-list-group flush v-if="entity.facilities.length > 0">
							<b-list-group-item v-for="facility in entity.facilities" :key="facility.id">
								<div class="py-2 d-flex justify-start align-items-top">
									<b-avatar
										rounded
										:variant="facility.active ? 'primary' : 'light'"
										class="mr-3 px-0 text-center"
									>
										<font-awesome-icon icon="building" class="px-0 mx-0" />
									</b-avatar>
									<b-row class="flex-fill">
										<b-col cols="9" class="text-left">
											<h6 class="h6 font-weight-bold mb-1">
												{{ facility.name }}
											</h6>
											<p v-if="facility.chain_name" class="small mb-1 text-muted" title="Chain">
												<font-awesome-icon icon="link" fixed-width class="d-none d-sm-inline" />
												{{ facility.chain_name }}
											</p>

											<p
												v-if="facility.full_address"
												class="small mb-1 text-muted"
												title="Facility"
											>
												<font-awesome-icon
													icon="location-dot"
													fixed-width
													class="d-none d-sm-inline"
												/>
												{{ facility.full_address }}
											</p>
											<div>
												<b-badge variant="light" v-if="!facility.active"> Inactive </b-badge>
												<b-badge variant="light" v-if="facility.facility_type">
													{{ facility.facility_type.name }}
												</b-badge>
											</div>
										</b-col>
										<b-col cols="3" class="text-right">
											<b-button :to="{ name: 'facilities.view', params: { id: facility.id } }">
												View
											</b-button>
										</b-col>
									</b-row>
								</div>
							</b-list-group-item>
						</b-list-group>
						<empty-result v-else>
							No facilities assigned.
							<template #content> No facilities have been assigned this service. </template>
						</empty-result>
					</b-card>
				</b-col>
			</b-row>
		</div>
	</div>
</template>

<script>
import { get as GetService, destroy as DeleteService } from "@/clients/services/services";

export default {
	name: "ViewService",
	components: {},
	data() {
		return {
			id: this.$route.params.id,
			loading: true,
			deleting: false,
			entity: {
				id: null,
				client_id: null,
				created: null,
				created_by: null,
				modified: null,
				modified_by: null,
				name: "",
				active: null,
				client_owned: null,
				facilities: [],
			},
		};
	},
	mounted() {
		this.refresh();
	},
	methods: {
		async refresh() {
			try {
				this.loading = true;
				this.entity = await GetService(this.id);
			} catch (e) {
				this.$router.push({ name: "services" });
			} finally {
				this.loading = false;
			}
		},
		async destroy() {
			if (!confirm("Are you sure you want to delete this service?")) {
				return;
			}

			try {
				this.deleting = true;
				const response = await DeleteService(this.id);
				this.$router.push({ name: "services" });

				this.$nextTick(function () {
					this.$store.dispatch("notify", {
						variant: "primary",
						title: "Service Deleted",
						message: `Service ${response.data.name} was deleted.`,
					});
				});
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Delete Failed",
					message: "Failed to delete service",
				});
			} finally {
				this.deleting = false;
			}
		},
	},
};
</script>
