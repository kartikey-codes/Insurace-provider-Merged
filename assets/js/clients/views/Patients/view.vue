<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'patients' }" v-text="`Patients /`" />
				<span>View</span>
			</template>
			<template #buttons>
				<b-button
					variant="primary"
					:to="{ name: 'patients.edit', params: { id: $route.params.id } }"
					:disabled="deleting"
					title="Edit patient"
				>
					<font-awesome-icon icon="edit" fixed-width />
					<span>Edit</span>
				</b-button>

				<b-dropdown variant="secondary" right title="More Options">
					<template #button-content>
						<font-awesome-icon icon="cog" fixed-width />
					</template>

					<b-dropdown-item
						:to="{ name: 'cases.add', query: { patient_id: patient.id } }"
						:disabled="deleting"
					>
						<font-awesome-icon icon="plus" fixed-width />
						<span>New Case</span>
					</b-dropdown-item>

					<b-dropdown-divider />

					<b-dropdown-item @click="destroy" :disabled="loading || deleting" variant="danger">
						<font-awesome-icon icon="trash" fixed-width />
						<span>Delete</span>
					</b-dropdown-item>
				</b-dropdown>
			</template>
		</page-header>

		<loading-indicator v-if="showLoading" class="my-5" />
		<b-container fluid class="my-2" v-else>
			<b-row>
				<b-col cols="12" lg="4" xl="3" class="mb-4">
					<div class="rounded bg-white p-4 shadow-sm mb-2">
						<h2 class="h3 my-0 mb-1 font-weight-bold text-uppercase" v-text="patient.list_name" />
						<h3 v-if="patient.date_of_birth" class="h6 mt-0 mb-1 text-muted text-uppercase">
							<font-awesome-icon
								icon="birthday-cake"
								:class="patient.is_birthday ? 'text-primary' : 'text-muted'"
								:title="patient.is_birthday ? 'Happy Birthday' : 'Date of Birth'"
							/>

							<span class="font-weight-bold">
								{{ $filters.formatDate(patient.date_of_birth) }}
							</span>
							<span v-if="patient.age != null && patient.age != undefined">
								&mdash;
								<span class="font-weight-bold" v-text="patient.age" />
							</span>
						</h3>
						<h3 v-else class="h6 mt-0 mb-1 text-warning">(Missing DOB)</h3>

						<div v-if="patient.modified" class="small text-muted mt-2 mb-2">
							<span v-if="patient.modified" :title="$filters.formatTimestamp(patient.modified)"
								>Last updated {{ $filters.fromNow(patient.modified) }}</span
							>
							<span v-if="patient.modified_by_user && patient.modified_by_user.full_name"
								>by {{ patient.modified_by_user.full_name }}</span
							>
						</div>
					</div>

					<b-row>
						<b-col cols="12" class="mb-2">
							<h5 class="h6 my-2 font-weight-bold text-muted text-uppercase">Details</h5>
							<b-card no-body class="shadow-sm">
								<b-card-body class="mb-0">
									<dl class="mb-0">
										<div class="row">
											<dt class="col-5 text-muted h6 small">Gender</dt>
											<dd class="col-7">
												<span v-if="patient.sex">
													{{ patient.sex }}
												</span>
												<span v-else class="text-muted"> &mdash; </span>
											</dd>
										</div>
										<div class="row">
											<dt class="col-5 text-muted h6 small">Marital Status</dt>
											<dd class="col-7">
												<span v-if="patient.marital_status">
													{{ patient.marital_status }}
												</span>
												<span v-else class="text-muted"> &mdash; </span>
											</dd>
										</div>
										<div v-if="patient.phone" class="row">
											<dt class="col-5 text-muted h6 small">Phone</dt>
											<dd class="col-7">
												{{ $filters.formatPhone(patient.phone) }}
											</dd>
										</div>
										<div v-if="patient.fax" class="row">
											<dt class="col-5 text-muted h6 small">Fax</dt>
											<dd class="col-7">
												{{ $filters.formatPhone(patient.fax) }}
											</dd>
										</div>
										<div v-if="patient.email" class="row">
											<dt class="col-5 text-muted h6 small">Email</dt>
											<dd class="col-7">
												<a :href="'mailto:' + patient.email">{{ patient.email }}</a>
											</dd>
										</div>
										<div v-if="patient.full_address" class="row">
											<dt class="col-5 text-muted h6 small">Address</dt>
											<dd class="col-7">
												<div class="white-space-pre" v-text="patient.full_address" />
											</dd>
										</div>
									</dl>
								</b-card-body>
							</b-card>
						</b-col>
					</b-row>
				</b-col>
				<b-col cols="12" lg="8" xl="9">
					<b-card no-body class="shadow-sm">
						<b-tabs card>
							<b-tab no-body lazy>
								<template #title>Existing Cases</template>
								<case-index
									ref="caseList"
									:filters="caseFilters"
									@clicked="viewCase"
									hide-patient
									empty-description="No cases have been created for this patient."
								/>
							</b-tab>
							<b-tab no-body lazy>
								<template #title>Add Case</template>
								<case-form flush :patient-id="patient.id" hide-patient hide-cancel @saved="addedCase" />
							</b-tab>
							<b-tab>
								<template #title>Similar Patients</template>
								<similar-patients :patient-id="$route.params.id" @saved="afterMerged" />
							</b-tab>
						</b-tabs>
					</b-card>
				</b-col>
			</b-row>
		</b-container>
	</div>
