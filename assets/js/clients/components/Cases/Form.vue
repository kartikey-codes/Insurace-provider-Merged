<template>
	<loading-indicator v-if="loading" class="my-5" title="Fetching case..." />
	<validation-observer v-else v-bind="$attrs" ref="observer" v-slot="{ invalid }">
		<b-form @submit.prevent="save">
			<b-card no-body :class="flush ? 'border-0' : ''">
				<b-card-body class="mb-0">
					<validation-provider
						vid="patient_id"
						name="Patient"
						:rules="{ required: true }"
						v-slot="validationContext"
					>
						<div v-if="!hidePatient">
							<div v-if="addingPatient" class="mb-4">
								<patient-form @cancel="addingPatient = false" @saved="addedNewPatient">
									<template #header>
										<b-card-header>
											<div class="d-flex justify-content-between align-items-center">
												<span class="font-weight-bold">Add New Patient</span>
												<b-button
													variant="secondary"
													size="sm"
													@click="addingPatient = false"
													title="Cancel"
													class="mb-0"
												>
													<font-awesome-icon icon="remove" fixed-width class="my-0 py-0" />
												</b-button>
											</div>
										</b-card-header>
									</template>
								</patient-form>
							</div>
							<div v-else-if="editingPatient" class="mb-4">
								<patient-form
									:id="editingPatient"
									@cancel="editingPatient = false"
									@saved="savedPatient"
								>
									<template #header>
										<b-card-header>
											<div class="d-flex justify-content-between align-items-center">
												<span class="font-weight-bold">Edit Patient</span>
												<b-button
													variant="secondary"
													size="sm"
													@click="editingPatient = false"
													title="Cancel"
													class="mb-0"
												>
													<font-awesome-icon icon="remove" fixed-width class="my-0 py-0" />
												</b-button>
											</div>
										</b-card-header>
									</template>
								</patient-form>
							</div>
							<div v-else>
								<b-form-group label="Patient" label-for="patient" label-cols-lg="4">
									<patient-search
										name="patient"
										v-model="currentPatient"
										@add="addingPatient = true"
										:state="getValidationState(validationContext)"
										:disabled="saving"
									/>
								</b-form-group>
								<b-row v-if="currentPatient && currentPatient.id">
									<b-col cols="12" lg="8" offset-lg="4" class="mb-4">
										<div class="d-flex justify-content-between align-items-center">
											<div>
												<h2
													class="h3 my-0 mb-1 font-weight-bold text-uppercase"
													v-text="currentPatient.list_name"
												/>
												<h3
													v-if="currentPatient.date_of_birth"
													class="h6 mt-0 mb-1 text-muted text-uppercase"
												>
													<span class="font-weight-bold">
														{{ $filters.formatDate(currentPatient.date_of_birth) }}
													</span>
													<span
														v-if="
															currentPatient.age != null &&
															currentPatient.age != undefined
														"
													>
														&mdash;
														<span>
															<font-awesome-icon
																v-if="currentPatient.is_birthday"
																icon="birthday-cake"
																class="text-muted"
															/>
															<span
																class="font-weight-bold"
																v-text="currentPatient.age"
															/>
														</span>
													</span>
												</h3>
												<h3 v-else class="h6 mt-0 mb-1 text-warning">(Missing DOB)</h3>
											</div>

											<b-button variant="light" class="mb-0" @click="editPatient">
												<font-awesome-icon icon="edit" fixed-width />
												<span>Edit Patient</span>
											</b-button>
										</div>
									</b-col>
								</b-row>
							</div>
						</div>
						<b-form-invalid-feedback
							v-for="error in validationContext.errors"
							:key="error"
							v-text="error"
						/>
					</validation-provider>

					<b-row>
						<b-col col="12">
							<b-form-group label="Case Type" label-for="case_type" label-cols-lg="4">
								<b-form-radio-group>
									<input type="radio" id="pre" name="case_type" value="pre" v-model="entity.caseType">
									<label for="pre">Pre-Payment</label>
									<span>&nbsp;</span>
									<input type="radio" id="post" name="case_type" value="post" v-model="entity.caseType">
									<label for="post">Post-Payment</label>
								</b-form-radio-group>
							</b-form-group>

						</b-col>
					</b-row>

					<b-row>
						<b-col col="12">
							<validation-provider
									vid="facility_id"
									name="Facility"
									:rules="{ required: false }"
									v-slot="validationContext"
								>
									<b-form-group label="Facility" label-for="facility" label-cols-lg="4">
										<b-input-group>
											<b-form-select
												name="facility_id"
												v-model="entity.facility_id"
												:options="facilities"
												:disabled="saving || loadingFacilities"
												:state="getValidationState(validationContext)"
												value-field="id"
												text-field="name"
											>
												<template #first>
													<option :value="null">(None)</option>
												</template>
											</b-form-select>
											<b-form-invalid-feedback
												v-for="error in validationContext.errors"
												:key="error"
												v-text="error"
											/>
											<template #append>
												<b-button
													variant="primary"
													@click="addingFacility = !addingFacility"
													:active="addingFacility"
												>
													<font-awesome-icon icon="plus" fixed-width />
												</b-button>
											</template>
										</b-input-group>
									</b-form-group>
								</validation-provider>

								<div v-if="addingFacility" class="mb-4">
									<h6 class="text-muted">Add New Facility</h6>

									<facility-form @saved="addedFacility" @cancel="addingFacility = false" />
								</div>

								<validation-provider
									vid="visit_number"
									name="Visit ID/Number"
									:rules="{ required: false }"
									v-slot="validationContext"
								>
									<b-form-group label="Visit ID / Number" label-for="visit_number" label-cols-lg="4">
										<b-form-input
											name="visit_number"
											v-model="entity.visit_number"
											:disabled="saving"
											:state="getValidationState(validationContext)"
											type="text"
											autocomplete="off"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>
						</b-col>
					</b-row>

					<b-row>
						<b-col cols="12">
							<validation-provider
								vid="admit_date"
								name="Admit Date"
								:rules="{ required: true }"
								v-slot="validationContext"
							>
								<b-form-group label="Admit Date" label-for="admit_date" label-cols-lg="4">
									<b-form-input
										name="admit_date"
										type="date"
										v-model="entity.admit_date"
										:disabled="saving"
										:state="getValidationState(validationContext)"
										required
									/>
									<b-form-invalid-feedback
										v-for="error in validationContext.errors"
										:key="error"
										v-text="error"
									/>
								</b-form-group>
							</validation-provider>
						</b-col>
						<b-col cols="12">
							<validation-provider
								vid="discharge_date"
								name="Discharge Date"
								:rules="{ required: false }"
								v-slot="validationContext"
							>
								<b-form-group label="Discharge Date" label-for="discharge_date" label-cols-lg="4">
									<b-form-input
										name="discharge_date"
										type="date"
										v-model="entity.discharge_date"
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
						</b-col>
					</b-row>
					<b-row>
						<b-col cols="12">
							<validation-provider
								vid="insurance_provider_id"
								name="Insurance Provider"
								:rules="{ required: false }"
								v-slot="validationContext"
							>
								<b-form-group
									label="Insurance Provider"
									label-for="insurance_provider"
									label-cols-lg="4"
								>
									<b-input-group>
										<b-select
											name="insurance_provider_id"
											v-model="entity.insurance_provider_id"
											:options="insuranceProviders"
											:disabled="saving || loadingInsuranceProviders"
											:state="getValidationState(validationContext)"
											value-field="id"
											text-field="name"
											@change="changedInsuranceProvider"
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
												<span class="font-weight-bold">Add New Insurance Provider</span>
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

							<validation-provider
								vid="case_type_id"
								name="Audit Type"
								:rules="{ required: false }"
								v-slot="validationContext"
							>
								<b-form-group label="Audit Type" label-for="case_type_id" label-cols-lg="4">
									<b-form-select
										name="case_type_id"
										v-model="entity.case_type_id"
										:options="caseTypes"
										:disabled="saving || loadingCaseTypes"
										:state="getValidationState(validationContext)"
										value-field="id"
										text-field="name"
									/>
									<b-form-invalid-feedback
										v-for="error in validationContext.errors"
										:key="error"
										v-text="error"
									/>
								</b-form-group>
							</validation-provider>

							<!-- <validation-provider
								vid="insurance_type_id"
								name="Insurance Type"
								:rules="{ required: true }"
								v-slot="validationContext"
							>
								<b-form-group label="Insurance Type" label-for="insurance_type_id" label-cols-lg="4">
									<b-select
										name="insurance_type_id"
										v-model="entity.insurance_type_id"
										:options="availableInsuranceTypes"
										:disabled="saving || loadingInsuranceTypes"
										:state="getValidationState(validationContext)"
										value-field="id"
										text-field="name"
										required="required"
									/>
									<b-form-invalid-feedback
										v-for="error in validationContext.errors"
										:key="error"
										v-text="error"
									/>
								</b-form-group>
							</validation-provider> -->

							<validation-provider
								vid="client_employee_id"
								name="Primary Employee"
								:rules="{ required: true, numeric: true }"
								v-slot="validationContext"
							>
								<b-form-group
									label="Primary Physician"
									label-for="client_employee_id"
									label-cols-lg="4"
									description="The licensed physician responsible for this case"
								>
									<b-input-group>
										<b-form-select
											name="client_employee_id"
											v-model="entity.client_employee_id"
											:options="clientEmployees"
											:disabled="saving || loadingClientEmployees"
											:state="getValidationState(validationContext)"
											value-field="id"
											text-field="full_name"
											required
										>
											<template #first>
												<option disabled v-if="!hasClientEmployees" :value="null">
													No physicians added.
												</option>
											</template>
										</b-form-select>
										<template #append>
											<b-button
												variant="primary"
												@click="addingClientEmployee = !addingClientEmployee"
												:active="addingClientEmployee"
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

							<div v-if="addingClientEmployee" class="mb-4">
								<client-employee-form
									@saved="addedClientEmployee"
									@cancel="addingClientEmployee = false"
								>
									<template #header>
										<b-card-header>
											<div class="d-flex justify-content-between align-items-center">
												<span class="font-weight-bold">Add New Physician</span>
												<b-button
													variant="secondary"
													size="sm"
													@click="addingClientEmployee = false"
													title="Cancel"
													class="mb-0"
												>
													<font-awesome-icon icon="remove" fixed-width class="my-0 py-0" />
												</b-button>
											</div>
										</b-card-header>
									</template>
								</client-employee-form>
							</div>

							<b-form-group
								id="utc"
								label="Unable To Complete"
								label-for="utc"
								label-cols-lg="4"
								description="This case is unable to be progressed any further"
							>
								<b-form-checkbox name="utc" v-model="entity.unable_to_complete" :disabled="saving">
									UTC
								</b-form-checkbox>
							</b-form-group>

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
						</b-col>
					</b-row>
				</b-card-body>

				<b-card-body>
					<h6 class="text-muted">Additional</h6>

					<b-card no-body>
						<b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseDenial
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
							>
								Audit Denial Details
							</b-button>
						</b-card-header>
						<b-collapse id="collapseDenial" role="tabpanel">
							<b-card-body>
								<validation-provider
									vid="denial_type_id"
									name="Denial Type"
									:rules="{ required: false }"
									v-slot="validationContext"
								>
									<b-form-group label="Denial Type" label-for="denial_type_id" label-cols-lg="4">
										<b-select
											name="denial_type_id"
											v-model="entity.denial_type_id"
											:options="denialTypes"
											:disabled="saving || loadingDenialTypes"
											:state="getValidationState(validationContext)"
											value-field="id"
											text-field="name"
										>
											<template #first>
												<option :value="null">(None)</option>
											</template>
										</b-select>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>

								<div v-if="addingDenialReason">
									<denial-reason-form
										autofocus
										@cancel="addingDenialReason = false"
										@saved="addedNewDenialReason"
									>
										<template #header>
											<b-card-header>
												<div class="d-flex justify-content-between align-items-center">
													<span class="font-weight-bold">Add New Denial Reason</span>
													<b-button
														variant="secondary"
														size="sm"
														@click="addingDenialReason = false"
														title="Cancel"
														class="mb-0"
													>
														<font-awesome-icon
															icon="remove"
															fixed-width
															class="my-0 py-0"
														/>
													</b-button>
												</div>
											</b-card-header>
										</template>
									</denial-reason-form>
								</div>
								<div v-else>
									<b-form-group
										label="Denial Reasons"
										label-for="denial_reasons"
										label-cols-lg="4"
										v-if="!addingDenialReason"
									>
										<denial-reason-search-multi
											name="denial_reasons"
											v-model="currentDenialReasons"
											@add="addingDenialReason = true"
											:disabled="saving"
										/>
									</b-form-group>
								</div>
							</b-card-body>
						</b-collapse>
						<!-- <b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseDisciplines
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
							>
								Disciplines
							</b-button>
						</b-card-header>
						<b-collapse id="collapseDisciplines" role="tabpanel">
							<b-card-body>
								<b-form-group label="Disciplines" label-for="discipline_ids" label-cols-lg="4">
									<b-form-checkbox-group
										stacked
										name="discipline_ids"
										v-model="disciplineIds"
										:options="disciplines"
										:disabled="saving || loadingDisciplines"
										value-field="id"
										text-field="name"
									/>
								</b-form-group>
							</b-card-body>
						</b-collapse> -->

						
						<b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseClaimBillingCodes
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
							>
							Claim Billing Codes
							</b-button>
						</b-card-header>
						<b-collapse id="collapseClaimBillingCodes" role="tabpanel">
							<!-- <b-card-body>
								<div class="d-flex">
									<b-button
										variant="secondary"
										@click="addReadmission"
										:disabled="!canAddReadmission"
										class="ml-auto"
									>
										<font-awesome-icon icon="plus" fixed-width />
										<span>Add Codes</span>
									</b-button>
								</div>
							</b-card-body> -->
							<b-card-body>
							<validation-provider
									vid="insurance_plan"
									name="Insurance Plan"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group label="ICD-10-CM codes" label-for="insurance_plan" label-cols-lg="4">
										<b-form-input
											name="insurance_plan"
											type="text"
											v-model="entity.insurance_plan"
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


								<validation-provider
									vid="insurance_number"
									name="Insurance Plan ID / Number"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="CPT Codes"
										label-for="insurance_number"
										label-cols-lg="4"
									>
										<b-form-input
											name="insurance_number"
											type="text"
											v-model="entity.insurance_number"
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

								<validation-provider
									vid="insurance_number"
									name="Insurance Plan ID / Number"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="ICD-10-PCS codes"
										label-for="insurance_number"
										label-cols-lg="4"
									>
										<b-form-input
											name="insurance_number"
											type="text"
											v-model="entity.insurance_number"
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

								<validation-provider
									vid="insurance_number"
									name="Insurance Plan ID / Number"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="HCPCS codes"
										label-for="insurance_number"
										label-cols-lg="4"
									>
										<b-form-input
											name="insurance_number"
											type="text"
											v-model="entity.insurance_number"
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
							</b-card-body>

							<!-- <case-readmissions
								v-if="hasReadmissions"
								v-model="entity.case_readmissions"
								:disabled="saving"
							/>
							<b-card-body v-else>
								<empty-result icon="list"
									>No Codes
									<template #content> Add Billing Codes. </template>
								</empty-result>
							</b-card-body> -->
						</b-collapse>


						<b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseClaimDenialCodes
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
							>
								Claim Denial Codes
							</b-button>
						</b-card-header>

						<b-collapse id="collapseClaimDenialCodes" role="tabpanel">
							<b-card-body>
								<validation-provider
									vid="insurance_plan"
									name="CARCs"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group label="CARCs" label-for="insurance_plan" label-cols-lg="4">
										<b-form-input
											name="insurance_plan"
											type="text"
											v-model="entity.insurance_plan"
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

								<validation-provider
									vid="insurance_number"
									name="Insurance Plan ID / Number"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="RARCs"
										label-for="insurance_number"
										label-cols-lg="4"
									>
										<b-form-input
											name="insurance_number"
											type="text"
											v-model="entity.insurance_number"
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
							</b-card-body>
						</b-collapse>
						<!-- <b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseFacility
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
							>
								Facility &amp; Visit Details
							</b-button>
						</b-card-header> -->
						<!-- <b-collapse id="collapseFacility" role="tabpanel">
							<b-card-body>
								<validation-provider
									vid="facility_id"
									name="Facility"
									:rules="{ required: false }"
									v-slot="validationContext"
								>
									<b-form-group label="Facility" label-for="facility" label-cols-lg="4">
										<b-input-group>
											<b-form-select
												name="facility_id"
												v-model="entity.facility_id"
												:options="facilities"
												:disabled="saving || loadingFacilities"
												:state="getValidationState(validationContext)"
												value-field="id"
												text-field="name"
											>
												<template #first>
													<option :value="null">(None)</option>
												</template>
											</b-form-select>
											<b-form-invalid-feedback
												v-for="error in validationContext.errors"
												:key="error"
												v-text="error"
											/>
											<template #append>
												<b-button
													variant="primary"
													@click="addingFacility = !addingFacility"
													:active="addingFacility"
												>
													<font-awesome-icon icon="plus" fixed-width />
												</b-button>
											</template>
										</b-input-group>
									</b-form-group>
								</validation-provider>

								<div v-if="addingFacility" class="mb-4">
									<h6 class="text-muted">Add New Facility</h6>

									<facility-form @saved="addedFacility" @cancel="addingFacility = false" />
								</div>

								<validation-provider
									vid="visit_number"
									name="Visit ID/Number"
									:rules="{ required: false }"
									v-slot="validationContext"
								>
									<b-form-group label="Visit ID / Number" label-for="visit_number" label-cols-lg="4">
										<b-form-input
											name="visit_number"
											v-model="entity.visit_number"
											:disabled="saving"
											:state="getValidationState(validationContext)"
											type="text"
											autocomplete="off"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider>
							</b-card-body>
						</b-collapse> -->

						<b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseFinancial
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
							>
								Financial Details
							</b-button>
						</b-card-header>
						<b-collapse id="collapseFinancial" role="tabpanel">
							<b-card-body>
								<validation-provider
									vid="total_claim_amount"
									name="Total Claim Amount"
									:rules="{ required: false, min: 0, max_value: currencyMax, double: true }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Total Claim Amount"
										label-for="total_claim_amount"
										label-cols-lg="4"
									>
										<b-input-group>
											<b-input-group-prepend is-text>
												<font-awesome-icon icon="dollar-sign" fixed-width />
											</b-input-group-prepend>
											<b-form-input
												name="total_claim_amount"
												type="number"
												v-model="entity.total_claim_amount"
												:disabled="saving"
												:state="getValidationState(validationContext)"
												:min="0"
												:max="currencyMax"
												step="1.00"
												maxlength="10"
												autocomplete="off"
											/>
											<b-form-invalid-feedback
												v-for="error in validationContext.errors"
												:key="error"
												v-text="error"
											/>
										</b-input-group>
									</b-form-group>
								</validation-provider>

								<validation-provider
									vid="disputed_amount"
									name="Disputed Amount"
									:rules="{ required: false, min: 0, max_value: currencyMax, double: true }"
									v-slot="validationContext"
								>
									<b-form-group label="Disputed Amount" label-for="disputed_amount" label-cols-lg="4">
										<b-input-group>
											<b-input-group-prepend is-text>
												<font-awesome-icon icon="dollar-sign" fixed-width />
											</b-input-group-prepend>
											<b-form-input
												name="disputed_amount"
												type="number"
												v-model="entity.disputed_amount"
												:disabled="saving"
												:state="getValidationState(validationContext)"
												:min="0"
												:max="currencyMax"
												step="1.00"
												maxlength="10"
												autocomplete="off"
											/>
											<b-form-invalid-feedback
												v-for="error in validationContext.errors"
												:key="error"
												v-text="error"
											/>
										</b-input-group>
									</b-form-group>
								</validation-provider>

								<validation-provider
									vid="reimbursement_amount"
									name="Reimbursement Amount"
									:rules="{ required: false, min: 0, max_value: currencyMax, double: true }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Reimbursement Amount"
										label-for="reimbursement_amount"
										label-cols-lg="4"
									>
										<b-input-group>
											<b-input-group-prepend is-text>
												<font-awesome-icon icon="dollar-sign" fixed-width />
											</b-input-group-prepend>
											<b-form-input
												name="reimbursement_amount"
												type="number"
												v-model="entity.reimbursement_amount"
												:disabled="saving"
												:state="getValidationState(validationContext)"
												:min="0"
												:max="currencyMax"
												step="1.00"
												maxlength="10"
												autocomplete="off"
											/>
											<b-form-invalid-feedback
												v-for="error in validationContext.errors"
												:key="error"
												v-text="error"
											/>
										</b-input-group>
									</b-form-group>
								</validation-provider>
							</b-card-body>
						</b-collapse>

						<b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseInsurance
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
							>
								Insurance Details
							</b-button>
						</b-card-header>
						<b-collapse id="collapseInsurance" role="tabpanel">
							<b-card-body>
								<validation-provider
									vid="insurance_plan"
									name="Insurance Plan"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group label="Plan Name" label-for="insurance_plan" label-cols-lg="4">
										<b-form-input
											name="insurance_plan"
											type="text"
											v-model="entity.insurance_plan"
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

								<validation-provider
									vid="insurance_number"
									name="Insurance Plan ID / Number"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="Plan ID / Number"
										label-for="insurance_number"
										label-cols-lg="4"
									>
										<b-form-input
											name="insurance_number"
											type="text"
											v-model="entity.insurance_number"
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
							</b-card-body>
						</b-collapse>

						<b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseReadmissions
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
							>
								Readmissions
							</b-button>
						</b-card-header>
						<b-collapse id="collapseReadmissions" role="tabpanel">
							<b-card-body>
								<div class="d-flex">
									<b-button
										variant="secondary"
										@click="addReadmission"
										:disabled="!canAddReadmission"
										class="ml-auto"
									>
										<font-awesome-icon icon="plus" fixed-width />
										<span>Add Readmission</span>
									</b-button>
								</div>
							</b-card-body>
							<case-readmissions
								v-if="hasReadmissions"
								v-model="entity.case_readmissions"
								:disabled="saving"
							/>
							<b-card-body v-else>
								<empty-result icon="list"
									>No readmissions
									<template #content> Add readmissions to track multiple service dates. </template>
								</empty-result>
							</b-card-body>
						</b-collapse>

						<!-- <b-card-header header-tag="header" role="tab" class="p-0">
							<b-button
								block
								v-b-toggle.collapseClaimBillingCodes
								variant="light"
								role="tab"
								class="text-left px-4 py-3 m-0"
							>
							Claim Billing Codes
							</b-button>
						</b-card-header>
						<b-collapse id="collapseClaimBillingCodes" role="tabpanel"> -->
							<!-- <b-card-body>
								<div class="d-flex">
									<b-button
										variant="secondary"
										@click="addReadmission"
										:disabled="!canAddReadmission"
										class="ml-auto"
									>
										<font-awesome-icon icon="plus" fixed-width />
										<span>Add Codes</span>
									</b-button>
								</div>
							</b-card-body> -->
							<!-- <b-card-body>
							<validation-provider
									vid="insurance_plan"
									name="Insurance Plan"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group label="ICD-10-CM codes" label-for="insurance_plan" label-cols-lg="4">
										<b-form-input
											name="insurance_plan"
											type="text"
											v-model="entity.insurance_plan"
											:disabled="saving"
											:state="getValidationState(validationContext)"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider> -->


								<!-- <validation-provider
									vid="insurance_number"
									name="Insurance Plan ID / Number"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="CPT Codes"
										label-for="insurance_number"
										label-cols-lg="4"
									>
										<b-form-input
											name="insurance_number"
											type="text"
											v-model="entity.insurance_number"
											:disabled="saving"
											:state="getValidationState(validationContext)"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider> -->

								<!-- <validation-provider
									vid="insurance_number"
									name="Insurance Plan ID / Number"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="ICD-10-PCS codes"
										label-for="insurance_number"
										label-cols-lg="4"
									>
										<b-form-input
											name="insurance_number"
											type="text"
											v-model="entity.insurance_number"
											:disabled="saving"
											:state="getValidationState(validationContext)"
										/>
										<b-form-invalid-feedback
											v-for="error in validationContext.errors"
											:key="error"
											v-text="error"
										/>
									</b-form-group>
								</validation-provider> -->

								<!-- <validation-provider
									vid="insurance_number"
									name="Insurance Plan ID / Number"
									:rules="{ required: false, max: 50 }"
									v-slot="validationContext"
								>
									<b-form-group
										label="HCPCS codes"
										label-for="insurance_number"
										label-cols-lg="4"
									>
										<b-form-input
											name="insurance_number"
											type="text"
											v-model="entity.insurance_number"
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
							</b-card-body> -->

							<!-- <case-readmissions
								v-if="hasReadmissions"
								v-model="entity.case_readmissions"
								:disabled="saving"
							/>
							<b-card-body v-else>
								<empty-result icon="list"
									>No Codes
									<template #content> Add Billing Codes. </template>
								</empty-result>
							</b-card-body> -->
						<!-- </b-collapse> -->
					</b-card>
				</b-card-body>

				<b-card-footer>
					<b-row>
						<b-col cols="12" md="6" xl="4" class="mb-4 mb-md-0">
							<b-button v-if="!hideCancel" block variant="light" @click="cancel">Cancel</b-button>
						</b-col>
						<b-col cols="12" md="6" offset-xl="4" xl="4">
							<b-button
								block
								variant="primary"
								type="submit"
								:disabled="saving || loading || !hasPatient"
								:title="invalid ? 'Please fix any validation errors' : 'Save'"
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
import { getTodaysDate, getAbsoluteMinimumDate } from "@/shared/helpers/dateHelper";

