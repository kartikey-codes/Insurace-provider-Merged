<template>
	<div>
		<page-header>
			<template #title>
				<span>{{ clientName ?? "Dashboard" }}</span>
				<b-button
					variant="light"
					:to="{ name: 'organization' }"
					title="Organization Settings"
					class="ml-2 text-center"
				>
					<font-awesome-icon icon="cog" class="text-muted px-0 mx-0" />
				</b-button>
			</template>
			<template #buttons>
				<b-button :to="{ name: 'cases.add' }" variant="primary" title="New Case">
					<font-awesome-icon icon="plus" fixed-width />
					<span>New Case</span>
				</b-button>
			</template>
		</page-header>

		<b-container fluid class="my-4">
			<b-row>
				<b-col cols="12" md="6" lg="7" xl="7" class="mb-4">
					<b-alert fade variant="warning" :show="incomingDocumentCount > 0" class="mb-4">
						<b-row class="d-flex align-items-center">
							<b-col cols="12" lg="9" class="text-lg-left">
								<p class="mb-lg-0">
									<font-awesome-icon icon="exclamation-triangle" fixed-width />
									<span v-if="incomingDocumentCount == 1">
										There is {{ incomingDocumentCount }} incoming document awaiting processing.
									</span>
									<span v-else>
										There are {{ incomingDocumentCount }} incoming documents awaiting processing.
									</span>
								</p>
							</b-col>
							<b-col cols="12" lg="3" class="text-lg-right">
								<b-button
									variant="light"
									:to="{ name: 'incomingDocuments' }"
									class="d-block d-lg-inline-block"
									title="View Incoming Documents"
								>
									<span>View</span>
									<font-awesome-icon icon="chevron-right" />
								</b-button>
							</b-col>
						</b-row>
					</b-alert>

					<b-card no-body class="mb-4 shadow-sm">
						<b-card-header header-tag="header" role="tab" class="p-0 border-b-0">
							<b-button
								block
								@click="collapseCalendar = !collapseCalendar"
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0 rounded-b-0"
							>
								<font-awesome-icon
									class="ml-0"
									:icon="collapseCalendar ? 'chevron-down' : 'chevron-right'"
									fixed-width
								/>
								Calendar
							</b-button>
						</b-card-header>
						<b-collapse v-model="collapseCalendar" role="tabpanel">
							<b-row>
								<b-col cols="12">
									<Calendar />
								</b-col>
							</b-row>
						</b-collapse>
					</b-card>

					<b-card no-body class="mb-4 shadow-sm">
						<b-card-header header-tag="header" role="tab" class="p-0 border-b-0">
							<b-button
								block
								@click="collapseNotes = !collapseNotes"
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0 rounded-b-0"
							>
								<font-awesome-icon
									class="ml-0"
									:icon="collapseNotes ? 'chevron-down' : 'chevron-right'"
									fixed-width
								/>
								Recent Notes
							</b-button>
						</b-card-header>
						<b-collapse v-model="collapseNotes" role="tabpanel">
							<b-row>
								<b-col cols="12">
									<RecentNotes />
								</b-col>
							</b-row>
						</b-collapse>
					</b-card>

					<b-card no-body class="mb-4 shadow-sm">
						<paginated-results
							v-slot="{
								empty,
								hasMultiplePages,
								hasNextPage,
								hasPrevPage,
								loading,
								nextPage,
								prevPage,
								page,
								pages,
								results,
								total,
							}"
							v-bind="{
								action: getAppealQueue,
								perPage: queuePerPage,
							}"
						>
							<b-card-header
								header-tag="header"
								header-bg-variant="primary"
								role="tab"
								class="p-0 border-b-0"
							>
								<b-button
									block
									@click="collapseMyAppeals = !collapseMyAppeals"
									variant="primary"
									role="tab"
									class="text-left px-4 py-3 m-0 font-weight-bold"
								>
									<font-awesome-icon
										class="ml-0"
										:icon="collapseMyAppeals ? 'chevron-down' : 'chevron-right'"
										fixed-width
									/>

									<span>My Appeals</span>
									<b-badge v-if="total > 0" pill variant="danger" class="ml-3">
										{{ total }}
									</b-badge>
								</b-button>
							</b-card-header>
							<b-collapse v-model="collapseMyAppeals" role="tabpanel">
								<b-card-header v-if="!empty && hasMultiplePages" class="text-right">
									<b-row class="d-flex justify-content-between align-items-center">
										<b-col cols="4" class="text-left">
											<p class="font-weight-bold small text-muted mb-0">
												Page {{ page }} / {{ pages }}
											</p>
										</b-col>
										<b-col cols="8" class="text-right">
											<simple-pagination
												v-bind="{ loading, prevPage, hasPrevPage, nextPage, hasNextPage }"
											/>
										</b-col>
									</b-row>
								</b-card-header>

								<loading-indicator v-if="loading && empty" class="my-5" />
								<b-list-group flush v-else-if="!empty">
									<b-list-group-item
										v-for="result in results"
										:key="result.id"
										:to="{
											name: 'appeals.view',
											params: {
												id: result.case_id,
												appeal_id: result.id,
											},
										}"
										class="py-3"
									>
										<div class="d-flex align-items-start">
											<appeal-status-label avatar :value="result" />
											<b-container fluid>
												<b-row>
													<b-col cols="6">
														<h5 class="h6 text-dark mb-0">
															<span v-if="result?.case?.patient">
																{{ result.case.patient.full_name }}
															</span>
															<span v-else class="text-danger">
																Missing Patient Name
															</span>
														</h5>
														<p class="mb-0">
															<span v-if="result?.appeal_level?.name">
																{{ result.appeal_level.name }}
															</span>
															<span v-else class="text-danger"> Missing Level </span>

															<appeal-status-label pill :value="result" />
														</p>
														<p
															v-if="result.case?.insurance_provider?.name"
															class="small text-muted mb-0"
														>
															<span>{{ result.case.insurance_provider.name }}</span>
														</p>
													</b-col>
													<b-col cols="6" class="text-right">
														<p
															v-if="result.priority"
															class="mb-0 font-weight-bold text-primary"
														>
															Priority
														</p>
														<p
															v-if="result.due_date"
															class="small mb-0"
															:class="
																result.is_overdue
																	? 'text-danger font-weight-bold'
																	: 'text-muted'
															"
														>
															Response due {{ $filters.fromNow(result.due_date) }}
														</p>
													</b-col>
												</b-row>
											</b-container>
										</div>
									</b-list-group-item>
								</b-list-group>
								<empty-result v-else-if="empty" icon="check-circle">
									No appeals assigned
									<template #content>
										<p class="mb-0">No open appeals are assigned to you at this time.</p>
										<p class="mb-0">You're all caught up!</p>
									</template>
								</empty-result>
							</b-collapse>
						</paginated-results>
					</b-card>

					<b-card no-body class="mb-4">
						<paginated-results
							v-slot="{
								empty,
								hasMultiplePages,
								hasNextPage,
								hasPrevPage,
								loading,
								nextPage,
								prevPage,
								page,
								pages,
								results,
								total,
							}"
							v-bind="{
								action: getRequestQueue,
								perPage: queuePerPage,
							}"
						>
							<b-card-header
								header-tag="header"
								header-bg-variant="primary"
								role="tab"
								class="p-0 border-b-0"
							>
								<b-button
									block
									@click="collapseMyRequests = !collapseMyRequests"
									variant="primary"
									role="tab"
									class="text-left px-4 py-3 m-0 font-weight-bold"
								>
									<font-awesome-icon
										class="ml-0"
										:icon="collapseMyRequests ? 'chevron-down' : 'chevron-right'"
										fixed-width
									/>
									<span>My Requests</span>
									<b-badge v-if="total" pill variant="danger" class="ml-3">
										{{ total }}
									</b-badge>
								</b-button>
							</b-card-header>
							<b-collapse v-model="collapseMyRequests" role="tabpanel">
								<b-card-header v-if="!empty && hasMultiplePages" class="text-right">
									<b-row class="d-flex justify-content-between align-items-center">
										<b-col cols="4" class="text-left">
											<p class="font-weight-bold small text-muted mb-0">
												Page {{ page }} / {{ pages }}
											</p>
										</b-col>
										<b-col cols="8" class="text-right">
											<simple-pagination
												v-bind="{ loading, prevPage, hasPrevPage, nextPage, hasNextPage }"
											/>
										</b-col>
									</b-row>
								</b-card-header>

								<loading-indicator v-if="loading && empty" class="my-5" />
								<b-list-group flush v-else-if="!empty">
									<b-list-group-item
										v-for="result in results"
										:key="result.id"
										:to="{
											name: 'caseRequests.view',
											params: {
												id: result.case_id,
												case_request_id: result.id,
											},
										}"
										class="py-3"
									>
										<b-row>
											<b-col cols="6">
												<h5 class="h6 text-dark mb-0">
													{{ result.name }}
												</h5>
											</b-col>
											<b-col cols="6" class="text-right">
												<p
													class="small mb-0"
													:class="
														result.is_overdue
															? 'font-weight-bold text-danger'
															: 'text-muted'
													"
												>
													Due {{ $filters.fromNow(result.due_date) }}
												</p>
											</b-col>
										</b-row>
									</b-list-group-item>
								</b-list-group>
								<empty-result v-else-if="empty" icon="check-circle">
									No requests assigned
									<template #content>
										<p class="mb-0">No open requests are assigned to you at this time.</p>
										<p class="mb-0">You're all caught up!</p>
									</template>
								</empty-result>
							</b-collapse>
						</paginated-results>
					</b-card>
				</b-col>
				<b-col cols="12" md="6" lg="5" xl="5" class="mb-4">
					<b-card-group class="shadow-sm mb-4">
						<b-card class="text-center">
							<p class="h4 font-weight-bold mb-1">
								<span v-if="!favorableCasesPercent" class="text-muted">&mdash;</span>
								<span v-else>{{ favorableCasesPercent }}%</span>
							</p>
							<p class="small text-muted mb-0">Favorable Cases</p>
						</b-card>
						<b-card class="text-center">
							<p class="h4 font-weight-bold mb-1">
								<span v-if="!openClaimsTotal" class="text-muted">&mdash;</span>
								<span v-else class="mx-0 px-0">
									{{ $filters.abbreviatedCurrency(openClaimsTotal) }}
								</span>
							</p>
							<p class="small text-muted mb-0">Open Claims</p>
						</b-card>
						<b-card class="text-center">
							<p class="h4 font-weight-bold mb-1">
								<span v-if="!reimbursedTotal" class="text-muted">&mdash;</span>
								<span v-else class="mx-0 px-0">
									{{ $filters.abbreviatedCurrency(reimbursedTotal) }}
								</span>
							</p>
							<p class="small text-muted mb-0">Reimbursed</p>
						</b-card>
					</b-card-group>

					<b-card no-body header-class="d-flex justify-content-between align-items-center mb-4">
						<b-tabs card>
							<b-tab no-body>
								<template #title>
									<span>Overview</span>
								</template>
								<b-card-body>
									<b-row>
										<b-col cols="12" class="mb-4">
											<AppealsLineChart height="300px" />
										</b-col>
									</b-row>
									<b-row>
										<b-col cols="12" xl="6">
											<b-row class="mt-4 mt-xl-4">
												<b-col cols="4" md="4" lg="6" xl="6" class="mb-4 mb-xl-4 text-center">
													<p class="h5 mb-0 font-weight-bold">
														{{ $filters.formatNumber(openCaseCount) }}
													</p>
													<p class="text-muted mb-0">Open Cases</p>
												</b-col>
												<b-col cols="4" md="4" lg="6" xl="6" class="mb-4 mb-xl-4 text-center">
													<p class="h5 mb-0 font-weight-bold">
														{{ $filters.formatNumber(openAppealCount) }}
													</p>
													<p class="text-muted mb-0">Open Appeals</p>
												</b-col>
												<b-col cols="4" md="4" lg="6" xl="6" class="mb-4 mb-xl-4 text-center">
													<p class="h5 mb-0 font-weight-bold">
														{{ $filters.formatNumber(openCaseRequestCount) }}
													</p>
													<p class="text-muted mb-0">Open Requests</p>
												</b-col>
												<b-col cols="4" md="4" lg="6" xl="6" class="mb-4 mb-xl-4 text-center">
													<p class="h5 mb-0 font-weight-bold">
														{{ $filters.formatNumber(assignedDocuments) }}
													</p>
													<p class="text-muted mb-0">Assigned Documents</p>
												</b-col>
												<b-col cols="4" md="4" lg="6" xl="6" class="mb-4 mb-xl-4 text-center">
													<p class="h5 mb-0 font-weight-bold">
														{{ $filters.formatNumber(unassignedAppeals) }}
													</p>
													<p class="text-muted mb-0">Unassigned Appeals</p>
												</b-col>
												<b-col cols="4" md="4" lg="6" xl="6" class="mb-4 mb-xl-4 text-center">
													<p class="h5 mb-0 font-weight-bold">
														{{ $filters.formatNumber(emptyCases) }}
													</p>
													<p class="text-muted mb-0">Empty Cases</p>
												</b-col>
											</b-row>
										</b-col>
										<b-col cols="12" xl="6">
											<CasesPieChart height="300px" />
										</b-col>
									</b-row>
								</b-card-body>
								<b-card-footer class="text-right">
									<b-button :to="{ name: 'reports' }" title="Reports" size="sm">
										View Reports
										<font-awesome-icon icon="chevron-right" />
									</b-button>
								</b-card-footer>
							</b-tab>
							<b-tab no-body>
								<template #title>
									<span>Users Online</span>
								</template>
								<b-list-group flush v-if="usersOnline.length > 0">
									<b-list-group-item
										v-for="user in usersOnline"
										:key="user.id"
										:to="{ name: 'users.view', params: { id: user.id } }"
										class="d-flex justify-content-between align-items-center py-3"
										title="View User"
									>
										<div class="d-flex justify-start align-items-center">
											<b-avatar
												rounded
												:variant="currentUserId == user.id ? 'primary' : 'light'"
												class="mr-3 px-0 text-center"
											>
												<font-awesome-icon icon="user" class="px-0 mx-0" />
											</b-avatar>
											<div>
												<h6 class="h6 font-weight-bold mb-0">
													{{ user.full_name }}
												</h6>
												<p class="small text-muted mb-0">
													{{ user.email }}
												</p>
											</div>
										</div>
										<font-awesome-icon icon="chevron-right" fixed-width />
									</b-list-group-item>
								</b-list-group>
								<empty-result v-else>
									No users online
									<template #content>
										No other users from your organization are active right now.
									</template>
								</empty-result>
							</b-tab>
							<b-tab no-body lazy>
								<template #title>
									<span>Search Patients</span>
								</template>
								<PatientSearchInput autofocus v-model="patient" class="mx-3 my-3" />
								<b-card-body v-if="patient && patient.list_name" class="pt-0">
									<dl class="mb-0">
										<div class="row" v-if="patient.date_of_birth">
											<dt class="col-5 text-muted h6 small">DOB</dt>
											<dd class="col-7">
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
											</dd>
										</div>

										<div class="row" v-if="patient.sex">
											<dt class="col-5 text-muted h6 small">Gender</dt>
											<dd class="col-7">
												<span v-if="patient.sex">
													{{ patient.sex }}
												</span>
											</dd>
										</div>
										<div class="row" v-if="patient.marital_status">
											<dt class="col-5 text-muted h6 small">Marital Status</dt>
											<dd class="col-7">
												<span v-if="patient.marital_status">
													{{ patient.marital_status }}
												</span>
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
										<div v-if="patient.modified" class="row">
											<dt class="col-5 text-muted h6 small">Last Updated</dt>
											<dd class="col-7">{{ $filters.fromNow(patient.modified) }}</dd>
										</div>
									</dl>
								</b-card-body>
								<div v-if="patient && patient.id">
									<h6 class="h6 text-muted text-uppercase font-weight-bold mx-2">Cases</h6>
									<case-index
										ref="caseList"
										:filters="{ patient_id: patient.id }"
										@clicked="viewCase"
										hide-patient
										empty-description="No cases have been created for this patient."
									/>
								</div>
								<b-card-footer v-if="patient && patient.list_name">
									<div class="d-flex justify-content-between align-items-center">
										<b-button
											variant="secondary"
											:to="{ name: 'patients.edit', params: { id: patient.id } }"
										>
											<font-awesome-icon icon="edit" fixed-width />
											Edit
										</b-button>

										<b-button
											variant="primary"
											:to="{ name: 'patients.view', params: { id: patient.id } }"
										>
											View Patient
											<font-awesome-icon icon="chevron-right" />
										</b-button>
									</div>
								</b-card-footer>
							</b-tab>
							<b-tab no-body>
								<template #title>
									<span>User Queues</span>
								</template>
								<loading-indicator v-if="loadingAppealsOpenByAssignedUser" class="my-5" />
								<b-list-group v-else flush>
									<b-list-group-item
										v-for="user in appealsOpenByAssignedUser"
										:key="user.id"
										:to="{ name: 'users.view', params: { id: user.id } }"
										class="d-flex justify-content-between align-items-center"
										:variant="user.count && user.count > 0 ? '' : 'light'"
									>
										<p class="mb-0" :class="user.count && user.count > 0 ? 'font-weight-bold' : ''">
											{{ user.full_name }}
										</p>
										<b-badge pill :variant="user.count && user.count > 0 ? 'primary' : 'light'">
											{{ user.count }}
										</b-badge>
									</b-list-group-item>
								</b-list-group>
							</b-tab>
						</b-tabs>
					</b-card>
				</b-col>
			</b-row>
		</b-container>
	</div>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

