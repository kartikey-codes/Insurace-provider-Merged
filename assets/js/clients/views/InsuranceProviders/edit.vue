<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'insuranceProviders' }" v-text="`Insurance Providers /`" />
				<router-link
					:to="{ name: 'insuranceProviders.view', params: { id: $route.params.id } }"
					v-text="name"
				/>
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
import EditForm from "@/clients/components/InsuranceProviders/Form.vue";

export default {
	name: "ViewEditInsuranceProvider",
	components: {
		EditForm,
	},
	data() {
		return {
			name: "",
		};
	},
	methods: {
		toIndex() {
			this.$router.push({
				name: "insuranceProviders",
			});
		},
		toView() {
			this.$router.push({
				name: "insuranceProviders.view",
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
