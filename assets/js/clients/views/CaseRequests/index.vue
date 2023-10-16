<template>
	<div>
		<page-header>
			<template #title>Requests</template>
			<template #buttons>
				<b-button variant="primary" :to="{ name: 'cases.add' }" title="Add New Case">
					<font-awesome-icon icon="plus" fixed-width />
					<span>Add New Case</span>
				</b-button>
			</template>
		</page-header>

		<b-card-header header-tag="nav">
			<b-nav card-header tabs>
				<b-nav-item :to="{ name: 'caseRequests' }" exact exact-active-class="active font-weight-bold">
					<span>All</span>
				</b-nav-item>
				<b-nav-item
					:to="{ name: 'caseRequests.index.open' }"
					exact
					exact-active-class="active font-weight-bold"
				>
					<span>Open</span>
				</b-nav-item>
				<b-nav-item :to="{ name: 'caseRequests.index.utc' }" exact exact-active-class="active font-weight-bold">
					<span>UTC</span>
				</b-nav-item>
				<b-nav-item
					:to="{ name: 'caseRequests.index.completed' }"
					exact
					exact-active-class="active font-weight-bold"
				>
					<span>Completed</span>
				</b-nav-item>
				<b-nav-item
					:to="{ name: 'caseRequests.index.unassigned' }"
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
