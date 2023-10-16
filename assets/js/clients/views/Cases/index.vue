<template>
	<div>
		<page-header>
			<template #title>Cases</template>
			<template #buttons>
				<b-button variant="primary" :to="{ name: 'cases.add' }" title="Add New">
					<font-awesome-icon icon="plus" fixed-width />
					<span>Add New</span>
				</b-button>
			</template>
		</page-header>

		<b-card-header header-tag="nav">
			<b-nav card-header tabs>
				<b-nav-item :to="{ name: 'cases' }" exact exact-active-class="active font-weight-bold">
					<span>All</span>
				</b-nav-item>
				<b-nav-item :to="{ name: 'cases.index.open' }" exact exact-active-class="active font-weight-bold">
					<span>Open</span>
				</b-nav-item>
				<b-nav-item :to="{ name: 'cases.index.utc' }" exact exact-active-class="active font-weight-bold">
					<span>UTC</span>
				</b-nav-item>
				<b-nav-item :to="{ name: 'cases.index.closed' }" exact exact-active-class="active font-weight-bold">
					<span>Closed</span>
				</b-nav-item>
				<b-nav-item :to="{ name: 'cases.index.empty' }" exact exact-active-class="active font-weight-bold">
					<span>Empty</span>
				</b-nav-item>
			</b-nav>
		</b-card-header>

		<router-view />
	</div>
</template>

<script setup></script>
