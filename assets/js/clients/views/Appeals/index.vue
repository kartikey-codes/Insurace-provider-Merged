<template>
	<div>
		<page-header>
			<template #title>Appeals</template>
			<template #buttons>
				<b-button variant="primary" :to="{ name: 'cases.add' }" title="Add New Case">
					<font-awesome-icon icon="plus" fixed-width />
					<span>Add New Case</span>
				</b-button>
			</template>
		</page-header>

		<b-card-header header-tag="nav">
			<b-nav card-header tabs>
				<b-nav-item :to="{ name: 'appeals' }" exact exact-active-class="active font-weight-bold">
					<span>All</span>
				</b-nav-item>
				<b-nav-item :to="{ name: 'appeals.index.open' }" exact exact-active-class="active font-weight-bold">
					<span>Open</span>
				</b-nav-item>
				<b-nav-item :to="{ name: 'appeals.index.utc' }" exact exact-active-class="active font-weight-bold">
					<span>UTC</span>
				</b-nav-item>
				<b-nav-item
					:to="{ name: 'appeals.index.cancelled' }"
					exact
					exact-active-class="active font-weight-bold"
				>
					<span>Cancelled</span>
				</b-nav-item>
				<b-nav-item :to="{ name: 'appeals.index.closed' }" exact exact-active-class="active font-weight-bold">
					<span>Closed</span>
				</b-nav-item>
				<b-nav-item
					:to="{ name: 'appeals.index.unassigned' }"
					exact
					exact-active-class="active font-weight-bold"
				>
					<span>Unassigned</span>
				</b-nav-item>
			</b-nav>
		</b-card-header>

		<router-view />
	</div>
</template>

<script setup></script>
