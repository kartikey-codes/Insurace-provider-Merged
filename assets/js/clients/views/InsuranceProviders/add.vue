<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'insuranceProviders' }" v-text="`Insurance Providers /`" />
				<span>Add</span>
			</template>
		</page-header>

		<b-row class="my-4">
			<b-col cols="12">
				<AddForm @cancel="toIndex" @saved="toView" />
			</b-col>
		</b-row>
	</div>
</template>

<script type="text/javascript">
import AddForm from "@/clients/components/InsuranceProviders/Form.vue";

export default {
	name: "ViewAddInsuranceProvider",
	components: {
		AddForm,
	},
	data() {
		return {};
	},
	methods: {
		toIndex() {
			this.$router.push({
				name: "insuranceProviders",
			});
		},
		toView(entity) {
			this.$router.push({
				name: "insuranceProviders.view",
				params: {
					id: entity.id,
				},
			});
		},
	},
};
</script>
