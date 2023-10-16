<template>
	<div>
		<page-header>
			<template #title>Calendar</template>
		</page-header>

		<Calendar />
	</div>
</template>

<script type="text/javascript">
import Calendar from "@/clients/components/Dashboard/Calendar.vue";

export default {
	name: "ViewCalendar",
	components: {
		Calendar,
	},
};
</script>