import { getAppealQueue, getCaseRequestQueue } from "@/clients/services/queue";
import { getIndex as GetCases } from "@/clients/services/cases";

import AppealsLineChart from "@/clients/components/charts/AppealsLine.vue";
import CasesPieChart from "@/clients/components/charts/CasesPie.vue";

import AppealStatusLabel from "@/clients/components/Appeals/StatusLabel.vue";
import PatientSearchInput from "@/clients/components/Patients/SearchInput.vue";

import Calendar from "@/clients/components/Dashboard/Calendar.vue";
import RecentNotes from "@/clients/components/Dashboard/RecentNotes.vue";

import CaseIndex from "@/clients/components/Cases/Index.vue";

export default {
	name: "Dashboard",
	components: {
		AppealsLineChart,
		CasesPieChart,
		AppealStatusLabel,
		PatientSearchInput,
		Calendar,
		RecentNotes,
		CaseIndex,
	},
	data() {
		return {
			timer: null,
			totalOpenCases: null,
			totalMyAppeals: null,
			totalMyRequests: null,

			queuePerPage: 6,

			patient: {
				id: null,
				list_name: null,
			},
		};
	},
	computed: {
		collapseCalendar: {
			get() {
				return this.showCalendar;
			},
			set(val) {
				this.$store.commit("dashboard/showCalendar", val);
			},
		},
		collapseNotes: {
			get() {
				return this.showNotes;
			},
			set(val) {
				this.$store.commit("dashboard/showNotes", val);
			},
		},
		collapseMyAppeals: {
			get() {
				return this.showMyAppeals;
			},
			set(val) {
				this.$store.commit("dashboard/showMyAppeals", val);
			},
		},
		collapseMyRequests: {
			get() {
				return this.showMyRequests;
			},
			set(val) {
				this.$store.commit("dashboard/showMyRequests", val);
			},
		},
		...mapGetters({
			// Global
			clientName: "clientName",

			// Dashboard
			loading: "dashboard/loading",
			assignedAppeals: "dashboard/assignedAppeals",
			assignedCases: "dashboard/assignedCases",
			assignedDocuments: "dashboard/assignedDocuments",
			returnedAppeals: "dashboard/returnedAppeals",
			completedAppeals: "dashboard/completedAppeals",
			showCalendar: "dashboard/showCalendar",
			showNotes: "dashboard/showNotes",
			showMyAppeals: "dashboard/showMyAppeals",
			showMyRequests: "dashboard/showMyRequests",

			// Appeals
			loadingAppealsOpenByAssignedUser: "appeals/loadingOpenByAssignedUser",
			appealsOpenByAssignedUser: "appeals/openByAssignedUser",

			// Statistics
			unassignedAppeals: "statistics/unassignedAppeals",
			emptyCases: "statistics/emptyCases",
			favorableCasesPercent: "statistics/favorableCasesPercent",
			openClaimsTotal: "statistics/openClaimsTotal",
			reimbursedTotal: "statistics/reimbursedTotal",

			// State
			openCaseCount: "openCaseCount",
			openAppealCount: "openAppealCount",
			openCaseRequestCount: "openCaseRequestCount",
			incomingDocumentCount: "incomingDocuments/count",

			// User
			currentUserId: "userId",
			user: "user",
			usersOnline: "users/online",
		}),
	},
	created() {
		// Reload every minute
		this.timer = setInterval(this.refresh, 60000);
	},
	mounted() {
		this.refresh();
	},
	methods: {
		async refresh() {
			await this.$store.dispatch("dashboard/refresh");
			await this.$store.dispatch("statistics/refresh");
			await this.$store.dispatch("appeals/openByAssignedUser");
		},
		async getOpenCases(params) {
			return await GetCases({
				status: "Open",
				...params,
			});
		},
		async getAppealQueue(params) {
			return await getAppealQueue(params);
		},
		async getRequestQueue(params) {
			return await getCaseRequestQueue(params);
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
	destroyed() {
		clearInterval(this.timer);
	},
};
</script>
