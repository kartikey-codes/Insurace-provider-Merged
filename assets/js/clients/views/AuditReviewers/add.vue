<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'auditReviewers' }" v-text="`Audit Reviewers /`" />
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
import AddForm from "@/clients/components/AuditReviewers/Form.vue";

export default {
	name: "ViewAddAuditReviewer",
	components: {
		AddForm,
	},
	data() {
		return {
			entity: {
				id: null,
				agency_id: null,
				first_name: "",
				last_name: "",
			},
		};
	},
	methods: {
		toIndex() {
			this.$router.push({
				name: "auditReviewers",
			});
		},
		toView(entity) {
			this.$router.push({
				name: "auditReviewers.view",
				params: {
					id: entity.id,
				},
			});
		},
	},
};
</script>
