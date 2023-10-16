<template>
	<b-row>
		<b-col cols="12">
			<loading-indicator v-if="loading" class="my-5" />
			<empty-result v-else-if="results.length <= 0" icon="search">
				No notes found
				<template #content>
					No notes were found on appeals. You can add notes to appeals by clicking the Notes tab when viewing
					an appeal.
				</template>
			</empty-result>
			<div v-else>
				<b-list-group flush>
					<b-list-group-item v-for="note in results" :key="note.id">
						<div class="d-flex align-items-left">
							<b-avatar class="mr-2" variant="light" />
							<div class="ml-2 flex-grow-1">
								<div class="font-weight-bold">
									<span
										v-if="note && note.created_by_user && note.created_by_user.full_name"
										class="text-dark"
									>
										{{ note.created_by_user.full_name }}
									</span>
									<span v-else class="text-warning">(Name Missing)</span>
								</div>
								<small
									class="text-muted"
									v-if="note.created"
									:title="$filters.formatTimestamp(note.created)"
								>
									{{ $filters.fromNow(note.created) }}
								</small>
								<div class="my-2">
									<p class="text-dark mb-0 white-space-pre">{{ note.notes }}</p>
								</div>
							</div>
							<div>
								<b-button
									:to="{
										name: 'appeals.view',
										params: {
											id: note.appeal.case_id,
											appeal_id: note.appeal_id,
										},
									}"
								>
									View Appeal
								</b-button>
							</div>
						</div>
					</b-list-group-item>
				</b-list-group>
			</div>
		</b-col>
	</b-row>
</template>

<script>
export default {
	name: "RecentNotes",
	data() {
		return {
			loading: false,
			page: 1,
			results: [],
			pagination: {
				count: 0,
				page: 1,
				pageCount: 1,
				prevPage: false,
				nextPage: false,
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

				const response = await this.$store.dispatch("dashboard/recentNotes", {
					page: this.page,
				});
				this.pagination = response.pagination || {};
				this.results = response.data || [];
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Fetching Notes Failed",
					message: "Failed to get recent notes. Contact support if the issue persists.",
					variant: "warning",
				});
			} finally {
				this.loading = false;
			}
		},
	},
	watch: {
		page: {
			immediate: false,
			handler(val) {
				this.refresh();
			},
		},
	},
};
</script>
