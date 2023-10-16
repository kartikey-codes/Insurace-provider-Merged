<template>
	<form ref="uploadForm" role="form" enctype="multipart/form-data" class="form-inline d-inline">
		<input
			ref="uploadInput"
			@change="uploaded"
			type="file"
			multiple="true"
			style="display: none"
			:accept="accept"
		/>
		<b-button v-bind="$attrs" :block="block" @click="browse">
			<slot :browse="browse"></slot>
		</b-button>
	</form>
</template>

<script>
export default {
	name: "UploadButton",
	props: {
		accept: {
			type: String,
			default: "",
		},
		block: {
			type: Boolean,
			default: false,
		},
	},
	methods: {
		browse(event) {
			if (event) {
				event.preventDefault();
			}

			this.$refs.uploadInput.click();
		},
		uploaded(event) {
			if (event.target != undefined && event.target.files.length > 0) {
				this.$emit("upload", event.target.files);
			}

			this.$refs.uploadForm.reset();
		},
	},
};
</script>
