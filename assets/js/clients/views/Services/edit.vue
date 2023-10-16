<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'services' }" v-text="`Services /`" />
				<router-link :to="{ name: 'services.view', params: { id: $route.params.id } }" v-text="name" />
				<span>/&nbsp;Edit</span>
			</template>
		</page-header>

		<b-row class="my-4">
			<b-col cols="12">
				<EditForm :id="$route.params.id" @loaded="loaded" @cancel="toView" @saved="toView" />
			</b-col>
		</b-row>
	</div>
</template>

<script type="text/javascript">
import EditForm from "@/clients/components/Services/Form.vue";

export default {
	name: "ViewEditService",
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
				name: "services.view",
				params: {
					id: this.$route.params.id,
				},
			});
		},
		loaded(entity) {
			this.name = entity.name;
		},
	},
};
</script>
