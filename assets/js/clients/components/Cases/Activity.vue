<template>
	<div v-bind="$attrs">
		<font-awesome-icon v-if="showLoading" icon="circle-notch" spin fixed-width />
		<div v-else-if="!isEmpty">
			<span class="small text-muted font-weight-bold">Viewing:</span>
			<transition-group name="fade" enter-active-class="fadeIn" leave-active-class="fadeOut">
				<span v-for="user in users" :key="user.id" class="text-muted mr-2">
					<b-avatar size="sm" variant="light" />
					<span v-text="user.full_name" />
				</span>
			</transition-group>
		</div>
	</div>
</template>

<script type="text/javascript">
export default {
	name: "CaseActivity",
	props: {
		caseId: {
			type: [String, Number],
			default: null,
			required: true,
		},
	},
	data() {
		return {
			loading: false,
			loaded: false,
			timer: null,
			users: [],
		};
	},
	computed: {
		isEmpty() {
			return this.users.length <= 0;
		},
		showLoading() {
			return this.loading && !this.loaded;
		},
	},
	created() {
		this.timer = setInterval(this.refresh, 60000); // 1 minute
	},
	mounted() {
		this.refresh();
	},
	methods: {
		// Ping the server that we're looking at this case and return who else is too
		async refresh() {
			try {
				this.loading = true;

				const response = await this.$store.dispatch("cases/activity", {
					id: this.caseId,
				});

				this.users = response;
				this.loaded = true;
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Error",
					message: "Unable to retrieve case activity.",
					notify: false,
				});
			} finally {
				this.loading = false;
			}
		},
	},
	destroyed() {
		clearInterval(this.timer);
	},
};
</script>
