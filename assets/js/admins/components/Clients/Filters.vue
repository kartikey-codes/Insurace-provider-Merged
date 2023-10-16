<template>
	<b-form @submit.prevent>
		<b-card>
			<b-row>
				<b-col sm="6" md="4">
					<b-form-group id="name" label="Name" label-for="name" label-cols-lg="4">
						<b-form-input v-model="value.name" @change="changed" :disabled="disabled" />
					</b-form-group>
				</b-col>
				<b-col sm="6" md="4">
					<b-form-group id="status" label="Status" label-for="status" label-cols-lg="4">
						<b-form-select
							v-model="value.status"
							@change="changed"
							:disabled="disabled"
							:options="statuses"
							value-field="value"
							text-field="name"
						>
							<template #first>
								<!-- this slot appears above the options from 'options' prop -->
								<option :value="null">(All)</option>
							</template>
						</b-form-select>
					</b-form-group>
				</b-col>
				<b-col sm="6" md="4">
					<b-form-group id="active" label="Active" label-for="active" label-cols-lg="4">
						<b-form-select
							v-model="value.active"
							:disabled="disabled"
							@change="changed"
							:options="activeOptions"
							value-field="value"
							text-field="name"
						>
							<template #first>
								<!-- this slot appears above the options from 'options' prop -->
								<option :value="null">(All)</option>
							</template>
						</b-form-select>
					</b-form-group>
				</b-col>
				<b-col sm="6" md="4">
					<b-form-group id="state" label="State" label-for="state" label-cols-lg="4">
						<b-form-select
							v-model="value.state"
							@change="changed"
							:disabled="disabled"
							:options="states"
							value-field="abbreviation"
							text-field="name"
						>
							<template #first>
								<!-- this slot appears above the options from 'options' prop -->
								<option :value="null">(All)</option>
							</template>
						</b-form-select>
					</b-form-group>
				</b-col>
			</b-row>
		</b-card>
		<b-row class="my-2">
			<b-col cols="12" lg="4" xl="2">
				<b-button block variant="secondary" @click="$emit('close')" class="py-2 px-4">
					<font-awesome-icon icon="chevron-up" fixed-width />Close Filters
				</b-button>
			</b-col>
		</b-row>
	</b-form>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

export default {
	name: "ClientFilters",
	props: ["value", "disabled"],
	computed: {
		...mapGetters({
			states: "states/states",
			statuses: "clients/statuses",
		}),
	},
	data() {
		return {
			activeOptions: [
				{
					value: 1,
					name: "Active",
				},
				{
					value: 0,
					name: "Inactive",
				},
			],
		};
	},
	methods: {
		changed() {
			this.$emit("changed", this.value);
		},
	},
};
</script>
