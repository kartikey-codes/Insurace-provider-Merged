<template>
	<div>
		<page-header>
			<template #title>
				<router-link :to="{ name: 'facilities' }" v-text="`Facilities /`" />
				<span>Add</span>
			</template>
			<template #buttons>
				<b-button variant="info" :to="{ name: 'facilities.add.npi' }" title="Add from NPI Registry">
					<font-awesome-icon icon="search" fixed-width />
					<span>Search NPI Registry</span>
				</b-button>
			</template>
		</page-header>

		<b-row class="my-4">
			<b-col cols="12">
				<AddForm @cancel="toIndex" @saved="toView" />
			</b-col>
		</b-row>
	</div>
</template>

<script type="text/javascript">
import AddForm from "@/clients/components/Facilities/AddForm.vue";

export default {
	name: "ViewAddFacility",
	components: {
		AddForm,
	},
	data() {
		return {};
	},
	methods: {
		toIndex() {
			this.$router.push({
				name: "facilities",
			});
		},
		toView(entity) {
			this.$router.push({
				name: "facilities.view",
				params: {
					id: entity.id,
				},
			});
		},
	},
};
</script>
