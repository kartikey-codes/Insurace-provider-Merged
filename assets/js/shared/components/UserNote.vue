<template>
	<div class="d-flex align-items-left">
		<b-avatar class="mr-2" variant="light" />
		<div class="ml-2 flex-grow-1">
			<slot name="author">
				<div class="font-weight-bold">
					<span v-if="user && user.full_name" class="text-dark">{{ user.full_name }}</span>
					<span v-else class="text-warning">(Name Missing)</span>
					<b-badge v-if="user && user.admin" variant="primary"> Admin </b-badge>
					<b-badge v-else-if="user && user.vendor_id" variant="success"> Professional </b-badge>
				</div>
			</slot>
			<slot name="timestamp">
				<small class="text-muted" v-if="timestamp" :title="$filters.formatTimestamp(timestamp)">
					{{ $filters.fromNow(timestamp) }}
				</small>
			</slot>
			<slot name="body">
				<div class="my-2">
					<p class="text-dark mb-0 white-space-pre">{{ body }}</p>
				</div>
			</slot>
		</div>
		<div class="ml-2">
			<b-button
				v-if="deletable"
				size="sm"
				variant="link"
				title="Delete Note"
				:disabled="deleting"
				@click="deleteNote"
			>
				<font-awesome-icon v-if="deleting" icon="circle-notch" spin />
				<font-awesome-icon v-else icon="trash" fixed-width />
			</b-button>
		</div>
	</div>
</template>

<script>
export default {
	name: "UserNote",
	props: {
		noteId: {
			type: Number,
			default: 0,
		},
		user: {
			type: Object,
			default: () => {
				return {
					id: null,
					full_name: null,
					admin: null,
					client_id: null,
					vendor_id: null,
				};
			},
		},
		body: {
			type: String,
			default: "",
		},
		timestamp: {
			type: String,
			default: "",
		},
		confirmDelete: {
			type: Boolean,
			default: true,
		},
		deleteConfirmText: {
			type: String,
			default: "Are you sure you want to delete this note?",
		},
		deleting: {
			type: Boolean,
			default: false,
		},
		deletable: {
			type: Boolean,
			default: false,
		},
	},
	methods: {
		deleteNote() {
			if (this.confirmDelete && !confirm(this.deleteConfirmText)) {
				return false;
			}

			this.$emit("delete", this.noteId);
		},
	},
};
</script>
