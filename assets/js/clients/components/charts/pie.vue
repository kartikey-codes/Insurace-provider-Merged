<template>
	<PieChartComponent v-bind="{ data, options, height }" />
</template>

<script>
import { Pie as PieChartComponent } from "vue-chartjs";
import { Chart as ChartJS, Title, Tooltip, Legend, PieController, CategoryScale, LinearScale } from "chart.js";

ChartJS.register(Title, Tooltip, Legend, PieController, CategoryScale, LinearScale);

export default {
	name: "PieChart",
	components: {
		PieChartComponent,
	},
	props: {
		data: {
			type: Object,
			default: () => {
				return {};
			},
		},
		options: {
			type: Object,
			default: () => {
				return {};
			},
		},
		height: {
			type: String,
			default: "",
		},
	},
};
</script>
