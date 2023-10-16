<template>
	<b-row>
		<b-col cols="12" lg="12">
			<b-card class="mb-4" header="User Performance">
				<b-row>
					<b-col cols="12" lg="6">
						<validation-provider
							vid="start_date"
							name="start Date"
							:rules="{ required: true }"
							v-slot="validationContext"
						>
							<b-form-group label="Start Date" label-for="start_date" label-cols-md="4" label-cols-lg="4">
								<b-form-input
									name="start_date"
									type="date"
									v-model="startDate"
									:disabled="disabled"
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
					<b-col cols="12" lg="6">
						<validation-provider
							vid="end_date"
							name="End Date"
							:rules="{ required: true }"
							v-slot="validationContext"
						>
							<b-form-group label="End Date" label-for="end_date" label-cols-md="4" label-cols-lg="4">
								<b-form-input
									name="end_date"
									type="date"
									v-model="endDate"
									:disabled="disabled"
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
				</b-row>
				<b-row>
					<b-col cols="12">
						Searching from {{ $filters.fromNow(range.start) }} to {{ $filters.fromNow(range.end) }}
					</b-col>
				</b-row>
			</b-card>
		</b-col>
	</b-row>
</template>

<script>
import { getValidationState } from "@/validation";
import { getTodaysDate, getDateOffsetDaysString } from "@/shared/helpers/dateHelper";

export default {
	name: "ViewReportIndexUsers",
	components: {},
	data() {
		return {
			disabled: false,
			startDate: getDateOffsetDaysString(-30),
			endDate: getTodaysDate(),
		};
	},
	computed: {
		range() {
			return {
				start: this.startDate,
				end: this.endDate,
			};
		},
	},
	methods: {
		getValidationState,
		fetch(range) {
			console.log("Range Updated", range);
		},
	},
	watch: {
		range: {
			immediate: false,
			handler(val) {
				this.fetch(val);
			},
		},
	},
};
</script>
