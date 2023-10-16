<template>
	<div>
		<page-header>
			<template #title>Outgoing</template>
		</page-header>

		<b-card-header header-tag="nav">
			<b-nav card-header tabs>
				<b-nav-item :to="{ name: 'outgoingDocuments' }" exact exact-active-class="active font-weight-bold">
					<span>Pending</span>
				</b-nav-item>
				<b-nav-item
					:to="{ name: 'outgoingDocuments.delivered' }"
					exact
					exact-active-class="active font-weight-bold"
				>
					<span>Delivered</span>
				</b-nav-item>
				<b-nav-item
					:to="{ name: 'outgoingDocuments.failed' }"
					exact
					exact-active-class="active font-weight-bold"
				>
					<span>Failed</span>
				</b-nav-item>
				<b-nav-item
					:to="{ name: 'outgoingDocuments.cancelled' }"
					exact
					exact-active-class="active font-weight-bold"
				>
					<span>Cancelled</span>
				</b-nav-item>
			</b-nav>
		</b-card-header>

		<b-container fluid class="my-4">
			<router-view />
		</b-container>
	</div>
</template>

<script setup></script>
