<template>
	<div class="py-5">
		<b-container class="mb-4">
			<b-row>
				<b-col class="py-2 text-center text-danger">
					<div class="mb-2">
						<font-awesome-icon icon="exclamation-triangle" size="6x" />
					</div>
					<h1 class="font-weight-bold">404 Error</h1>
				</b-col>
			</b-row>
		</b-container>

		<b-container class="my-4">
			<b-row>
				<b-col align="center">
					<h2>Sorry!</h2>
					<p>The page you requested could not be found or the link you used to get here is broken.</p>
					<p class="mt-4">
						<b-btn variant="primary" size="lg" to="/"> Return to Dashboard </b-btn>
					</p>
				</b-col>
			</b-row>
		</b-container>
	</div>
</template>

<script>
export default {
	name: "Error404",
};
</script>
