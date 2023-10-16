<template>
	<div>
		<page-header :fluid="false">
			<template #title>
				<router-link :to="{ name: 'patients' }" v-text="`Patients /`" />
				<router-link :to="{ name: 'patients.view', params: { id: $route.params.id } }" v-text="name" />
				<span class="text-muted">/&nbsp;Edit</span>
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
import EditForm from "@/clients/components/Patients/Form.vue";

export default {
	name: "ViewEditPatient",
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
				name: "patients",
			});
		},
		toView(entity) {
			this.$router.push({
				name: "patients.view",
				params: {
					id: this.$route.params.id,
				},
			});
		},
		loaded(entity) {
			this.name = entity.list_name;
		},
	},
};
</script>
