<template>
	<b-list-group-item >
		
		<b-row>
			<b-col cols="5" md="12" lg="6" class="mb-4 mb-md-0 text-left">
				<b-row>
					<b-col cols="12">
						<case-status-label :value="caseEntity" />

						<span class="font-weight-bold">
							<span v-if="caseEntity.admit_date">
								{{ $filters.formatDate(caseEntity.admit_date) }}
							</span>
							<span v-if="caseEntity.admit_date && caseEntity.discharge_date" class="text-muted">
								&mdash;
							</span>
							<span v-if="caseEntity.discharge_date">
								{{ $filters.formatDate(caseEntity.discharge_date) }}
							</span>
						</span>
					</b-col>
				</b-row>

				<p v-if="caseTypeName" class="small text-muted mb-0" title="Case Type">
					<span>{{ caseTypeName }}</span>
				</p>
				<p v-if="clientEmployeeName" class="small text-muted mb-0" title="Physician">
					<font-awesome-icon icon="user-md" fixed-width />
					<span>{{ clientEmployeeName }}</span>
				</p>
				<p v-if="facilityName" class="small text-muted mb-0" title="Facility">
					<font-awesome-icon icon="building" fixed-width />
					<span>{{ facilityName }}</span>
				</p>
			</b-col>
			<b-col cols="7" md="12" lg="6" class="mb-4 mb-md-0 text-right">
				<b-dropdown right menu-class="shadow" variant="primary">
					<template #button-content>
						<font-awesome-icon icon="plus" fixed-width />
						<span>New</span>
					</template>

					<b-dropdown-item
						@click="
							addingAppeal = true;
							addingRequest = false;
						"
						:active="addingAppeal"
					>
						<span>Appeal</span>
					</b-dropdown-item>

					<b-dropdown-item
						@click="
							addingRequest = true;
							addingAppeal = false;
						"
						:active="addingRequest"
					>
						<span>Request</span>
					</b-dropdown-item>
				</b-dropdown>

				<b-dropdown right menu-class="shadow" variant="secondary">
					<template #button-content>
						<font-awesome-icon icon="cog" fixed-width />
					</template>
					<b-dropdown-item :to="{ name: 'cases.view', params: { id: caseEntity.id } }">
						<font-awesome-icon icon="eye" fixed-width />
						<span>View Case</span>
					</b-dropdown-item>

					<b-dropdown-divider />

					<b-dropdown-item :to="{ name: 'cases.edit', params: { id: caseEntity.id } }">
						<font-awesome-icon icon="edit" fixed-width />
						<span>Edit Case</span>
					</b-dropdown-item>
				</b-dropdown>

				<b-dropdown
										split
										right
										@click="attachToAppeal(appeal, { redirect: false })"
										:disabled="attaching"
										variant="primary"
									>
										<template #button-content>
											<font-awesome-icon icon="paperclip" fixed-width />
											<!-- <span>Attach</span> -->
										</template>
										<b-dropdown-item-button
											@click="attachToAppeal(appeal, { redirect: true })"
											:disabled="attaching"
											title="Attach and view appeal"
										>
											<div>Attach &amp; View</div>
											<small class="text-muted"> Attach document and view appeal after. </small>
										</b-dropdown-item-button>
									</b-dropdown>
			</b-col>
		</b-row>
		<b-row v-if="addingAppeal" class="my-2">
			<b-col cols="12">
				<appeal-form
					:case-entity="caseEntity"
					@saved="addedAppeal"
					@cancel="addingAppeal = false"
					class="shadow"
				>
					<template #header>
						<b-card-header>
							<div class="d-flex justify-content-between align-items-center">
								<span class="font-weight-bold">Add New Appeal</span>
								<b-button
									variant="secondary"
									size="sm"
									@click="addingAppeal = false"
									title="Cancel"
									class="mb-0"
								>
									<font-awesome-icon icon="remove" fixed-width class="my-0 py-0" />
								</b-button>
							</div>
						</b-card-header>
					</template>
				</appeal-form>
			</b-col>
		</b-row>
		<b-row v-else-if="addingRequest" class="my-2">
			<b-col cols="12">
				<case-request-form :case-entity="caseEntity" @saved="addedRequest" @cancel="addingRequest = false">
					<template #header>
						<b-card-header>
							<div class="d-flex justify-content-between align-items-center">
								<span class="font-weight-bold">Add New Request</span>
								<b-button
									variant="secondary"
									size="sm"
									@click="addingRequest = false"
									title="Cancel"
									class="mb-0"
								>
									<font-awesome-icon icon="remove" fixed-width class="my-0 py-0" />
								</b-button>
							</div>
						</b-card-header>
					</template>
				</case-request-form>
			</b-col>
		</b-row>
		<div v-else-if="hasAppeals || hasRequests" class="my-2">
			<!-- <div v-if="hasRequests">
				<b-card no-body>
					<b-card-header>Requests</b-card-header>
					<b-list-group flush>
						<b-list-group-item v-for="caseRequest in requests" :key="caseRequest.id" class="shadow-sm">
							<p class="font-weight-bold mb-0">
								<span v-if="caseRequest.name">
									{{ caseRequest.name }}
								</span>
							</p>
							<p class="mb-0 text-muted">
								<span v-if="caseRequest.type_label"> {{ caseRequest.type_label }} Request </span>
							</p>
							<p v-if="caseRequest.status_label !== 'Closed'" class="mb-0">
								<span
									v-if="caseRequest.due_date"
									class="small"
									:class="caseRequest.is_overdue ? 'text-danger font-weight-bold' : 'text-muted'"
								>
									Due on {{ $filters.formatDate(caseRequest.due_date) }}
								</span>
							</p>
						</b-list-group-item>
					</b-list-group>
				</b-card>
			</div> -->
			<div v-if="hasAppeals">
				<b-card no-body>
					<b-card-header>Appeals</b-card-header>
					<b-list-group flush>
						<b-list-group-item v-for="appeal , i in appeals" :key="appeal.id" class="shadow-sm">
							<b-row>
								<b-col cols="8" md="6" lg="12" xl="6" class="text-left mb-2 mb-md-0">
									<p class="mb-0">
										<!-- <span v-if="appeal.appeal_level && appeal.appeal_level.name">
											{{ appealLevelNames[i] }}
										</span> -->
										<span v-if="true">
											{{ appealLevelNames[i] }}
										</span>
										<span v-else class="text-danger"> Missing Level </span>
										<span v-if="appeal.appeal_level && appeal.appeal_type" class="text-muted">
											&mdash;
										</span>
										<span v-if="appeal.appeal_type && appeal.appeal_type.name" class="text-muted">
											{{ appeal.appeal_type.name }} 
										</span>
										<span v-else class="text-danger"> Missing Type </span>
										<appeal-status-label :value="appeal" />
									</p>
									<p v-if="appeal.appeal_status !== 'Closed'" class="mb-0">
										<span
											v-if="appeal.due_date"
											class="small"
											:class="appeal.is_overdue ? 'text-danger font-weight-bold' : 'text-muted'"
										>
											Due on {{ $filters.formatDate(appeal.due_date) }}
										</span>
									</p>
									<!-- <p v-if="appeal.modified" class="small text-muted mb-0">
										Last updated {{ $filters.formatTimestamp(appeal.modified) }}
									</p> -->
								</b-col>
								
								<b-col cols="4" md="6" lg="12" xl="6" class="text-right">
									<b-dropdown
										split
										right
										@click="attachToAppeal(appeal, { redirect: false })"
										:disabled="attaching"
										variant="primary"
									>
										<template #button-content>
											<font-awesome-icon icon="paperclip" fixed-width />
											<!-- <span>Attach</span> -->
										</template>
										<b-dropdown-item-button
											@click="attachToAppeal(appeal, { redirect: true })"
											:disabled="attaching"
											title="Attach and view appeal"
										>
											<div>Attach &amp; View</div>
											<small class="text-muted"> Attach document and view appeal after. </small>
										</b-dropdown-item-button>
									</b-dropdown>
								</b-col>
							</b-row>
							<!-- For rendering requests for every appeal -->
							<!-- use appeal_level_id for rendering -->
							<b-card no-body>
								<b-card-header >Requests </b-card-header>
							 <div v-for="request,j in request_list" :key="request.id" class="shadow-sm upper-space">
								
								<!-- <b-row v-if="request.case_id===appeal.case_id && request.appeal_level == appeal.appeal_level_id " > -->
									<b-row v-if="request.case_id===appeal.case_id && request.appeal_level == i">
									<b-col cols="8" md="6" lg="12" xl="6" class="text-left mb-2 mb-md-0">
										<p  class="font-weight-bold mb-0 custom-padding" >
											<span>
												{{ request.request_type }} 
											</span>
										</p>
										<p class="mb-0 text-muted custom-padding">
											<span > {{ request.type_label }} Request</span>
										</p>
										<p v-if="request.status_label !== 'Closed'" class="custom-padding">
											<span
												v-if="request.due_date"
												class="small"
												:class="request.is_overdue ? 'text-danger font-weight-bold' : 'text-muted'"
											>
												Due on {{ $filters.formatDate(request.due_date) }} 
											</span>
										</p>
										<div class="custom-padding">
											<label class="checkbox-container">
												Response Received
												<input type="checkbox" v-model="responseReceived" class="response-checkbox">
												<span class="checkmark"></span>
											</label>
										</div>
									</b-col>
									<b-col cols="4" md="6" lg="12" xl="6" class="text-right">
									<b-dropdown
										split
										right
										@click="attachToAppeal(appeal, { redirect: false })"
										:disabled="attaching"
										variant="primary"
									>
										<template #button-content>
											<font-awesome-icon icon="paperclip" fixed-width />
											<!-- <span>Attach</span> -->
										</template>
										<b-dropdown-item-button
											@click="attachToAppeal(appeal, { redirect: true })"
											:disabled="attaching"
											title="Attach and view appeal"
										>
											<div>Attach &amp; View</div>
											<small class="text-muted"> Attach document and view appeal after. </small>
										</b-dropdown-item-button>
									</b-dropdown>
								</b-col>
								</b-row>
							 </div>
							</b-card>
							<!-- <b-row>
								<b-col cols="12">
									<b-dropdown :id="'dropdown-' + appeal.id" variant="btn btn-secondary"  class="dropdown-container">
										<template #button-content>
											<span>Decision</span>
											<span v-if="selectedOptionL1 && appeal.appeal_level.order_number==1">: {{ selectedOptionL1 }}</span>
											<span v-if="selectedOptionL2 && appeal.appeal_level.order_number==2">: {{ selectedOptionL2 }}</span>
											<span v-if="selectedOptionL3 && appeal.appeal_level.order_number==3">: {{ selectedOptionL3 }}</span>
											<span v-if="selectedOptionL4 && appeal.appeal_level.order_number==4">: {{ selectedOptionL4 }}</span>
											<span v-if="selectedOptionL5 && appeal.appeal_level.order_number==5">: {{ selectedOptionL5 }}</span>
											<span v-if="selectedOptionL6 && appeal.appeal_level.order_number==6">: {{ selectedOptionL6 }}</span>
											<span v-if="selectedOptionL7 && appeal.appeal_level.order_number==7">: {{ selectedOptionL7 }}</span>
										</template>
										<b-dropdown-item @click="updateStatus('Issues', appeal.appeal_level.order_number)" v-if="appeal.appeal_level.order_number==1">Issues</b-dropdown-item>
										<b-dropdown-item @click="updateStatus('No Findings', appeal.appeal_level.order_number)" v-if="appeal.appeal_level.order_number==1" >No Findings</b-dropdown-item>
										<b-dropdown-item @click="updateStatus('Favorable', appeal.appeal_level.order_number)" v-if="appeal.appeal_level.order_number !==1" >Favorable</b-dropdown-item>
										<b-dropdown-item @click="updateStatus('Not Favorable', appeal.appeal_level.order_number)" v-if="appeal.appeal_level.order_number !==1" >Non Favorable</b-dropdown-item>
										<b-dropdown-item @click="updateStatus('Partially Favorable', appeal.appeal_level.order_number)" v-if="appeal.appeal_level.order_number !==1 && appeal.appeal_level.order_number !==2" >Partially Favorable</b-dropdown-item>
									</b-dropdown>
								</b-col>
							</b-row> -->
						</b-list-group-item>
					</b-list-group>
				</b-card>
			</div>
		</div>
		<div v-else-if="false">
			<b-alert show variant="info" class="my-2">
				<font-awesome-icon icon="exclamation-triangle" fixed-width />
				No appeals or requests have been created for this case.
			</b-alert>
		</div>
	</b-list-group-item>
