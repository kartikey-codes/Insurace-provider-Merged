<template>
	<b-card no-body>
		<b-tabs v-model="tabIndex" card active-nav-item-class="font-weight-bold">
			<b-tab title="New Case" no-body>
				<case-form
					flush
					hide-cancel
					hide-patient
					:patient-id="patient.id"
					:current-document="document"
					@saved="addedCase"
				/>
			</b-tab>
			<b-tab title="Open Cases" no-body lazy>
				<paginated-results
					v-slot="{
						empty,
						hasMultiplePages,
						hasNextPage,
						hasPrevPage,
						loading,
						nextPage,
						prevPage,
						results,
						total,
					}"
					v-bind="{
						action: GetCases,
						filters: {
							status: 'Open',
							patient_id: patient.id,
						},
						perPage: casesPerPage,
						search: '',
						sort: casesSort,
						sortDescending: casesDescending,
					}"
				>
					<loading-indicator v-if="loading && empty" size="4x" class="my-5" />
					<div v-else-if="!empty">
						<b-card-header v-if="hasMultiplePages">
							<b-row class="d-flex justify-content-between align-items-center">
								<b-col cols="6">
									<p class="font-weight-bold mb-0 text-muted">{{ total }} Open Cases</p>
								</b-col>
								<b-col cols="6" class="text-right">
									<simple-pagination
										v-bind="{ loading, nextPage, prevPage, hasPrevPage, hasNextPage }"
									/>
								</b-col>
							</b-row>
						</b-card-header>
						<b-list-group flush>
							<attach-case-list-item
								v-for="caseEntity in results"
								:key="caseEntity.id"
								:case-entity="caseEntity"
								:patient="patient"
								:document="document"
								@attached="attachedDocument"
							/>
						</b-list-group>
					</div>
					<empty-result v-else>
						No open cases
						<template #content> No open cases were found for this patient. </template>
					</empty-result>
				</paginated-results>
			</b-tab>
			<b-tab title="UTC Cases" no-body lazy>
				<paginated-results
					v-slot="{
						empty,
						hasMultiplePages,
						hasNextPage,
						hasPrevPage,
						loading,
						nextPage,
						prevPage,
						results,
						total,
					}"
					v-bind="{
						action: GetCases,
						filters: {
							status: 'UTC',
							patient_id: patient.id,
						},
						perPage: casesPerPage,
						search: '',
						sort: casesSort,
						sortDescending: casesDescending,
					}"
				>
					<loading-indicator v-if="loading && empty" size="4x" class="my-5" />
					<div v-else-if="!empty">
						<b-card-header v-if="hasMultiplePages">
							<b-row class="d-flex justify-content-between align-items-center">
								<b-col cols="6">
									<p class="font-weight-bold mb-0 text-muted">{{ total }} UTC Cases</p>
								</b-col>
								<b-col cols="6" class="text-right">
									<simple-pagination
										v-bind="{ loading, nextPage, prevPage, hasPrevPage, hasNextPage }"
									/>
								</b-col>
							</b-row>
						</b-card-header>
						<b-list-group flush>
							<attach-case-list-item
								v-for="caseEntity in results"
								:key="caseEntity.id"
								:case-entity="caseEntity"
								:patient="patient"
								:document="document"
								@attached="attachedDocument"
							/>
						</b-list-group>
					</div>
					<empty-result v-else>
						No UTC cases
						<template #content> No unable-to-complete cases found for this patient. </template>
					</empty-result>
				</paginated-results>
			</b-tab>
			<b-tab title="Closed Cases" no-body lazy>
				<paginated-results
					v-slot="{
						empty,
						hasMultiplePages,
						hasNextPage,
						hasPrevPage,
						loading,
						nextPage,
						prevPage,
						results,
						total,
					}"
					v-bind="{
						action: GetCases,
						filters: {
							status: 'Closed',
							patient_id: patient.id,
						},
						perPage: casesPerPage,
						search: '',
						sort: casesSort,
						sortDescending: casesDescending,
					}"
				>
					<loading-indicator v-if="loading && empty" size="4x" class="my-5" />
					<div v-else-if="!empty">
						<b-card-header v-if="hasMultiplePages">
							<b-row class="d-flex justify-content-between align-items-center">
								<b-col cols="6">
									<p class="font-weight-bold mb-0 text-muted">{{ total }} Closed Cases</p>
								</b-col>
								<b-col cols="6" class="text-right">
									<simple-pagination
										v-bind="{ loading, nextPage, prevPage, hasPrevPage, hasNextPage }"
									/>
								</b-col>
							</b-row>
						</b-card-header>
						<b-list-group flush>
							<attach-case-list-item
								v-for="caseEntity in results"
								:key="caseEntity.id"
								:case-entity="caseEntity"
								:patient="patient"
								:document="document"
								@attached="attachedDocument"
							/>
						</b-list-group>
					</div>
					<empty-result v-else>
						No closed cases
						<template #content> No closed cases found for this patient. </template>
					</empty-result>
				</paginated-results>
			</b-tab>
		</b-tabs>
	</b-card>
</template>

<script>
import { getIndex as GetCases } from "@/clients/services/cases";
import CaseForm from "@/clients/components/Cases/Form.vue";
import AttachCaseListItem from "@/clients/components/IncomingDocuments/AttachCaseListItem.vue";

export default {
	name: "IncomingDocumentAttach",
	components: {
		CaseForm,
		AttachCaseListItem,
	},
	props: {
		patient: {
			required: true,
			type: Object,
			default: () => {
				return {
					id: null,
				};
			},
		},
		document: {
			required: true,
			type: Object,
			default: () => {
				return {
					id: null,
				};
			},
		},
		casesPerPage: {
			type: Number,
			default: 4,
		},
		casesSort: {
			type: String,
			default: "created",
		},
		casesDescending: {
			type: Boolean,
			default: true,
		},
	},
	data() {
		return {
			tabIndex: 1,
		};
	},
	methods: {
		GetCases,
		addedCase(caseEntity) {
			this.$emit("added-case", caseEntity);
			const isUtc = caseEntity.unable_to_complete ? true : false;

			console.log("New Case", {
				id: caseEntity.id,
				patient_id: caseEntity.patient_id,
				patient_name: caseEntity.patient?.full_name ?? "(MISSING)",
				unable_to_complete: isUtc,
				tabIndex: this.tabIndex,
			});

			// "New Case"
			if (this.tabIndex == 0) {
				/**
				 * @todo Fix this. Not working for some reason.
				 */

				if (isUtc == false) {
					this.tabIndex = 1; // Open
				} else {
					this.tabIndex = 2; // UTC
				}
			}

			this.$store.dispatch("notify", {
				variant: "primary",
				title: "Case Created",
				message: "New case created. Please create an Appeal under the case in order to attach documents.",
			});
		},
		attachedDocument(document, type = null) {
			this.$emit("attached", document);

			switch (type) {
				case "Case":
					this.$emit("attached-case", document);
					break;
				case "Appeal":
					this.$emit("attached-appeal", document);
					break;
				case "Request":
					this.$emit("attached-request", document);
					break;
			}
		},
	},
};
</script>