<template>
	<div>
		<page-header :fluid="false">
			<template #title>
				<router-link :to="{ name: 'cases' }" v-text="`Cases /`" />
				<router-link :to="{ name: 'cases.view', params: { id: $route.params.id } }" v-text="name" />
				<span>/&nbsp;Edit</span>
			</template>
		</page-header>

		<b-container class="my-4">
			<b-row>
				<b-col cols="12" class="mb-5">
					<EditForm :id="$route.params.id" @loaded="loaded" @cancel="toView" @saved="toView" />
				</b-col>
			</b-row>
		</b-container>
	</div>
</template>

<script type="text/javascript">
import EditForm from "@/clients/components/Cases/Form.vue";

export default {
	name: "ViewEditCase",
	components: {
		EditForm,
	},
	data() {
		return {
			name: null,
		};
	},
	methods: {
		toView() {
			this.$router.push({
				name: "cases.view",
				params: {
					id: this.$route.params.id,
				},
			});
		},
		loaded(entity) {
			this.name = entity.patient.list_name;
		},
	},
};
</script>
