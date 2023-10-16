<template>
	<empty-result v-if="!url">
		No cover page
		<template #content> No cover page has been created. </template>
	</empty-result>
	<div v-else-if="!editing">
		<b-card-header>
			<b-button @click="editing = !editing" :active="editing" variant="primary">
				<font-awesome-icon icon="edit" fixed-width />
				<span>Edit Letter</span>
			</b-button>
		</b-card-header>
		<pdf-frame :url="url" />
	</div>
	<div v-else>
		<b-card-body>
			<form-group name="letter" label="Letter Contents" label-cols-lg="12">
				<b-form-textarea
					autofocus
					rows="10"
					placeholder="Paste in a response to generate a PDF..."
					v-model="letter"
				></b-form-textarea>
			</form-group>
		</b-card-body>
		<b-card-footer>
			<b-row>
				<b-col cols="6" class="text-left">
					<b-button variant="light" @click="editing = false"> Cancel </b-button>
				</b-col>
				<b-col cols="6" class="text-right">
					<b-button @click="save" :disabled="saving" variant="primary">
						<font-awesome-icon v-if="saving" icon="circle-notch" spin fixed-width />
						<span>Save</span>
					</b-button>
				</b-col>
			</b-row>
		</b-card-footer>
	</div>
</template>

<script>
import { mapGetters } from "vuex";
import PdfFrame from "@/shared/components/PdfFrame.vue";

export default {
	name: "AppealCoverPage",
	components: {
		PdfFrame,
	},
	props: {
		appeal: {
			type: Object,
			required: true,
			default: () => {
				return {
					id: null,
					case_id: null,
					cover_page_url: null,
				};
			},
		},
	},
	computed: {
		url() {
			return this.appeal.cover_page_url + "?token=" + this.apiToken;
		},
		...mapGetters({
			apiToken: "apiToken",
		}),
	},
	data() {
		return {
			editing: false,
			letter: "",
			saving: false,
		};
	},
	methods: {
		async save() {
			try {
				this.saving = true;

				const response = await this.$store.dispatch("appeals/generateCoverPage", {
					id: this.appeal.id,
					letter: this.letter,
				});

				this.$store.dispatch("notify", {
					variant: "primary",
					title: "Cover Page Updated",
					message: `Cover page updated successfully.`,
				});

				this.$emit("saved", response);

				this.editing = false;
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Cover Page Generation Failed",
					message: "Error attempting to save cover page. Contact support if the issue persists.",
					variant: "warning",
				});
			} finally {
				this.saving = false;
			}
		},
	},
};
</script>
