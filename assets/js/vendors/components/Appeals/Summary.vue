<template>
	<div v-bind="$attrs">
		<b-row>
			<b-col cols="12" lg="6" class="mb-4 mb-lg-0">
				<p class="text-muted mb-2">
					Letter dated {{ $filters.formatDate(appeal.letter_date) }} and received on
					{{ $filters.formatDate(appeal.received_date) }}.
				</p>
				<p
					v-if="!appeal.is_finished"
					class="mb-2"
					:class="appeal.is_overdue ? 'text-danger font-weight-bold' : 'text-muted'"
				>
					Response due {{ $filters.fromNow(appeal.due_date) }} on
					<span>{{ $filters.formatDate(appeal.due_date) }}</span
					>.
				</p>
				<p v-if="appeal.hearing_date" class="text-muted mb-2">
					Hearing on {{ $filters.formatDate(appeal.hearing_date) }}{{ !appeal.hearing_time ? "." : "" }}
					<span v-if="appeal.hearing_time">at {{ $filters.formatTime(appeal.hearing_time) }}.</span>
				</p>
				<p v-if="appeal.defendable === true || appeal.defendable === false" class="mb-2 font-weight-bold">
					<span v-if="appeal.defendable === true" class="text-success">
						<font-awesome-icon icon="check" fixed-width />
						<span>Defendable</span>
					</span>
					<span v-else-if="appeal.defendable === false" class="text-danger">
						<font-awesome-icon icon="times" fixed-width />
						<span>Not Defendable</span>
					</span>
				</p>
				<div v-if="hasNotDefendableReasons">
					<p class="small text-muted font-weight-bold mb-0">Reasons:</p>
					<ul class="mb-2">
						<li v-for="reason in appeal.not_defendable_reasons" :key="reason.id" class="ml-0 pl-0 mb-0">
							{{ reason.name }}
						</li>
					</ul>
				</div>
			</b-col>
			<b-col cols="12" lg="6" class="mb-4 mb-lg-0">
				<b-list-group v-if="hasReferenceNumbers">
					<b-list-group-item
						v-for="referenceNumber in appeal.appeal_reference_numbers"
						:key="referenceNumber.id"
						class="d-flex justify-content-between align-items-center"
					>
						<div class="text-muted font-weight-normal">
							{{ referenceNumber.reference_number.name }}
						</div>
						<div>
							{{ referenceNumber.value }}
						</div>
					</b-list-group-item>
				</b-list-group>
			</b-col>
		</b-row>
	</div>
</template>

<script>
export default {
	name: "AppealSummary",
	components: {},
	props: {
		appeal: {
			type: Object,
			required: true,
			default: () => {
				return {
					id: null,
					case_id: null,
					defendable: null,
					letter_date: null,
					received_date: null,
					due_date: null,
					appeal_type: {
						name: null,
					},
					appeal_level: {
						name: null,
					},
					appeal_reference_numbers: [],
					not_defendable_reasons: [],
					is_overdue: null,
					is_finished: null,
				};
			},
		},
	},
	computed: {
		hasReferenceNumbers() {
			return this.appeal.appeal_reference_numbers && this.appeal.appeal_reference_numbers.length > 0;
		},
		hasNotDefendableReasons() {
			return this.appeal.not_defendable_reasons && this.appeal.not_defendable_reasons.length > 0;
		},
	},
};
</script>
