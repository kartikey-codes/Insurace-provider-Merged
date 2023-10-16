<template>
	<div v-bind="$attrs">
		<h2 class="h2 my-0 mb-1 font-weight-bold text-uppercase" v-if="showName" v-text="value.list_name" />
		<h3 v-if="value.date_of_birth" class="h5 mt-0 mb-1 text-muted text-uppercase">
			<span class="font-weight-bold">
				{{ $filters.formatDate(value.date_of_birth) }}
			</span>
			<span v-if="value.age != null && value.age != undefined">
				&mdash;
				<span>
					<font-awesome-icon v-if="value.is_birthday" icon="birthday-cake" class="text-muted" />
					<span class="font-weight-bold" v-text="value.age" />
				</span>
			</span>
		</h3>
		<h3 v-else class="h5 mt-0 mb-1 text-warning">(Missing DOB)</h3>
	</div>
</template>

<script type="text/javascript">
export default {
	name: "PatientHeader",
	props: {
		value: {
			required: true,
			type: Object,
			default: () => {
				return {
					list_name: null,
					date_of_birth: null,
					age: null,
					is_birthday: null,
				};
			},
		},
		showName: {
			default: true,
		},
	},
};
</script>
