<template>
	<b-form @submit.prevent>
		<b-row>
			<b-col v-if="!hideStatus" cols="12" sm="6" lg="6" xl="4">
				<b-form-group label="Status" label-for="status" label-cols-lg="4">
					<b-form-select
						name="status"
						v-model="filters.status"
						:disabled="disabled"
						:options="statuses"
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
				<b-form-group label="Type" label-for="request_type" label-cols-lg="4">
					<b-form-select
						name="request_type"
						v-model="filters.request_type"
						:disabled="disabled"
						:options="types"
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
						v-bind="{ disabled }"
						:options="priorityOptions"
					/>
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
		</b-row>
	</b-form>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "CaseRequestFilters",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					status: null,
					request_type: null,
					assigned_to: null,
					insurance_provider_id: null,
					agency_id: null,
					priority: null,
				};
			},
		},
		disabled: {
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
			statuses: "caseRequests/statuses",
			types: "caseRequests/types",
			agencies: "agencies/active",
			insuranceProviders: "insuranceProviders/active",
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
