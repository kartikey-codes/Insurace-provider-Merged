<template>
	<b-container fluid class="my-5">
		<b-row>
			<b-col cols="12">
				<empty-result>
					No document selected.
					<template #content>
						<div class="mb-4">Select an incoming document from the list to view.</div>

						<b-button
							v-b-toggle.sidebar-list
							title="Hide or display the incoming document list"
							variant="primary"
							size="lg"
						>
							Select Document
						</b-button>
					</template>
				</empty-result>
			</b-col>
		</b-row>
	</b-container>
</template>

<script>
export default {
	name: "IndexViewHome",
};
</script>
