<template>
	<PieChart v-bind="{ data, options, styles, params, refresh, height }" />
</template>

<script>
import PieChart from "./pie.vue";

export default {
	name: "CasesPieChart",
	components: {
		PieChart,
	},
	props: {
		hideTitle: {
			type: Boolean,
			default: false,
		},
		height: {
			type: String,
			default: "300px",
		},
		params: {
			type: Object,
			default: () => {},
		},
	},
	data() {
		return {
			loading: false,
			openCases: 0,
			utcCases: 0,
			closedCases: 0,
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					title: {
						display: !this.hideTitle,
						text: "Cases",
					},
				},
			},
		};
	},
	computed: {
		styles() {
			return {
				position: "relative",
				height: this.height,
			};
		},
		data() {
			return {
				labels: ["Open", "UTC", "Closed"],
				datasets: [
					{
						backgroundColor: ["rgb(50, 211, 85)", "rgb(255, 193, 7)", "rgb(0, 123, 255)"],
						// borderColor: [
						// 	'rgb(30, 191, 65)',
						// 	'rgb(0, 103, 235)'
						// ],
						data: [this.openCases, this.utcCases, this.closedCases],
					},
				],
			};
		},
	},
	mounted() {
		window.onresize = () => {
			this.windowWidth = window.innerWidth;
		};

		this.refresh();
	},
	methods: {
		async refresh() {
			try {
				this.loading = true;

				const response = await this.$store.dispatch("charts/cases", this.params);

				this.openCases = response.open;
				this.utcCases = response.utc;
				this.closedCases = response.closed;
			} finally {
				this.loading = false;
			}
		},
	},
};
</script>
