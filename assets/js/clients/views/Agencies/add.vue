<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'agencies' }" v-text="`Agencies /`" />
				<span>Add</span>
			</template>
		</page-header>

		<b-row class="my-4">
			<b-col cols="12">
				<AddForm v-model="entity" @cancel="toIndex" @saved="toView" />
			</b-col>
		</b-row>
	</div>
</template>

<script type="text/javascript">
import AddForm from "@/clients/components/Agencies/Form.vue";

export default {
	name: "ViewAddAgency",
	components: {
		AddForm,
	},
	data() {
		return {
			entity: {
				id: null,
				name: "",
				active: true,
				third_party_contractor: false,
			},
		};
	},
	methods: {
		toIndex() {
			this.$router.push({
				name: "agencies",
			});
		},
		toView(entity) {
			this.$router.push({
				name: "agencies.view",
				params: {
					id: entity.id,
				},
			});
		},
	},
};
</script>
