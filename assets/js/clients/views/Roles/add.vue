<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'roles' }" v-text="`Roles /`" />
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
import AddForm from "@/clients/components/Roles/Form.vue";

export default {
	name: "ViewAddRole",
	components: {
		AddForm,
	},
	data() {
		return {
			entity: {
				id: null,
				name: "",
			},
		};
	},
	methods: {
		toIndex() {
			this.$router.push({
				name: "roles",
			});
		},
		toView(entity) {
			this.$router.push({
				name: "roles.view",
				params: {
					id: entity.id,
				},
			});
		},
	},
};
</script>
