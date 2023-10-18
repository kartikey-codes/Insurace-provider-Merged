<template>
	<loading-indicator v-if="loading" class="my-5" />
	<validation-observer v-else v-bind="$attrs" ref="observer" v-slot="{ invalid }">
		<b-form @submit.prevent="save">
			<b-card no-body :class="flush ? 'border-0' : ''">
				<slot name="header"></slot>

				<b-card-body>

					<validation-provider
						vid="status"
						name="Status"
						:rules="{ required: true }"
						v-slot="validationContext"
						>
						<b-form-group label="Level" label-for="status" label-cols-lg="4">
							<b-form-select
							v-model="entity.status"
							:options="insuranceData"
							:state="getValidationState(validationContext)"
							name="status"
							value-field="id"
      						text-field="label"
							required="required"
							@change="updateAppealLevelCount"
							/>
							<b-form-invalid-feedback
							v-for="error in validationContext.errors"
							:key="error"
							v-text="error"
							/>
						</b-form-group>	
					</validation-provider>


					<!-- <b-form-group label="Type" label-for="request_type" label-cols-lg="4" class="mb-4">
						<b-form-radio-group
							stacked
							name="request_type"
							v-model="entity.request_type"
							:options="requestTypes"
							:disabled="loadingRequestTypes || saving"
							required="required"
							value-field="value"
							text-field="name"
						/>
					</b-form-group> -->

					<b-form-group label="Type" label-for="request_type" label-cols-lg="4" class="mb-4">
					<b-form-select
						v-model="entity.request_type"
						:options="requestTypes"
						:disabled="loadingRequestTypes || saving"
						required
						value-field="value"
						text-field="name"
					/>
					</b-form-group>


					<validation-provider
						vid="name"
						name="Name"
						:rules="{ required: true, min: 2, max: 50 }"
						v-slot="validationContext"
					>
						<b-form-group
							label="Name"
							label-for="name"
							label-cols-lg="4"
							description="The display name for this request"
						>
							<b-form-input
								autofocus
								name="name"
								type="text"
								size="lg"
								v-model="entity.name"
								required
								placeholder="Required"
								:state="getValidationState(validationContext)"
								:disabled="saving"
							/>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<validation-provider
						vid="assigned_to"
						name="Assigned To"
						:rules="{ required: false }"
						v-slot="validationContext"
					>
						<b-form-group label="Assigned To" label-for="assigned_to" label-cols-lg="4">
							<b-form-select
								v-model="entity.assigned_to"
								name="assigned_to"
								:options="users"
								:disabled="saving"
								value-field="id"
								text-field="full_name"
							>
								<template #first>
									<option :value="null">(Unassigned)</option>
								</template>
							</b-form-select>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<validation-provider
						vid="due_date"
						name="Due Date"
						:rules="{ required: true }"
						v-slot="validationContext"
					>
						<b-form-group label="Due Date" label-for="due_date" label-cols-lg="4">
							<b-form-input
								type="date"
								v-model="due_date"
								name="due_date"
								required="required"
								:readonly="false"
								:disabled="saving"
								:state="getValidationState(validationContext)"
							/>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<b-form-group
						label="Priority"
						label-for="priority"
						label-cols-lg="4"
						description="This is a high priority request."
					>
						<b-form-checkbox name="priority" v-model="entity.priority">Priority</b-form-checkbox>
					</b-form-group>

					<validation-provider
						vid="agency_id"
						name="Agency"
						:rules="{ required: false }"
						v-slot="validationContext"
					>
						<b-form-group label="Agency" label-for="agency" label-cols-lg="4">
							<b-input-group>
								<b-select
									name="agency_id"
									v-model="agency_autofill"
									:options="agencies"
									:disabled="saving || loadingAgencies"
									:state="getValidationState(validationContext)"
									value-field="id"
									text-field="name"
								>
									<template #first>
										<b-select-option :value="null"> (None) </b-select-option>
									</template>
								</b-select>
								<template #append>
									<b-button
										variant="primary"
										@click="addingAgency = !addingAgency"
										:active="addingAgency"
									>
										<font-awesome-icon icon="plus" fixed-width />
									</b-button>
								</template>
							</b-input-group>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<div v-if="addingAgency" class="mb-4">
						<agency-form @cancel="addingAgency = false" @saved="addedNewAgency">
							<template #header>
								<b-card-header>
									<div class="d-flex justify-content-between align-items-center">
										<span class="font-weight-bold">Add New Agency</span>
										<b-button
											variant="secondary"
											size="sm"
											@click="addingAgency = false"
											title="Cancel"
											class="mb-0"
										>
											<font-awesome-icon icon="remove" fixed-width class="my-0 py-0" />
										</b-button>
									</div>
								</b-card-header>
							</template>
						</agency-form>
					</div>

					<validation-provider
						vid="insurance_provider_id"
						name="Insurance Provider"
						:rules="{ required: false }"
						v-slot="validationContext"
					>
						<b-form-group label="Insurance Provider " label-for="insurance_provider" label-cols-lg="4">
							<b-input-group>
								<b-select
									name="insurance_provider_id"
									v-model="insurance"
									:options="insuranceProviders"
									:disabled="saving || loadingInsuranceProviders"
									:state="getValidationState(validationContext)"
									value-field="id"
									text-field="name"
								>
									<template #first>
										<b-select-option :value="null"> (None) </b-select-option>
									</template>
								</b-select>
								<template #append>
									<b-button
										variant="primary"
										@click="addingInsuranceProvider = !addingInsuranceProvider"
										:active="addingInsuranceProvider"
									>
										<font-awesome-icon icon="plus" fixed-width />
									</b-button>
								</template>
							</b-input-group>
							<b-form-invalid-feedback
								v-for="error in validationContext.errors"
								:key="error"
								v-text="error"
							/>
						</b-form-group>
					</validation-provider>

					<div v-if="addingInsuranceProvider" class="mb-4">
						<insurance-provider-form
							@cancel="addingInsuranceProvider = false"
							@saved="addedNewInsuranceProvider"
						>
							<template #header>
								<b-card-header>
									<div class="d-flex justify-content-between align-items-center">
										<span class="font-weight-bold">Add Insurance Provider</span>
										<b-button
											variant="secondary"
											size="sm"
											@click="addingInsuranceProvider = false"
											title="Cancel"
											class="mb-0"
										>
											<font-awesome-icon icon="remove" fixed-width class="my-0 py-0" />
										</b-button>
									</div>
								</b-card-header>
							</template>
						</insurance-provider-form>
					</div>
				</b-card-body>

				<b-card-footer>
					<b-row>
						<b-col cols="12" md="6" xl="4" class="mb-4 mb-md-0">
							<b-button v-if="!disableCancel" block variant="light" @click="cancel">Cancel</b-button>
						</b-col>
						<b-col cols="12" md="6" offset-xl="4" xl="4">
							<b-button
								block
								variant="primary"
								type="submit"
								:disabled="saving || disabled"
								:title="invalid ? 'Please fix any validation errors' : 'Save'"
								@click="buttonPressed"
							>
								<font-awesome-icon icon="circle-notch" v-if="saving" spin fixed-width />
								<span>Save</span>
							</b-button>
						</b-col>
					</b-row>
				</b-card-footer>
			</b-card>
		</b-form>
	</validation-observer>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import { formatErrors, getValidationState } from "@/validation";
