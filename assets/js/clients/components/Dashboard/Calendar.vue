<template>
	<b-container fluid class="my-4">
		<b-row>
			<b-col cols="12" lg="4" class="mb-4">
				<b-calendar
					block
					hide-header
					v-model="selectedDay"
					:date-info-fn="dateClass"
					@context="onContext"
					locale="en-US"
				/>
			</b-col>
			<b-col cols="12" lg="8">
				<h4 class="font-weight-bold mb-4">{{ $filters.formatDateLong(selectedDay) }}</h4>
				<loading-indicator v-if="loadingDay" />
				<empty-result v-else-if="dayEmpty" icon="calendar">
					Nothing due
					<template #content> No open appeals or requests were found due on this day. </template>
				</empty-result>
				<b-list-group v-else>
					<b-list-group-item
						v-for="request in data.requests"
						:key="request.id"
						:to="{
							name: 'caseRequests.view',
							params: {
								id: request.case_id,
								case_request_id: request.id,
							},
						}"
						class="py-3"
					>
						<b-row>
							<b-col cols="6">
								<h6 class="small text-muted">Request</h6>
								<h5 class="h6 text-dark mb-0">
									{{ request.name }}
								</h5>
							</b-col>
							<b-col cols="6" class="text-right">
								<p
									class="small mb-0"
									:class="request.is_overdue ? 'font-weight-bold text-danger' : 'text-muted'"
								>
									Due {{ $filters.formatDate(request.due_date) }}
								</p>
							</b-col>
						</b-row>
					</b-list-group-item>

					<b-list-group-item
						v-for="appeal in data.appeals"
						:key="appeal.id"
						:to="{
							name: 'appeals.view',
							params: {
								id: appeal.case_id,
								appeal_id: appeal.id,
							},
						}"
						class="py-3"
					>
						<div class="d-flex align-items-start">
							<appeal-status-label avatar :value="appeal" />
							<b-container fluid>
								<b-row>
									<b-col cols="6">
										<h6 class="small text-muted">Appeal</h6>
										<h5 class="h6 text-dark mb-0">
											<span v-if="appeal?.case?.patient">
												{{ appeal.case.patient.full_name }}
											</span>
											<span v-else class="text-danger"> Missing Patient Name </span>
										</h5>
										<p class="mb-0">
											<span v-if="appeal?.appeal_level?.name">
												{{ appeal.appeal_level.name }}
											</span>
											<span v-else class="text-danger"> Missing Level </span>

											<appeal-status-label pill :value="appeal" />
										</p>
										<p v-if="appeal.case?.insurance_provider?.name" class="small text-muted mb-0">
											<span>{{ appeal.case.insurance_provider.name }}</span>
										</p>
									</b-col>
									<b-col cols="6" class="text-right">
										<p v-if="appeal.priority" class="mb-0 font-weight-bold text-primary">
											Priority
										</p>
										<p
											v-if="appeal.due_date"
											class="small mb-0"
											:class="appeal.is_overdue ? 'text-danger font-weight-bold' : 'text-muted'"
										>
											Response due {{ $filters.formatDate(appeal.due_date) }}
										</p>
									</b-col>
								</b-row>
							</b-container>
						</div>
					</b-list-group-item>
				</b-list-group>
			</b-col>
		</b-row>
	</b-container>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import { getTodaysDate } from "@/shared/helpers/dateHelper";
import AppealStatusLabel from "@/clients/components/Appeals/StatusLabel.vue";

export default {
	name: "Calendar",
	components: {
		AppealStatusLabel,
	},
	data() {
		return {
			selectedDay: getTodaysDate(),
			context: null,
			start: null,
			end: null,
			loadingDay: false,
			data: {
				count: 0,
				appeals: [],
				requests: [],
			},
		};
	},
	computed: {
		dayEmpty() {
			return this.data.count <= 0;
		},
		...mapGetters({
			loading: "calendar/loading",
			results: "calendar/results",
		}),
	},
	mounted() {
		this.$store.dispatch("calendar/getIndex", {
			start: this.start,
			end: this.end,
		});

		this.getDay();
	},
	methods: {
		dateClass(ymd, date) {
			// const day = date.getDate();
			// return day >= 10 && day <= 20 ? "table-info" : "";

			return "";
		},
		onContext(ctx) {
			this.context = ctx;
			this.selectedDay = ctx.selectedYMD;
		},
		async getDay() {
			try {
				this.loadingDay = true;
				this.dayResults = [];

				const response = await this.$store.dispatch("calendar/get", {
					date: this.selectedDay,
				});

				this.data = response;
			} finally {
				this.loadingDay = false;
			}
		},
	},
	watch: {
		selectedDay() {
			this.getDay();
		},
	},
};
</script>