</template>

<script type="text/javascript">
import SimilarPatients from "@/clients/components/Patients/Similar.vue";
import CaseIndex from "@/clients/components/Cases/Index.vue";
import CaseForm from "@/clients/components/Cases/Form.vue";

export default {
	name: "PatientView",
	components: {
		SimilarPatients,
		CaseIndex,
		CaseForm,
	},
	data() {
		return {
			loading: true,
			deleting: false,
			initialLoaded: false,
			patient: {
				id: null,
				full_name: "",
				list_name: "",
			},
			addingCase: false,
			confirmDeleteText: "Are you sure you want to delete this patient?",
		};
	},
	computed: {
		caseFilters() {
			return {
				patient_id: this.$route.params.id,
			};
		},
		showLoading() {
			return this.loading && (!this.patient.id || this.patient.id == null);
		},
	},
	mounted() {
		this.refresh();
	},
	methods: {
		async refresh() {
			try {
				this.loading = true;

				const response = await this.$store.dispatch("patients/get", {
					id: this.$route.params.id,
				});

				this.patient = response;
				this.initialLoaded = true;
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Error getting patient details",
				});

				this.$router.push({
					name: "patients",
				});
			} finally {
				this.loading = false;
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
		addedCase(caseEntity) {
			this.$router.push({
				name: "cases.view",
				params: {
					id: caseEntity.id,
				},
			});
		},
		viewPatient(patient) {
			if (this.$route.params.id == patient.id) {
				return this.refresh();
			}

			this.$router.push({
				name: "patients.view",
				params: {
					id: patient.id,
				},
			});
		},
		afterMerged(data) {
			this.refresh();
		},
		async destroy() {
			if (!confirm(this.confirmDeleteText)) {
				return false;
			}

			try {
				this.deleting = true;

				const response = await this.$store.dispatch("patients/delete", {
					id: this.$route.params.id,
				});

				this.$router.push({ name: "patients" });

				this.$nextTick(function () {
					this.$store.dispatch("notify", {
						variant: "primary",
						title: "Patient Deleted",
						message: `Patient ${response.data.full_name} was deleted.`,
					});
				});
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Delete Failed",
					message: "Failed to delete patient",
				});
			} finally {
				this.deleting = false;
			}
		},
	},
};
</script>
