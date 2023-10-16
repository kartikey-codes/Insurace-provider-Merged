<template>
	<div>
		<page-header :fluid="false">
			<template #title>
				<router-link :to="{ name: 'cases' }" v-text="`Cases /`" />
				<span>Add</span>
			</template>
		</page-header>

		<b-container class="my-4">
			<b-row>
				<b-col cols="12" class="mb-5">
					<AddForm
						ref="caseForm"
						v-model="entity"
						:client-employee-id="entity.client_employee_id"
						:facility-id="entity.facility_id"
						@cancel="toIndex"
						@saved="toView"
					/>
				</b-col>
			</b-row>
		</b-container>
	</div>
</template>

<script type="text/javascript">
import AddForm from "@/clients/components/Cases/Form.vue";

export default {
	name: "ViewAddCase",
	components: {
		AddForm,
	},
	data() {
		return {
			entity: {
				id: null,
				case_type_id: null,
				patient_id: this.$route.query.patient_id || null,
				denial_type_id: null,
				client_employee_id: this.$route.query.physician_id || null,
				admit_date: null,
				discharge_date: null,
				insurance_type_id: null,
				case_readmissions: [],
				facility_id: this.$route.query.facility_id || null,
				visit_number: null,
				total_claim_amount: null,
				disputed_amount: null,
				reimbursement_amount: null,
				insurance_provider_id: this.$route.query.insurance_provider_id || null,
				insurance_plan: null,
				insurance_number: null,
			},
			loadingPatient: false,
		};
	},
	mounted() {
		if (this.$route.query.patient_id && this.$route.query.patient_id > 0) {
			this.getPatient(this.$route.query.patient_id);
		}
	},
	methods: {
		async getPatient(id) {
			try {
				this.loadingPatient = true;
				const response = await this.$store.dispatch("patients/get", { id });
				this.entity.patient_id = response.id;

				this.$refs.caseForm.setPatient(response);
			} finally {
				this.loadingPatient = false;
			}
		},
		toIndex() {
			this.$router.push({
				name: "cases",
			});
		},
		toView(entity) {
			this.$router.push({
				name: "cases.view",
				params: {
					id: entity.id,
				},
			});
		},
	},
};
</script>
