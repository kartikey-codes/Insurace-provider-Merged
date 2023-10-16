<template>
	<b-container class="my-4 my-lg-5">
		<div class="d-flex justify-content-between align-items-center mb-2">
			<h1 class="h3">Clients</h1>

			<b-button href="/client" class="ml-4" variant="light">
				<font-awesome-icon icon="external-link-alt" fixed-width />
				<span>Client App</span>
			</b-button>
		</div>
		<b-row>
			<b-col cols="12" class="mb-4">
				<b-input-group>
					<template #prepend>
						<b-input-group-text class="bg-white border-right-0">
							<font-awesome-icon icon="search" fixed-width />
						</b-input-group-text>
					</template>
					<b-input
						autofocus
						type="text"
						v-model="search"
						placeholder="Filter by name..."
						class="border-left-0"
					/>
					<template #append>
						<b-button @click="search = ''">
							<font-awesome-icon icon="times" fixed-width />
						</b-button>
					</template>
				</b-input-group>
			</b-col>
		</b-row>
		<b-spinner
			v-if="loading"
			label="Loading..."
			variant="primary"
			class="d-block mx-auto"
			style="width: 5em; height: 5em;"
		/>
		<b-alert v-else-if="results.length <= 0" show variant="light" class="p-5 shadow-sm">
			No results found.
		</b-alert>
		<b-list-group v-else class="shadow-sm">
			<b-list-group-item
				action
				button
				v-for="result in results"
				:key="result.id"
				@click="clicked(result)"
				class="d-flex justify-content-between align-items-center"
			>
				<div>
					<div class="font-weight-bold">{{ result.name }}</div>
					<div class="small text-muted" v-if="result.licenses">{{ result.licenses }} Licenses</div>
				</div>
				<div>
					<span class="badge badge-warning" v-if="!result.subscription_active">Unsubscribed</span>
					<span class="badge badge-danger" v-if="!result.active">Inactive</span>
				</div>
			</b-list-group-item>
		</b-list-group>

		<!-- Form Modal -->
		<b-modal v-model="entity.modal" size="xl" scrollable @ok.prevent="submitForm" :ok-disabled="entity.saving">
			<template #modal-title>
				<h6 class="text-muted mb-1">Client</h6>
				<h3 v-if="entity.value.name">
					{{ entity.value.name }}
				</h3>
				<h3 v-else class="text-danger">Missing Name</h3>
			</template>
			<edit-form ref="form" :value="entity.value" :busy="entity.saving" @save="save" />
			<template #modal-ok>
				<font-awesome-icon icon="circle-notch" v-if="entity.saving" spin fixed-width class="mr-2" />
				<span>Save Changes</span>
			</template>
		</b-modal>
	</b-container>
</template>

<script>
import { mapGetters } from "vuex";
import EditForm from "@/admins/components/Clients/Form.vue";

export default {
	name: "AdminViewClients",
	components: {
		EditForm
	},
	computed: {
		results() {
			return this.clients.filter(client => client.name.toUpperCase().includes(this.search.toUpperCase()));
		},
		...mapGetters({
			clients: "clients/all",
			loading: "clients/loadingAll"
		})
	},
	mounted() {
		this.$store.dispatch("clients/getAll");
	},
	data() {
		return {
			search: "",
			entity: {
				modal: false,
				saving: false,
				value: {}
			}
		};
	},
	methods: {
		clicked(value) {
			this.entity = {
				modal: true,
				saving: false,
				value: value
			};
		},
		submitForm(e) {
			this.$refs.form.submit(e);
		},
		async save(data) {
			try {
				this.entity.saving = true;

				const response = await this.$store.dispatch("clients/save", data);
				this.entity.modal = false;
			} catch (e) {
				console.error(e);

				alert(e.response.data.message || "Failed to save");
			} finally {
				this.entity.saving = false;

				this.$store.dispatch("clients/getAll");
			}
		}
	}
};
</script>
