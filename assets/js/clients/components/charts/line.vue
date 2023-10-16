<template>
	<LineChartComponent v-bind="{ data, options, height }" />
</template>

<script>
import { Line as LineChartComponent } from "vue-chartjs";
import {
	Chart as ChartJS,
	Title,
	Tooltip,
	Legend,
	LineElement,
	CategoryScale,
	LinearScale,
	PointElement,
	ArcElement,
} from "chart.js";

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement, ArcElement);

export default {
	name: "LineChart",
	components: {
		LineChartComponent,
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
