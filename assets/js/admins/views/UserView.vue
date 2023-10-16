<template>
	<b-row>
		<b-col cols="12">
			<pre>{{ entity }}</pre>
			<b-button @click="modal = true">Edit</b-button>

			<!-- Form Modal -->
			<b-modal v-model="modal" size="xl" scrollable @ok.prevent="submitForm" :ok-disabled="saving">
				<template #modal-title>
					<h6 class="text-muted mb-1">User</h6>
					<h3 v-if="entity.full_name">
						{{ entity.full_name }}
					</h3>
					<h3 v-else class="text-danger">Missing Name</h3>
				</template>
				<edit-form ref="form" :value="entity" :busy="saving" @save="save" />
				<template #modal-ok>
					<font-awesome-icon icon="circle-notch" v-if="saving" spin fixed-width class="mr-2" />
					<span>Save Changes</span>
				</template>
			</b-modal>
		</b-col>
	</b-row>
</template>

<script>
import { mapGetters } from "vuex";
import EditForm from "@/admins/components/Users/Form.vue";

export default {
	name: "UserView",
	components: {
		EditForm,
	},
	props: {},
	data() {
		return {
			modal: false,
			loading: false,
			saving: false,
			entity: {
				id: null,
				full_name: null,
			},
		};
	},
	mounted() {
		this.refresh();
	},
	methods: {
		async refresh() {
			try {
				this.loading = true;
				const response = await this.$store.dispatch("users/get", {
					id: this.$route.params.id,
				});
				this.entity = response;
			} finally {
				this.loading = false;
			}
		},
		async save() {
			try {
				this.saving = true;
				const response = await this.$store.dispatch("users/save", this.entity);
				this.entity = response;
			} finally {
				this.saving = false;
			}
		},
	},
};
</script>
