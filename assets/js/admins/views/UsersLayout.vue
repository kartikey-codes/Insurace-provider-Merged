<template>
	<b-container fluid>
		<b-row>
			<b-col cols="12">
				<h1 class="h3">Users</h1>

				<router-view />
			</b-col>
		</b-row>
	</b-container>
</template>

<script>
export default {
	name: "UsersLayout",
};
</script>
