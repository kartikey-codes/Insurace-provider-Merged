<template>
	<b-input-group>
		<b-input-group-prepend is-text>
			<font-awesome-icon :icon="loading ? loadingIcon : searchIcon" :spin="loading" />
		</b-input-group-prepend>
		<b-form-input
			type="text"
			ref="formInput"
			v-model="query"
			:autofocus="autofocus"
			:placeholder="placeholder"
			autocomplete="off"
			class="ml-auto"
		/>
	</b-input-group>
</template>

<script>
export default {
	name: "SearchInput",
	props: {
		autofocus: {
			type: Boolean,
			default: true,
		},
		value: {
			type: String,
			default: "",
		},
		placeholder: {
			type: String,
			default: "Search...",
		},
		loading: {
			type: Boolean,
			default: false,
		},
		loadingIcon: {
			type: String,
			default: "circle-notch",
		},
		searchIcon: {
			type: String,
			default: "search",
		},
	},
	computed: {
		query: {
			get() {
				return this.value;
			},
			set(val) {
				this.$emit("input", val);
			},
		},
	},
	methods: {
		focus() {
			this.$refs.formInput.focus();
		},
		clear() {
			this.query = "";
		},
	},
};
</script>
