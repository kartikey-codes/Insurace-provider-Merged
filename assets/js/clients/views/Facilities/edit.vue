<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'facilities' }" v-text="`Facilities /`" />
				<router-link :to="{ name: 'facilities.view', params: { id: $route.params.id } }" v-text="name" />
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
import EditForm from "@/clients/components/Facilities/EditForm.vue";

export default {
	name: "ViewEditFacility",
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
				name: "facilities",
			});
		},
		toView() {
			this.$router.push({
				name: "facilities.view",
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
