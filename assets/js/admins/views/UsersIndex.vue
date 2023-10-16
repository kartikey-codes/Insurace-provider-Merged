<template>
	<b-container class="my-4 my-lg-5">
		<div class="d-flex justify-content-between align-items-center mb-2">
			<h1 class="h3">Users</h1>
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
			style="width: 5em; height: 5em"
		/>
		<b-alert v-else-if="results.length <= 0" show variant="light" class="p-5 shadow-sm">
			No results found.
		</b-alert>
		<b-list-group v-else class="shadow-sm">
			<b-list-group-item
				v-for="result in results"
				:key="result.id"
				:to="{ name: 'users.view', params: { id: result.id } }"
			>
				<div class="d-flex justify-content-between align-items-center mb-1">
					<div>
						<span class="font-weight-bold">{{ result.full_name }}</span>
						<span v-if="result.admin" class="badge badge-primary"> Admin </span>
					</div>
					<div>
						<span v-if="!result.active" class="badge badge-warning"> Inactive </span>
						<span v-if="result.client_id" class="badge badge-light"> Client </span>
						<span v-if="result.vendor_id" class="badge badge-info"> Vendor </span>
					</div>
				</div>
				<div class="small text-muted">
					{{ result.email }}
				</div>
				<div v-if="result.last_seen" class="small text-muted">
					Last seen {{ $filters.fromNow(result.last_seen) }}
				</div>
				<div v-else class="small text-danger">Never logged in</div>
			</b-list-group-item>
		</b-list-group>
	</b-container>
</template>

<script>
import { mapGetters } from "vuex";
import EditForm from "@/admins/components/Users/Form.vue";

export default {
	name: "AdminViewUsers",
	components: {
		EditForm,
	},
	computed: {
		results() {
			return this.users.filter((user) => user.full_name.toUpperCase().includes(this.search.toUpperCase()));
		},
		...mapGetters({
			users: "users/all",
			loading: "users/loadingAll",
		}),
	},
	mounted() {
		this.$store.dispatch("users/getAll");
	},
	data() {
		return {
			search: "",
			entity: {
				modal: false,
				saving: false,
				value: {},
			},
		};
	},
	methods: {
		clicked(value) {
			this.entity = {
				modal: true,
				saving: false,
				value: value,
			};
		},
		submitForm(e) {
			this.$refs.form.submit(e);
		},
		async save(data) {
			try {
				this.entity.saving = true;

				const response = await this.$store.dispatch("users/save", data);
				this.entity.modal = false;
			} catch (e) {
				console.error(e);

				alert(e.response.data.message || "Failed to save");
			} finally {
				this.entity.saving = false;

				this.$store.dispatch("users/getAll");
			}
		},
	},
};
</script>
