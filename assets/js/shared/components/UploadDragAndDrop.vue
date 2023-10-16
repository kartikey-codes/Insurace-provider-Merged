<template>
	<div
		@dragstart="startDrag"
		@drop.prevent="dropped"
		@dragover.prevent="dragging=true"
		@dragenter.prevent="dragging=true"
		@dragleave.prevent="dragging=false"
		@dragend.prevent="dragging=false"
	>
		<slot :dragging="dragging"></slot>
	</div>
</template>

<script>
export default {
	name: "UploadDragAndDrop",
	data() {
		return {
			dragging: false,
		};
	},
	methods: {
		startDrag(event) {
			event.dataTransfer.dropEffect = "move";
			event.dataTransfer.effectAllowed = "move";
		},
		dropped(event) {
			event.preventDefault();
			event.stopPropagation();

			this.dragging = false;

			if (event.dataTransfer == undefined) {
				return false;
			}

			if (
				event.dataTransfer.files == undefined ||
				event.dataTransfer.files.length == undefined
			) {
				return false;
			}

			if (event.dataTransfer.files.length == 0) {
				return false;
			}

			this.$emit("dropped", event.dataTransfer.files);
		},
	},
};
</script>
