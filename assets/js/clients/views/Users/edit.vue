<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'users' }" v-text="`Users /`" />
				<router-link :to="{ name: 'users.view', params: { id: $route.params.id } }" v-text="name" />
				<span>/&nbsp;Edit</span>
			</template>
		</page-header>

		<b-row class="my-4">
			<b-col cols="12">
				<UserEditForm
					autofocus
					:id="$route.params.id"
					v-on="{
						loaded,
						cancel: view,
						saved: view,
					}"
				/>
			</b-col>
		</b-row>
	</div>
</template>

<script type="text/javascript">
import UserEditForm from "@/clients/components/Users/EditForm.vue";

export default {
	name: "ViewEditUser",
	components: {
		UserEditForm,
	},
	data() {
		return {
			name: null,
		};
	},
	methods: {
		view() {
			this.$router.push({
				name: "users.view",
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
