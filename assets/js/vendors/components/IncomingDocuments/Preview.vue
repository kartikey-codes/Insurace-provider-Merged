<template>
	<pdf-frame :url="previewUrl" />
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import PdfFrame from "@/shared/components/PdfFrame.vue";

export default {
	name: "IncomingDocumentPreview",
	components: {
		PdfFrame,
	},
	props: {
		document: {
			required: true,
			type: Object,
			default: () => {
				return {
					id: null,
				};
			},
		},
	},
	computed: {
		previewUrl() {
			return (
				this.apiBaseURL +
				"/incoming-documents/" +
				this.document.id +
				"/preview" +
				"?token=" +
				this.apiToken +
				"#view=FitH"
			);
		},
		...mapGetters({
			apiBaseURL: "baseURL",
			apiToken: "apiToken",
		}),
	},
};
</script>