import { getDateOffsetDaysString } from "@/shared/helpers/dateHelper";
import AgencyForm from "@/clients/components/Agencies/Form.vue";
import InsuranceProviderForm from "@/clients/components/InsuranceProviders/Form.vue";
import axios from "axios";

export default {
	name: "CaseRequestForm",
	components: {
		AgencyForm,
		InsuranceProviderForm,
	},
	props: {
		id: {
			default: null,
		},
		caseEntity: {
			type: Object,
			default: () => {
				return {
					id: null,
				};
			},
		},
		flush: {
			type: Boolean,
			default: false,
		},
		disabled: {
			type: Boolean,
			default: false,
		},
		disableCancel: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			loading: true,
			saving: false,
			entity: {
				id: this.id,
				case_id: this.caseEntity.id,
				name: "",
				request_type: this.defaultRequestType,
				assigned_to: this.currentUserId,
				priority: false,
				due_date: getDateOffsetDaysString(2),
				status: "Level 0",
				appeal_level:null,
			},
			loadingRequestTypes: false,
			addingAgency: false,
			addingInsuranceProvider: false,
			statusOptions: [
				{ value: 1, label: "ADR" },
				{ value: 2, label: "Discussion" },
				{ value: 3, label: "Redetermination" },
				{ value: 4, label: "Reconsideration" },
				{ value: 5, label: "ALJ Hearing" },
				{ value: 6, label: "Level 4" },
				{ value: 7, label: "Level 5" },

			],
			letterData: null,	
			due_date:null,
			insurance:null,
			agency_autofill:null,
			insuranceData:[],
		};
	},
	computed: mapGetters({
		currentUserId: "userId",
		users: "users/active",
		loadingUsers: "users/loadingActive",
		defaultRequestType: "caseRequests/defaultType",
		requestTypes: "caseRequests/types",
		agencies: "agencies/active",
		loadingAgencies: "agencies/loadingActive",
		insuranceProviders: "insuranceProviders/active",
		loadingInsuranceProviders: "insuranceProviders/loadingActive",
	}),
	mounted() {
		
		this.test();

		if (this.id) {
			this.refresh();
			
		} else {
			this.loading = false;
				
		}
		
		// for calling autofillform function during mounting phase for autofilling the form
		if(true){
			this.autoFillForm();
		}

	},
	methods: {
		getValidationState,
		cancel(e) {
			if (e) {
				e.preventDefault();
			}

			this.$emit("cancel");
		},
		async refresh() {
			try {
				this.loading = true;

				const response = await this.$store.dispatch("caseRequests/get", {
					id: this.id,
				});

				this.entity = response;
				this.$emit("loaded", response);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Failed to find request",
				});
			} finally {
				this.loading = false;
			}
		},
		async save() {
			try {
				this.saving = true;

				const response = await this.$store.dispatch("caseRequests/save", this.entity);

				this.$emit("saved", response);
				this.$emit("update:id", response.id);

				if (!this.entity.id) {
					this.$store.dispatch("notify", {
						variant: "success",
						title: "Request created",
						message: "Case request has been added successfully",
					});
				}
			} catch (e) {
				if (e.response.data.errors) {
					this.$refs.observer.setErrors(formatErrors(e.response.data.errors));
				}

				this.$store.dispatch("apiError", {
					error: e,
					message: "Failed to save request. Please check for errors.",
					variant: "warning",
				});
			} finally {
				this.saving = false;
			}
		},
		addedNewAgency(agency) {
			this.$store.dispatch("agencies/getActive");
			this.addingAgency = false;
			this.entity.agency_id = agency.id;
		},
		addedNewInsuranceProvider(insuranceProvider) {
			this.$store.dispatch("insuranceProviders/getActive");
			this.addingInsuranceProvider = false;
			this.entity.insurance_provider_id = insuranceProvider.id;
		},

		formatDate(dateString) {
			const date = new Date(dateString);
			const year = date.getFullYear();
			const month = String(date.getMonth() + 1).padStart(2, '0');
			const day = String(date.getDate()).padStart(2, '0');
			return `${year}-${month}-${day}`;
		},

		addDaysToDate(dateString, days)
		 {
			const date = new Date(dateString);
			date.setDate(date.getDate() + days);
			return date.toISOString().split('T')[0];
		},

		async autoFillForm() {
			try{
				const url = "http://localhost:8765/client/textract";
				
				const responses = await axios.get(url, {
				headers: {
					"Accept": "application/json",
					// You can add other headers here if needed
				},
				});
				this.letterData = responses.data[0]; 
				this.daysToRespond =  parseInt(responses.data[0].days_to_respond, 10);
				this.due_date = this.formatDate(responses.data[0].letter_date);  
				this.due_date = this.addDaysToDate(this.due_date , this.daysToRespond);
				this.insurance = responses.data[0].insurance_provider;
				// this.insurance = responses.data[0].insurance_provider; 
				this.insuranceProviders.unshift(this.insurance);
				this.agency_autofill = responses.data[0].sender;
				this.agencies.unshift(this.agency_autofill);
			} 
			catch (error) {
				console.error("Error:", error);
			}
    	},

		async buttonPressed() {
		console.log('Save button pressed');
		console.log("assigned_to = ", this.entity.assigned_to);
		const data = {'id':this.entity.assigned_to}

				// Use Axios to make the request to the controller for sending user_id 
				console.log(data);
				const resp = await axios.post('/client/sendemail', data);
				console.log(resp);
				// Reload the page
				// location.reload();

		},

		async test(){
			try {
				const url = "/client/insuranceappeal";
				
				const response = await axios.get(url, {
				headers: {
					"Accept": "application/json",
					// You can add other headers here if needed
				},
				});
				// Handle the response data here
				// this.insuranceData = response.data;
				let count =1 ;
				response.data.forEach((item, index) => {
				console.log(`Element at index ${index}:`, item.insurance_provider_id);
				if(item.insurance_provider_id==this.caseEntity.insurance_provider_id){
					console.log("match found = ", item);
					this.insuranceData.push({label:item.label, id:item.id , count:count});
					count ++;
				}
				});
				this.insuranceData.sort((a,b)=> a.id - b.id);
				this.insuranceData.forEach((item,index)=>{
					item.count=index;
				});
				console.log("response = " , this.insuranceData);
				console.log("case entity =", this.caseEntity);
				
				} 
			catch (error) {
				console.error(error);
			}

		},

		updateAppealLevelCount(){
			const selectedOption = this.insuranceData.find(option => option.id === this.entity.status);
			if (selectedOption) {
				this.entity.appeal_level = selectedOption.count;
			} else {
				this.entity.appeal_level = null; // Handle the case where no option is selected
			}
			console.log("apeeal level =" , this.entity.appeal_level);
		}
	},
};
</script>
