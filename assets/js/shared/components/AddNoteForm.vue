<template>
	<b-form @submit.prevent="submit" v-bind="$attrs">
		<b-row>
			<b-col cols="12">
				<b-form-textarea
					v-model="notes"
					:autofocus="autofocus"
					:disabled="disabled || saving"
					:rows="rows"
					:max-rows="maxRows"
					:class="inputClass"
					:placeholder="placeholder"
					style="resize: vertical; overflow-y: auto"
					:style="{ minHeight, maxHeight }"
				/>
			</b-col>
			<b-col cols="12" class="text-right">
				<b-button type="submit" variant="primary" :disabled="empty || disabled || saving" class="px-4">
					<font-awesome-icon v-if="saving" icon="circle-notch" spin fixed-width />
					<span v-if="!saving">Save Note</span>
					<span v-else>Saving...</span>
				</b-button>
			</b-col>
		</b-row>
	</b-form>
</template>

<script type="text/javascript">
export default {
	name: "AddNoteForm",
	props: {
		autofocus: {
			type: Boolean,
			default: false,
		},
		disabled: {
			type: Boolean,
			default: false,
		},
		saving: {
			type: Boolean,
			default: false,
		},
		maxRows: {
			type: Number,
			default: 6,
		},
		resetAfterSubmit: {
			type: Boolean,
			default: false,
		},
		inputClass: {
			type: String,
			default: "rounded mb-2",
		},
		placeholder: {
			type: String,
			default: "Add a note...",
		},
		minHeight: {
			type: String,
			default: "5rem",
		},
		maxHeight: {
			type: String,
			default: "25rem",
		},
	},
	data() {
		return {
			notes: "",
		};
	},
	computed: {
		empty() {
			return !this.notes || this.notes === null || this.notes === "";
		},
		rows() {
			return this.empty ? 1 : this.maxRows;
		},
	},
	methods: {
		async submit(event) {
			event.preventDefault();

			this.$emit("submit", {
				notes: this.notes,
			});

			if (this.resetAfterSubmit) {
				this.reset();
			}
		},
		reset() {
			this.notes = "";
		},
	},
};
</script>
