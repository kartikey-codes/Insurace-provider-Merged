<template>
	<b-row>
		<b-col cols="12" sm="6" lg="6" xl="4">
			<b-form-group label="Active" label-for="active" label-cols-lg="4">
				<b-form-select
					v-model="filters.active"
					v-bind="{ disabled }"
					:options="activeOptions"
					value-field="value"
					text-field="text"
				/>
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
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "AuditReviewerFilters",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					active: null,
					agency_id: null,
				};
			},
		},
		disabled: {
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
			agencies: "agencies/active",
		}),
	},
	data() {
		return {
			activeOptions: [
				{ text: "(All)", value: null },
				{ text: "Yes", value: true },
				{ text: "No", value: false },
			],
		};
	},
};
</script>
