<template>
	<b-form @submit.prevent>
		<b-card class="shadow-sm">
			<b-row>
				<b-col cols="12" sm="6" lg="6" xl="4">
					<b-form-group label-cols-lg="4" label="Status" label-for="status">
						<b-form-select
							name="status"
							v-model="localValue.status"
							:disabled="disabled"
							:options="appealStatuses"
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
							v-model="localValue.appeal_level_id"
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
				<b-col cols="12" sm="6" lg="6" xl="4">
					<b-form-group id="appealType" label-cols-lg="4" label="Appeal Type" label-for="appealType">
						<b-form-select
							v-model="localValue.appeal_type_id"
							:disabled="disabled"
							:options="appealTypes"
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
		</b-card>
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
					appeal_level_id: null,
					appeal_type_id: null,
				};
			},
		},
		disabled: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		localValue: {
			get() {
				return this.value;
			},
			set(val) {
				this.$emit("input", val);
			},
		},
		...mapGetters({
			appealStatuses: "appeals/statuses",
			appealLevels: "appealLevels/all",
			appealTypes: "appealTypes/all",
			/*
			activeUsers: "users/active",
			caseStatuses: "caseStatuses/all",
			caseTypes: "caseTypes/all",
			caseOutcomes: "caseOutcomes/all",
			denialTypes: "denialTypes/all",
			facilities: "facilities/active",
			insuranceProviders: "insuranceProviders/active",
			insuranceTypes: "insuranceTypes/all",
			*/
			defendableOptions: "defendableOptions",
		}),
	},
};
</script>
