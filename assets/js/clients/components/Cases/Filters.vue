<template>
	<b-form @submit.prevent>
		<b-row>
			<b-col v-if="!hideStatus" cols="12" sm="6" lg="6" xl="4">
				<b-form-group label-cols-lg="4" label="Status" label-for="status">
					<b-form-select
						name="status"
						v-model="filters.status"
						:disabled="disabled"
						:options="caseStatuses"
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
				<b-form-group label-cols-lg="4" label="Case Type" label-for="case_type_id">
					<b-form-select
						name="case_type_id"
						v-model="filters.case_type_id"
						:disabled="disabled"
						:options="caseTypes"
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
				<b-form-group label-cols-lg="4" label="Primary Physician" label-for="client_employee_id">
					<b-form-select
						name="client_employee_id"
						v-model="filters.client_employee_id"
						:disabled="disabled"
						:options="clientEmployees"
						value-field="id"
						text-field="full_name"
					>
						<template #first>
							<option :value="null">(All)</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label-cols-lg="4" label="Denial Type" label-for="denial_type_id">
					<b-form-select
						name="denial_type_id"
						v-model="filters.denial_type_id"
						:disabled="disabled"
						:options="denialTypes"
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
				<b-form-group label="Denial Reason" label-for="denial_reason_id" label-cols-lg="4">
					<b-form-select
						name="denial_reason_id"
						v-model="filters.denial_reason_id"
						:disabled="disabled"
						:options="denialReasons"
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
				<b-form-group label-cols-lg="4" label="Facility" label-for="facility_id">
					<b-form-select
						name="facility_id"
						v-model="filters.facility_id"
						:disabled="disabled"
						:options="facilities"
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
				<b-form-group label-cols-lg="4" label="Appeal Level" label-for="appeal_level_id">
					<b-form-select
						name="appeal_level_id"
						v-model="filters.appeal_level_id"
						:disabled="disabled"
						:options="appealLevels"
						value-field="id"
						text-field="name"
					>
						<template #first>
							<option :value="null">(All)</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
			<b-col v-if="!hideOutcome" cols="12" sm="6" lg="6" xl="4">
				<b-form-group label-cols-lg="4" label="Outcome" label-for="case_outcome_id">
					<b-form-select
						name="case_outcome_id"
						v-model="filters.case_outcome_id"
						:disabled="disabled"
						:options="caseOutcomes"
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
				<b-form-group label-cols-lg="4" label="Insurance Provider" label-for="insurance_provider_id">
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
				<b-form-group label-cols-lg="4" label="Insurance Type" label-for="insurance_type_id">
					<b-form-select
						name="insurance_type_id"
						v-model="filters.insurance_type_id"
						:disabled="disabled"
						:options="insuranceTypes"
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
				<b-form-group label-cols-lg="4" label="Insurance Plan" label-for="insurance_plan">
					<b-form-input name="insurance_plan" v-model="filters.insurance_plan" :disabled="disabled" lazy />
				</b-form-group>
			</b-col>

			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label-cols-lg="4" label="Assigned To" label-for="assigned_to">
					<b-form-select
						name="assigned_to"
						v-model="filters.assigned_to"
						:options="activeUsers"
						:disabled="disabled"
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
				<b-form-group label-cols-lg="4" label="Admit Date" label-for="admit_date">
					<b-form-input name="admit_date" type="date" v-model="filters.admit_date" :disabled="disabled" />
				</b-form-group>
			</b-col>
			<b-col cols="12" sm="6" lg="6" xl="4">
				<b-form-group label-cols-lg="4" label="Discharge Date" label-for="discharge_date">
					<b-form-input
						name="discharge_date"
						type="date"
						v-model="filters.discharge_date"
						:disabled="disabled"
					/>
				</b-form-group>
			</b-col>
			<b-col v-if="!hideEmpty" cols="12" sm="6" lg="6" xl="4">
				<b-form-group label-cols-lg="4" label="Case Empty" label-for="case_empty">
					<b-form-select
						name="case_empty"
						v-model="filters.empty"
						:disabled="disabled"
						:options="emptyOptions"
						value-field="value"
						text-field="name"
					>
						<template #first>
							<option :value="null">(All)</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
			<b-col v-if="!hideUTC" cols="12" sm="6" lg="6" xl="4">
				<b-form-group label-cols-lg="4" label="Unable To Complete" label-for="unable_to_complete">
					<b-form-select
						name="unable_to_complete"
						v-model="filters.unable_to_complete"
						:disabled="disabled"
						:options="utcOptions"
						value-field="value"
						text-field="text"
					>
						<template #first>
							<option :value="null">(All)</option>
						</template>
					</b-form-select>
				</b-form-group>
			</b-col>
		</b-row>
	</b-form>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import { debounce } from "lodash";

export default {
	name: "CaseFilters",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					status: null,
					case_type_id: null,
					client_employee_id: null,
					denial_type_id: null,
					denial_reason_id: null,
					facility_id: null,
					appeal_level_id: null,
					case_outcome_id: null,
					insurance_provider_id: null,
					insurance_type_id: null,
					insurance_plan: null,
					admit_date: null,
					discharge_date: null,
					empty: null,
					assigned_to: null,
					unable_to_complete: null,
				};
			},
		},
		disabled: {
			type: Boolean,
			default: false,
		},
		hideEmpty: {
			type: Boolean,
			default: false,
		},
		hideStatus: {
			type: Boolean,
			default: false,
		},
		hideOutcome: {
			type: Boolean,
			default: false,
		},
		hideUTC: {
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
			appealLevels: "appealLevels/all",
			caseStatuses: "cases/statuses",
			caseTypes: "caseTypes/all",
			caseOutcomes: "caseOutcomes/all",
			clientEmployees: "clientEmployees/active",
			caseOutcomes: "caseOutcomes/all",
			denialTypes: "denialTypes/all",
			denialReasons: "denialReasons/all",
			facilities: "facilities/active",
			insuranceProviders: "insuranceProviders/active",
			insuranceTypes: "insuranceTypes/all",
		}),
	},
	data() {
		return {
			emptyOptions: [
				{
					value: true,
					name: "Yes",
				},
				{
					value: false,
					name: "No",
				},
			],
			utcOptions: [
				{ text: "Yes", value: true },
				{ text: "No", value: false },
			],
		};
	},
};
</script>
