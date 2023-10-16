<template>
	<b-form @submit.prevent>
		<b-row>
			<b-col v-if="!hideStatus" cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Status" label-for="status" label-cols-lg="4">
					<b-form-select
						name="status"
						v-model="filters.status"
						:disabled="disabled"
						:options="appealStatuses"
						text-field="name"
						value-field="value"
					>
						<template #first>
							<option :value="null">(All)</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Level" label-for="appeal_level_id" label-cols-lg="4">
					<b-form-select
						name="appeal_level_id"
						v-model="filters.appeal_level_id"
						:disabled="disabled"
						:options="appealLevels"
						value-field="id"
						text-field="display_name"
					>
						<template #first>
							<option :value="null">(All)</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Appeal Type" label-for="appeal_type_id" label-cols-lg="4">
					<b-form-select
						name="appeal_type_id"
						v-model="filters.appeal_type_id"
						:disabled="disabled"
						:options="appealTypes"
						value-field="id"
						text-field="name"
					>
						<template #first>
							<option :value="null">(All)</option>
							<option :value="false">(None)</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Denial Type" label-for="denial_type_id" label-cols-lg="4">
					<b-form-select
						name="denial_type_id"
						v-model="filters.denial_type_id"
						:disabled="disabled"
						:options="denialTypes"
						value-field="id"
						text-field="name"
					>
						<template #first>
							<!-- this slot appears above the options from 'options' prop -->
							<option :value="null">(All)</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Facility" label-for="facility_id" label-cols-lg="4">
					<b-form-select
						name="facility_id"
						v-model="filters.facility_id"
						:disabled="disabled"
						:options="facilities"
						value-field="id"
						text-field="name"
					>
						<template #first>
							<!-- this slot appears above the options from 'options' prop -->
							<option :value="null">(All)</option>
							<option v-if="!facilities.length > 0" :value="null" disabled>No facilities added.</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Visit Number" label-for="visit_number" label-cols-lg="4">
					<b-form-input
						type="text"
						name="visit_number"
						v-model="filters.visit_number"
						:disabled="disabled"
						lazy
					/>
				</b-form-group>
			</b-col>
			<b-col v-if="!hideAgency" cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Agency" label-for="agency_id" label-cols-lg="4">
					<b-form-select
						name="agency_id"
						v-model="filters.agency_id"
						:disabled="disabled"
						:options="agencies"
						value-field="id"
						text-field="name"
					>
						<template #first>
							<option :value="null">(All)</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Insurance Provider" label-for="insurance_provider_id" label-cols-lg="4">
					<b-form-select
						name="insurance_provider_id"
						v-model="filters.insurance_provider_id"
						:disabled="disabled"
						:options="insuranceProviders"
						value-field="id"
						text-field="name"
					>
						<template #first>
							<option :value="null">(All)</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Insurance Type" label-for="insurance_type_id" label-cols-lg="4">
					<b-form-select
						name="insurance_type_id"
						v-model="filters.insurance_type_id"
						:disabled="disabled"
						:options="insuranceTypes"
						value-field="id"
						text-field="name"
					>
						<template #first>
							<!-- this slot appears above the options from 'options' prop -->
							<option :value="null">(All)</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Insurance Plan" label-for="insurance_plan" label-cols-lg="4">
					<b-form-input name="insurance_plan" v-model="filters.insurance_plan" :disabled="disabled" lazy />
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Insurance Number" label-for="insurance_number" label-cols-lg="4">
					<b-form-input
						name="insurance_number"
						v-model="filters.insurance_number"
						:disabled="disabled"
						lazy
					/>
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Outcome" label-for="case_outcome_id" label-cols-lg="4">
					<b-form-select
						name="case_outcome_id"
						v-model="filters.case_outcome_id"
						:disabled="disabled"
						:options="caseOutcomes"
						value-field="id"
						text-field="name"
					>
						<template #first>
							<!-- this slot appears above the options from 'options' prop -->
							<option :value="null">(All)</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
			<b-col v-if="!hideAssignedTo" cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Assigned To" label-for="assigned_to" label-cols-lg="4">
					<b-form-select
						name="assigned_to"
						v-model="filters.assigned_to"
						:disabled="disabled"
						:options="activeUsers"
						value-field="id"
						text-field="full_name"
					>
						<template #first>
							<option :value="null">(All)</option>
							<option :value="false">(Unassigned)</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Admit Date" label-for="admit_date" label-cols-lg="4">
					<b-form-input name="admit_date" type="date" v-model="filters.admit_date" :disabled="disabled" />
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Discharge Date" label-for="discharge_date" label-cols-lg="4">
					<b-form-input
						name="discharge_date"
						type="date"
						v-model="filters.discharge_date"
						:disabled="disabled"
					/>
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Defendable" label-for="defendable" label-cols-lg="4">
					<b-form-select
						name="defendable"
						v-model="filters.defendable"
						:disabled="disabled"
						:options="defendableOptions"
						value-field="value"
						text-field="name"
					>
						<template #first>
							<option :value="null">(All)</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Priority" label-for="priority" label-cols-lg="4">
					<b-form-select
						name="priority"
						v-model="filters.priority"
						:disabled="disabled"
						:options="priorityOptions"
					/>
				</b-form-group>
			</b-col>
		</b-row>
	</b-form>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "AppealFilters",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					status: null,
					denial_type_id: null,
					facility_id: null,
					visit_number: null,
					insurance_provider_id: null,
					insurance_type_id: null,
					insurance_plan: null,
					insurance_number: null,
					appeal_level_id: null,
					appeal_type_id: null,
					case_outcome_id: null,
					assigned_to: null,
					admit_date: null,
					discharge_date: null,
					defendable: null,
					audit_reviewer_id: null,
					agency_id: null,
					unable_to_complete: null,
				};
			},
		},
		disabled: {
			type: Boolean,
			default: false,
		},
		hideAgency: {
			type: Boolean,
			default: false,
		},
		hideAssignedTo: {
			type: Boolean,
			default: false,
		},
		hideStatus: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		filters: {
			get() {
				return this.value;
			},
			set(val) {
				this.$emit("input", val);
			},
		},
		...mapGetters({
			activeUsers: "users/active",
			agencies: "agencies/active",
			appealStatuses: "appeals/statuses",
			appealTypes: "appealTypes/all",
			appealLevels: "appealLevels/all",
			caseStatuses: "cases/statuses",
			caseTypes: "caseTypes/all",
			caseOutcomes: "caseOutcomes/all",
			defendableOptions: "appeals/defendableOptions",
			denialTypes: "denialTypes/all",
			facilities: "facilities/active",
			insuranceProviders: "insuranceProviders/active",
			insuranceTypes: "insuranceTypes/all",
		}),
	},
	data() {
		return {
			priorityOptions: [
				{ text: "(All)", value: null },
				{ text: "Yes", value: true },
				{ text: "No", value: false },
			],
		};
	},
};
</script>
