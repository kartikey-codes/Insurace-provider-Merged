<template>
	<b-modal title="Inactivity Warning" size="lg" ref="modal" hide-header-close centered @shown="shown">
		<p class="mb-2" v-if="intervalsLeft">
			Please respond within <strong>{{ intervalsLeft }} minute{{ intervalsLeft == 1 ? "" : "s" }}</strong> or you
			will be automatically logged out.
		</p>
		<p class="text-muted">Press any key or move your mouse to stay logged in.</p>

		<template #modal-footer="{ ok, cancel, hide }">
			<small class="text-muted">
				Article § 164.312(a)(2)(iii) of the HIPAA technical safeguards require an automatic logout when users
				are deemed inactive for a certain period of time.
			</small>
		</template>
	</b-modal>
</template>

<script type="text/javascript">
export default {
	name: "InactivityDetection",
	props: {
		// Amount of increments before warning
		warnThreshold: {
			default: 15,
		},
		// Amount of increments before declared inactive
		expireThreshold: {
			default: 45,
		},
		// Milliseconds between timer incrementing
		// Should be 60000 (1 minute)
		interval: {
			default: 60000,
		},
	},
	computed: {
		difference() {
			return this.expireThreshold - this.warnThreshold;
		},
		intervalsLeft() {
			if (this.inactiveTime < this.warnThreshold) {
				return false;
			}

			return this.expireThreshold - this.inactiveTime;
		},
	},
	created() {
		this.timer = setInterval(this.increment, this.interval);

		document.addEventListener("onload", this.reset);
		document.addEventListener("mousemove", this.reset);
		document.addEventListener("mousedown", this.reset); // touchscreen presses
		document.addEventListener("touchstart", this.reset);
		document.addEventListener("click", this.reset); // touchpad clicks
		document.addEventListener("scroll", this.reset); // scrolling with arrow keys
		document.addEventListener("keypress", this.reset);
	},
	data() {
		return {
			timer: null,
			inactiveTime: null,
			warned: false,
		};
	},
	methods: {
		reset() {
			if (this.inactiveTime > 0) {
				//console.log('Reset inactivity');
			}

			this.warned = false;
			this.inactiveTime = null;

			clearInterval(this.timer);
			this.timer = setInterval(this.increment, this.interval);
		},
		increment() {
			this.inactiveTime++;

			//console.log('Inactive for ' + this.inactiveTime + ' minutes');

			if (this.inactiveTime >= this.expireThreshold) {
				this.$emit("expired");
				return;
			}

			if (this.inactiveTime >= this.warnThreshold) {
				if (!this.warned) {
					this.$emit("warn");
				}

				this.warned = true;

				return;
			}
		},
		shown() {
			window.focus();
		},
	},
	watch: {
		warned(newVal, oldVal) {
			if (oldVal == false && newVal == true) {
				this.$refs.modal.show();
			}

			if (oldVal == true && newVal == false) {
				this.$refs.modal.hide();
			}
		},
	},
	destroyed() {
		clearInterval(this.timer);
	},
};
</script>