</template>

<style>
.dropdown-container {
  margin-top: 20px;
  
}
.custom-padding {
	
    padding-left: 20px; /* Adjust the value as needed */
}
.upper-space{
	margin-top: 5px;
}
</style>

<script>
import AppealForm from "@/clients/components/Appeals/Form.vue";
import AppealStatusLabel from "@/clients/components/Appeals/StatusLabel.vue";
import CaseStatusLabel from "@/clients/components/Cases/StatusLabel.vue";
import CaseRequestForm from "@/clients/components/CaseRequests/Form.vue";
import axios from "axios";


export default {
	name: "AttachCaseListItem",
	components: {
		AppealForm,
		AppealStatusLabel,
		CaseStatusLabel,
		CaseRequestForm,
	},
	props: {
		patient: {
			required: true,
			type: Object,
			default: () => {
				return {
					id: null,
					first_name: null,
					last_name: null,
					full_name: null,
					list_name: null,
				};
			},
		},
		caseEntity: {
			type: Object,
			default: () => {
				return {
					id: null,
					appeals: [],
					case_requests: [],
					case_type: {
						id: null,
						name: null,
					},
					facility: {
						id: null,
						name: null,
					},
				};
			},
		},
		document: {
			required: true,
			type: Object,
			default: () => {
				return {
					id: null,
					created: null,
				};
			},
		},
	},
	
	data() {
		return {
			addingAppeal: false,
			appeals: this.caseEntity.appeals || [],

			addingRequest: false,
			requests: this.caseEntity.requests || [],

			loading: false,
			attaching: false,
			request_list:null,
			selectedOptionL1: null,
			selectedOptionL2: null,
			selectedOptionL3: null,
			selectedOptionL4: null,
			selectedOptionL5: null,
			selectedOptionL6: null,
			selectedOptionL7: null,
			responseReceived:true,
			appealLevelNames:[],
			appealLevelNamesObj:[],
		};
	},
	computed: {
		caseTypeName() {
			return this.caseEntity.case_type?.name ?? "";
		},
		clientEmployeeName() {
			return this.caseEntity.client_employee?.full_name ?? "";
		},
		facilityName() {
			return this.caseEntity.facility?.name ?? "";
		},
		hasAppeals() {
			return this.appeals.length > 0;
		},
		hasRequests() {
			return this.requests.length > 0;
		},
	},
	methods: {
		addedAppeal(appeal) {
			this.addingAppeal = false;
			this.appeals.push(appeal);

			this.$emit("added-appeal", appeal);
			this.test();
		},
		addedRequest(request) {
			this.addingRequest = false;
			this.requests.push(request);

			this.$emit("added-request", request);
			this.test();
		},
		async attachToAppeal(appeal, options = {}) {
			try {
				this.attaching = true;

				const response = await this.$store.dispatch("incomingDocuments/attachAppeal", {
					id: this.document.id,
					case_id: appeal.case_id,
					appeal_id: appeal.id,
				});

				this.$emit("attached", response);

				if (options.redirect && options.redirect === true) {
					this.$router.push({
						name: "appeals.view",
						params: {
							id: appeal.case_id,
							appeal_id: appeal.id,
						},
					});
				}
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Attach Failed",
					message: "An error occurred when attempting to attach to appeal.",
				});
			} finally {
				this.attaching = false;
			}
		},
		async attachToCase(caseEntity, options = {}) {
			let message = `Are you sure you want to merge the current document in with case #${caseEntity.id} (Admit Date: ${caseEntity.admit_date})?`;

			if (!redirectAfter) {
				message +=
					" The document will be removed from the queue and you will need to search the patient in order to find it again.";
			}

			if (!confirm(message)) {
				return false;
			}

			try {
				const response = await this.$store.dispatch("incomingDocuments/attachCase", {
					id: this.document.id,
					case_id: caseEntity.id,
				});

				this.$emit("attached-case", response);

				if (options.redirect && options.redirect === true) {
					this.$router.push({
						name: "cases.view",
						params: {
							id: caseEntity.id,
						},
					});
				}
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Attach Failed",
					message: "An error occurred when attempting to attach to case.",
				});
			} finally {
				if (!redirectAfter) {
					this.$emit("refresh");
				}
			}
		},

		async test(){
			try {
				const url = "/client/request";
				
				const response = await axios.get(url, {
				headers: {
					"Accept": "application/json",
					// You can add other headers here if needed
				},
				});
				const urlInsurance = "/client/insuranceappeal";
				
				const responseInsurance = await axios.get(urlInsurance, {
				headers: {
					"Accept": "application/json",
					// You can add other headers here if needed
				},
				});
				// Handle the response data here
				this.request_list = []
				// this.request_list = response.data;
				this.request_list = [...this.request_list , ...response.data];
				console.log('request list updated  = ',this.request_list);
				console.log("appeals =" , this.appeals);
				console.log("case entity = ",this.caseEntity);
				console.log("insurance response = ", responseInsurance.data);
				this.appealLevelNames = [];
				this.appealLevelNamesObj = [];
				this.appeals.forEach((item, index) => {
				console.log(`Element at index ${index}:`, item);
				console.log("appeal level id = ",item.appeal_level_id);
				responseInsurance.data.forEach((value, i)=>{
					if(this.caseEntity.insurance_provider_id==value.insurance_provider_id){
					console.log('insurance =',value);
						if(index==0){
							// this.appealLevelNames.push(value.label);
							console.log('value of index = ',index);
							this.appealLevelNamesObj.push({label:value.label , id:value.id});
						}
					}
				})
				return;
				});
				this.appealLevelNamesObj.sort((a,b)=> a.id - b.id);
				this.appealLevelNamesObj.forEach((item,index)=>{
					this.appealLevelNames.push(item.label);
				});
				console.log('details  = ', this.appealLevelNames);
				console.log('details obj updated = ', this.appealLevelNamesObj);

				} 
			catch (error) {
				console.error(error);
			}

		},
		updateStatus(selectedStatus, appealOrder) 
		{
        // Call your function with the selected status and appealId
        // For example, you can make an API request here or update the local data
        console.log(`Selected status: ${selectedStatus}, Appeal ID: ${appealOrder}`);
		if(appealOrder==1)
		{
			this.selectedOptionL1=selectedStatus;
		}
		else if(appealOrder==2){
			this.selectedOptionL2=selectedStatus;
		}
		else if(appealOrder==3){
			this.selectedOptionL3=selectedStatus;
		}
		else if(appealOrder==4){
			this.selectedOptionL4=selectedStatus;
		}
		else if(appealOrder==5){
			this.selectedOptionL5=selectedStatus;
		}
		else if(appealOrder==6){
			this.selectedOptionL6=selectedStatus;
		}
		else if(appealOrder==7){
			this.selectedOptionL7=selectedStatus;
		}
		
        // Call your function with the selectedStatus and appealId as arguments
        // e.g., this.yourFunction(selectedStatus, appealId);
        },
	},
	mounted(){
		this.test();
	},
};
</script>
