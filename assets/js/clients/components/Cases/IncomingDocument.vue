<template>
	<div>
		<b-button variant="outline-danger" @click="detachDocument" :disabled="detaching" class="my-2 mx-2">
			<font-awesome-icon :icon="detaching ? 'sync' : 'times'" :spin="detaching" fixed-width />
			<span>Detach Document</span>
		</b-button>

		<pdf-frame v-show="previewUrl" :url="previewUrl" />
	</div>
</template>

<script type="text/javascript">
import PdfFrame from "@/shared/components/PdfFrame.vue";

export default {
	name: "CaseIncomingDocument",
	components: {
		PdfFrame,
	},
	props: {
		document: {
			type: Object,
			default: () => {},
		},
		confirmDeleteText: {
			type: String,
			default:
				"Are you sure you want to detach this document from this case? The document will be returned to the incoming document queue.",
		},
	},
	data() {
		return {
			detaching: false,
			previewUrl: null,
		};
	},
	methods: {
		async getUrl() {
			this.previewUrl = await this.$store.dispatch("incomingDocuments/previewUrl", {
				id: this.document.id,
			});
		},
		async detachDocument() {
			if (!confirm(this.confirmDeleteText)) {
				return false;
			}

			try {
				this.detaching = true;

				const response = await this.$store.dispatch("incomingDocuments/detachCase", {
					id: this.document.id,
				});

				this.$emit("detached", response);
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Detach Error",
					message: "Error detaching document from case",
				});
			} finally {
				this.detaching = false;

				this.$store.dispatch("updateState");
			}
		},
	},
};
</script>
