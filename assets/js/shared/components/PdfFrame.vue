<template>
	<div
		class="h-auto d-block flex-fill align-items-start mb-0 pb-0"
		:style="sticky ? `position: sticky; top: ${top}` : ''"
		v-bind="$attrs"
	>
		<iframe
			ref="iframe"
			width="100%"
			height="100%"
			frameborder="0"
			:src="parameterizedUrl"
			:class="['w-100', 'pdfFrame', 'm-0', dragging ? 'pointer-events-none' : 'pointer-events-auto']"
			@load="afterFrameLoaded"
			v-show="iframeLoaded"
		/>
		<div class="card-body loading" v-if="loading || iframeLoaded == false">
			<b-progress :max="100" height="12rem" animated striped class="my-2">
				<b-progress-bar :value="100">
					<div class="text-center py-4">
						<h3 class="mb-4">
							<font-awesome-icon icon="circle-notch" spin fixed-width />
							<span>Loading Document...</span>
						</h3>
						<b-button variant="primary" @click="reload" class="px-5">Retry</b-button>
					</div>
				</b-progress-bar>
			</b-progress>
		</div>
	</div>
</template>

<script type="text/javascript">
export default {
	name: "Pdf",
	props: {
		url: {
			required: true,
			type: String,
			default: "",
		},
		title: {
			type: String,
			default: "",
		},
		dragging: {
			type: Boolean,
			default: false,
		},
		top: {
			type: String,
			default: "68px",
		},
		sticky: {
			type: Boolean,
			default: false,
		},
		loading: {
			type: Boolean,
			default: false,
		},
	},
	computed: {
		parameterizedUrl() {
			let url = this.url + "#view=FitH&zoom=100";

			if (this.title) {
				return url + "//" + encodeURIComponent(this.title);
			}

			return url;
		},
	},
	data() {
		return {
			iframeLoaded: false,
		};
	},
	methods: {
		// PDF Viewing
		afterFrameLoaded(event) {
			// This event is always fired regardless of whether the iframe encountered an error or not
			// No way to detect an error in the iframe, so return a 404/500 error from server

			this.iframeLoaded = true;

			this.$emit("loaded");
		},
		// Reload
		reload() {
			this.$emit("reloaded");

			if (this.$refs.iframe) {
				this.iframeLoaded = false;

				//this.$refs.iframe.contentWindow.location.reload();
				this.$refs.iframe.src = this.parameterizedUrl;

				this.$forceUpdate();
			}
		},
	},
	watch: {
		url: {
			immediate: false,
			handler(val) {
				this.iframeLoaded = false;
			},
		},
	},
};
</script>
