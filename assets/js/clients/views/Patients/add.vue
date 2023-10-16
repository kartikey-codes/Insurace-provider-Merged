<template>
	<div>
		<page-header :fluid="false">
			<template #title>
				<router-link :to="{ name: 'patients' }" v-text="`Patients /`" />
				<span>Add</span>
			</template>
		</page-header>

		<b-container class="my-4">
			<b-row>
				<b-col cols="12" class="mb-5">
					<AddForm @cancel="toIndex" @saved="toView" />
				</b-col>
			</b-row>
		</b-container>
	</div>
</template>

<script type="text/javascript">
import AddForm from "@/clients/components/Patients/Form.vue";

export default {
	name: "ViewAddPatient",
	components: {
		AddForm,
	},
	data() {
		return {};
	},
	methods: {
		toIndex() {
			this.$router.push({
				name: "patients",
			});
		},
		toView(entity) {
			this.$router.push({
				name: "patients.view",
				params: {
					id: entity.id,
				},
			});
		},
	},
};
</script>