import CaseReadmissions from "./Readmissions.vue";
import PatientForm from "@/clients/components/Patients/Form.vue";
import PatientSearch from "@/clients/components/Search/Patients.vue";

import InsuranceProviderForm from "@/clients/components/InsuranceProviders/Form.vue";
import DenialReasonSearchMulti from "@/clients/components/Search/DenialReasonsMulti.vue";
import DenialReasonForm from "@/clients/components/DenialReasons/Form.vue";
import ClientEmployeeForm from "@/clients/components/ClientEmployees/Form.vue";
import FacilityForm from "@/clients/components/Facilities/AddForm.vue";

export default {
	name: "CaseForm",
	props: {
		id: {
			type: [Number, String],
			default: null,
		},
		patientId: {
			type: [Number, String],
			default: null,
		},
		hideCancel: {
			type: Boolean,
			default: false,
		},
		hidePatient: {
			type: Boolean,
			default: false,
		},
		facilityId: {
			type: [Number, String],
			default: null,
		},
		clientEmployeeId: {
			type: [Number, String],
			default: null,
		},
		currentDocument: {
			default: () => {
				return {};
			},
		},
		readmissionLimit: {
			type: Number,
			default: 5,
		},
		currencyMax: {
			type: Number,
			default: 999999999,
		},
		flush: {
			type: Boolean,
			default: false,
		},
	},
	components: {
		CaseReadmissions,
		PatientForm,
		PatientSearch,
		InsuranceProviderForm,
		DenialReasonSearchMulti,
		DenialReasonForm,
		ClientEmployeeForm,
		FacilityForm,
	},
	data() {
		return {
			loading: true,
			saving: false,
			currentPatient: {},
			currentInsuranceProvider: {},
			addingPatient: false,
			editingPatient: false,
			addingInsuranceProvider: false,
			addingClientEmployee: false,
			addingFacility: false,
			entity: {
				id: null,
				case_type_id: null,
				denial_type_id: null,
				client_employee_id: null,
				admit_date: null,
				discharge_date: null,
				insurance_type_id: null,
				case_readmissions: [],
				facility_id: null,
				visit_number: null,
				total_claim_amount: null,
				disputed_amount: null,
				reimbursement_amount: null,
				insurance_provider_id: null,
				insurance_plan: null,
				insurance_number: null,
				discipline_id: null, // Unused, join table now
				disciplines: [],
				unable_to_complete: false,
				assigned: null,
				assigned_to: null,
			},
			currentDenialReasons: [],
			addingDenialReason: false,
			minDate: getAbsoluteMinimumDate(),
			maxDate: getTodaysDate(),
			disciplineIds: [],
		};
	},
	computed: {
		hasPatient() {
			return this.currentPatient && this.currentPatient.id ? true : false;
		},
		hasClientEmployees() {
			return this.clientEmployees && this.clientEmployees.length > 0;
		},
		canAddReadmission() {
			if (!this.entity.case_readmissions || !this.entity.case_readmissions.length) {
				return true;
			}

			// Limit number of readmissions
			return this.entity.case_readmissions.length < this.readmissionLimit;
		},
		hasReadmissions() {
			return this.entity.case_readmissions && this.entity.case_readmissions.length > 0;
		},
		availableInsuranceTypes() {
			if (this.currentInsuranceProvider) {
				if (
					this.currentInsuranceProvider.insurance_types &&
					this.currentInsuranceProvider.insurance_types.length > 0
				) {
					return this.currentInsuranceProvider.insurance_types;
				}
			}

			return this.insuranceTypes;
		},
		...mapGetters({
			currentUserId: "userId",
			apiToken: "apiToken",
			loadingUsers: "users/loadingActive",
			users: "users/active",
			caseTypes: "caseTypes/all",
			loadingCaseTypes: "caseTypes/loadingAll",
			caseOutcomes: "caseOutcomes/all",
			loadingCaseOutcomes: "caseOutcomes/loadingAll",
			clientEmployees: "clientEmployees/active",
			loadingClientEmployees: "clientEmployees/loadingActive",
			denialTypes: "denialTypes/all",
			loadingDenialTypes: "denialTypes/loadingAll",
			disciplines: "disciplines/all",
			loadingDisciplines: "disciplines/loadingAll",
			insuranceProviders: "insuranceProviders/active",
			loadingInsuranceProviders: "insuranceProviders/loadingActive",
			insuranceTypes: "insuranceTypes/all",
			loadingInsuranceTypes: "insuranceTypes/loadingAll",
			facilities: "facilities/active",
			loadingFacilities: "facilities/loadingActive",
		}),
	},
	mounted() {
		if (this.id) {
			this.refresh();
		} else {
			this.loading = false;
			this.entity.assigned_to = this.currentUserId;
		}

		if (this.facilityId) {
			this.entity.facility_id = this.facilityId;
		}

		if (this.clientEmployeeId) {
			this.entity.client_employee_id = this.clientEmployeeId;
		}

		// Default case type
		if (this.entity.case_type_id === null) {
			if (this.caseTypes && this.caseTypes.length > 0) {
				this.entity.case_type_id = this.caseTypes[0].id;
			}
		}
	},
	methods: {
		getValidationState,
		cancel() {
			this.$emit("cancel");
		},
		async refresh() {
			if (!this.id) {
				return false;
			}

			try {
				this.loading = true;

				const response = await this.$store.dispatch("cases/get", {
					id: this.id,
				});

				this.$set(this, "entity", response);
				this.currentPatient = response.patient || {};

				this.currentInsuranceProvider = response.insurance_provider || {};

				if (this.entity.denial_reasons) {
					this.currentDenialReasons = this.entity.denial_reasons;
				}

				if (response.disputed_amount) {
					this.entity.disputed_amount = parseFloat(response.disputed_amount).toFixed(2);
				}

				if (this.entity.disciplines) {
					this.disciplineIds = this.entity.disciplines.map((item) => item.id);
				}

				this.$emit("loaded", this.entity);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Error getting case details",
				});
			} finally {
				this.loading = false;
				this.initialLoaded = true;
			}
		},
		async save(e) {
			try {
				this.saving = true;

				let entity = {
					id: this.id || null,
					case_type_id: this.entity.case_type_id,
					patient_id: this.entity.patient_id,
					facility_id: this.entity.facility_id,
					denial_type_id: this.entity.denial_type_id,
					case_outcome_id: this.entity.case_outcome_id,
					client_employee_id: this.entity.client_employee_id,

					insurance_provider_id: this.entity.insurance_provider_id,
					insurance_type_id: this.entity.insurance_type_id,
					insurance_plan: this.entity.insurance_plan,
					insurance_number: this.entity.insurance_number,

					total_claim_amount: Number.parseFloat(this.entity.total_claim_amount),
					disputed_amount: Number.parseFloat(this.entity.disputed_amount),
					reimbursement_amount: Number.parseFloat(this.entity.reimbursement_amount),
					visit_number: this.entity.visit_number,

					admit_date: this.entity.admit_date,
					discharge_date: this.entity.discharge_date,

					unable_to_complete: this.entity.unable_to_complete,

					case_readmissions: this.entity.case_readmissions,

					assigned_to: this.entity.assigned_to,

					// cannot use _ids here otherwise the ordering won't be perserved
					denial_reasons: this.currentDenialReasons.map((item) => {
						return { id: item.id };
					}),

					disciplines: {
						_ids: this.disciplineIds,
					},
				};

				if (this.currentDocument && this.currentDocument.id) {
					entity.attach_document_id = this.currentDocument.id;
				}

				const response = await this.$store.dispatch("cases/save", entity);

				this.$emit("saved", response);
				this.$emit("update:id", response.id);
			} catch (e) {
				if (e.response.data.errors) {
					this.$refs.observer.setErrors(formatErrors(e.response.data.errors));
				}

				this.$store.dispatch("apiError", {
					error: e,
					title: "Save Failed",
					message: "Error saving case details. Please check for errors.",
					variant: "warning",
				});
			} finally {
				this.saving = false;

				this.$store.dispatch("updateState");
			}
		},
		setPatient(patient) {
			this.addingPatient = false;
			this.currentPatient = patient;
		},
		addedNewPatient(patient) {
			this.addingPatient = false;
			this.currentPatient = patient;
		},
		editPatient() {
			this.editingPatient = this.currentPatient.id;
		},
		savedPatient(patient) {
			this.editingPatient = false;
			this.currentPatient = patient;
		},
		async getPatient(patientId) {
			try {
				const response = await this.$store.dispatch("patients/get", {
					id: patientId,
				});

				this.currentPatient = response;
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					message: "Error getting patient details",
				});
			}
		},
		addReadmission() {
			if (!this.entity.case_readmissions) {
				this.$set(this.entity, "case_readmissions", []);
			}

			this.entity.case_readmissions.push({
				// Defaults
			});
		},
		addedNewInsuranceProvider(insuranceProvider) {
			this.$store.dispatch("insuranceProviders/getAll");
			this.addingInsuranceProvider = false;
			this.currentInsuranceProvider = insuranceProvider;
		},
		async changedInsuranceProvider(id) {
			if (!id || id == null) {
				this.currentInsuranceProvider = {};
				return;
			}

			// @todo Include insurance types to avoid roundtrip to server

			// const match = this.insuranceProviders.find(
			// 	(entity) => entity.id == id
			// );
			// console.log(match);
			// this.currentInsuranceProvider = match;

			const response = await this.$store.dispatch("insuranceProviders/get", {
				id: id,
			});

			this.currentInsuranceProvider = response;
		},
		addedNewDenialReason(denialReason) {
			this.addingDenialReason = false;
			this.currentDenialReasons.push(denialReason);
		},
		addedClientEmployee(employee) {
			this.addingClientEmployee = false;
			this.$store.dispatch("clientEmployees/getAll");
		},
		addedFacility(facility) {
			this.addingFacility = false;
			this.entity.facility_id = facility.id;
			this.$store.dispatch("facilities/getActive");
		},
	},
	watch: {
		patientId: {
			immediate: true,
			handler(val) {
				if (val !== null) {
					this.entity.patient_id = val;
					this.getPatient(val);
				}
			},
		},
		currentPatient(newVal) {
			if (newVal && newVal.id) {
				this.entity.patient_id = newVal.id;
			}
		},
		currentInsuranceProvider(newVal) {
			if (newVal && newVal.id) {
				//this.$set(this.entity, 'insurance_provider_id', newVal.id);
				this.entity.insurance_provider_id = newVal.id;
			}

			if (newVal && newVal.default_insurance_type_id) {
				if (!this.entity.insurance_type_id || this.entity.insurance_type_id == null) {
					this.entity.insurance_type_id = newVal.default_insurance_type_id;
				}
			}
		},
	},
};
</script>
