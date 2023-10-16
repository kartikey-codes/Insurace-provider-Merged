<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'users' }" v-text="`Users /`" />
				<span>Add</span>
			</template>
		</page-header>

		<b-row class="my-4">
			<b-col cols="12">
				<UserAddForm autofocus @cancel="$router.push({ name: 'users' })" @saved="saved" />
			</b-col>
		</b-row>
	</div>
</template>

<script type="text/javascript">
import UserAddForm from "@/clients/components/Users/AddForm.vue";

export default {
	name: "ViewAddUser",
	components: {
		UserAddForm,
	},
	data() {
		return {};
	},
	methods: {
		saved(response) {
			this.$store.dispatch("users/getActive");

			this.$router.push({
				name: "users.view",
				params: {
					id: response.id,
				},
			});

			this.$nextTick(function () {
				this.$store.dispatch("notify", {
					variant: "primary",
					title: "User Added",
					message: `User account was created.`,
				});
			});
		},
	},
};
</script>
