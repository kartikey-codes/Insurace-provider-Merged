<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'clientEmployees' }" v-text="`Physicians /`" />
				<router-link :to="{ name: 'clientEmployees.view', params: { id: $route.params.id } }" v-text="name" />
				<span class="text-muted">/&nbsp;Edit</span>
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
import EditForm from "@/clients/components/ClientEmployees/Form.vue";

export default {
	name: "ViewEditClientEmployee",
	components: {
		EditForm,
	},
	data() {
		return {
			name: null,
		};
	},
	methods: {
		toIndex() {
			this.$router.push({
				name: "clientEmployees",
			});
		},
		toView() {
			this.$router.push({
				name: "clientEmployees.view",
				params: {
					id: this.$route.params.id,
				},
			});
		},
		loaded(entity) {
			this.name = entity.full_name;
		},
	},
};
</script>
