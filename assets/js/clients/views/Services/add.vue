<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'services' }" v-text="`Services /`" />
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
import AddForm from "@/clients/components/Services/Form.vue";

export default {
	name: "ViewAddService",
	components: {
		AddForm,
	},
	data() {
		return {
			entity: {
				id: null,
				name: "",
				active: true,
			},
		};
	},
	methods: {
		toIndex() {
			this.$router.push({
				name: "services",
			});
		},
		toView(entity) {
			this.$router.push({
				name: "services.view",
				params: {
					id: entity.id,
				},
			});
		},
	},
};
</script>
