<template>
	<b-row>
		<b-col cols="12">
			<b-card-group class="shadow-sm mb-4">
				<b-card class="text-center">
					<p class="h4 font-weight-bold mb-1">
						<span v-if="!favorableCasesPercent" class="text-muted">&mdash;</span>
						<span v-else>{{ favorableCasesPercent }}%</span>
					</p>
					<p class="small text-muted mb-0">Favorable Cases</p>
				</b-card>
				<b-card class="text-center">
					<p class="h4 font-weight-bold mb-1">
						<span v-if="!openClaimsTotal" class="text-muted">&mdash;</span>
						<span v-else class="mx-0 px-0">
							{{ $filters.abbreviatedCurrency(openClaimsTotal) }}
						</span>
					</p>
					<p class="small text-muted mb-0">Open Claims</p>
				</b-card>
				<b-card class="text-center">
					<p class="h4 font-weight-bold mb-1">
						<span v-if="!reimbursedTotal" class="text-muted">&mdash;</span>
						<span v-else class="mx-0 px-0">
							{{ $filters.abbreviatedCurrency(reimbursedTotal) }}
						</span>
					</p>
					<p class="small text-muted mb-0">Reimbursed</p>
				</b-card>
			</b-card-group>
		</b-col>
		<b-col cols="12" lg="6">
			<b-card class="mb-4 shadow-sm" no-body header="Cases" header-class="font-weight-bold">
				<b-container fluid class="py-4">
					<b-row>
						<b-col cols="12" lg="12">
							<CasesPieChart hide-title height="300px" />
						</b-col>
					</b-row>
				</b-container>
				<b-list-group flush v-if="closedCasesByOutcome.length > 0">
					<b-list-group-item
						v-for="item in closedCasesByOutcome"
						:key="item.name"
						class="d-flex justify-content-between align-items-center"
					>
						<span> {{ item.name }}</span>
						<span v-if="item.count > 0" class="font-weight-bold">
							{{ item.count }}
						</span>
						<span v-else class="text-muted small">&mdash;</span>
					</b-list-group-item>
				</b-list-group>
			</b-card>
		</b-col>
		<b-col cols="12" lg="6">
			<b-card class="mb-4 shadow-sm" no-body header="Appeals" header-class="font-weight-bold">
				<b-container fluid class="py-4">
					<b-row>
						<b-col cols="12">
							<AppealsLineChart hide-title height="300px" />
						</b-col>
					</b-row>
				</b-container>
				<b-list-group flush v-if="appealsByCaseType.length > 0">
					<b-list-group-item
						v-for="item in appealsByCaseType"
						:key="item.name"
						class="d-flex justify-content-between align-items-center"
					>
						<span> {{ item.name }}</span>
						<span v-if="item.count > 0" class="font-weight-bold">
							{{ item.count }}
						</span>
						<span v-else class="text-muted small">&mdash;</span>
					</b-list-group-item>
				</b-list-group>
			</b-card>
		</b-col>
	</b-row>
</template>

<script>
import { mapGetters } from "vuex";
import AppealsLineChart from "@/clients/components/charts/AppealsLine.vue";
import CasesPieChart from "@/clients/components/charts/CasesPie.vue";

export default {
	name: "ViewReportIndexHome",
	components: {
		AppealsLineChart,
		CasesPieChart,
	},
	computed: {
		...mapGetters({
			// Statistics
			unassignedAppeals: "statistics/unassignedAppeals",
			emptyCases: "statistics/emptyCases",
			appealsByCaseType: "statistics/appealsByCaseType",
			closedCasesByOutcome: "statistics/closedCasesByOutcome",
			favorableCasesPercent: "statistics/favorableCasesPercent",
			openClaimsTotal: "statistics/openClaimsTotal",
			reimbursedTotal: "statistics/reimbursedTotal",
		}),
	},
};
</script>
