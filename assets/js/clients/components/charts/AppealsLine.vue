<template>
	<line-chart v-bind="{ options, data, styles, params, refresh, height }" />
</template>

<script>
import moment from "moment";
import LineChart from "./line.vue";

export default {
	name: "AppealsLineChart",
	components: {
		LineChart,
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
		startDate: {
			type: Object,
			default: () => {
				return moment().add(-30, "days");
			},
		},
		endDate: {
			type: Object,
			default: () => {
				return moment();
			},
		},
		params: {
			type: Object,
			default: () => {},
		},
	},
	data() {
		return {
			loading: false,
			labelFormat: "M/D",
			dateFormat: "YYYY-MM-DD",
			appeals: {
				created: [],
				completed: [],
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				elements: {
					point: {
						radius: 3,
					},
				},
				scales: {
					y: {
						beginAtZero: true,
					},
				},
				plugins: {
					title: {
						display: !this.hideTitle,
						text: "Appeals",
					},
				},
			},
		};
	},
	computed: {
		labels() {
			var days = [];
			var day = this.startDate;

			while (day <= this.endDate) {
				days.push(day.format(this.labelFormat));
				day = day.clone().add(1, "d");
			}

			return days;
		},
		styles() {
			return {
				position: "relative",
				height: this.height,
			};
		},
		data() {
			return {
				labels: this.labels,
				datasets: [
					{
						label: "Created",
						borderColor: "rgb(37, 99, 235)",
						backgroundColor: "rgb(59, 130, 246)",
						data: this.appeals.created
							? Object.keys(this.appeals.created).map((e) => this.appeals.created[e])
							: [],
					},
					{
						label: "Completed",
						borderColor: "rgb(21, 128, 61)",
						backgroundColor: "rgb(22, 163, 74)",
						data: this.appeals.completed
							? Object.keys(this.appeals.completed).map((e) => this.appeals.completed[e])
							: [],
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

				const response = await this.$store.dispatch("charts/appeals", {
					start: this.startDate.format(this.dateFormat),
					end: this.endDate.add(1, "days").format(this.dateFormat),
					params: this.params,
				});

				this.appeals = {
					created: response.created ?? [],
					completed: response.completed ?? [],
				};
			} finally {
				this.loading = false;
			}
		},
	},
};
</script>
