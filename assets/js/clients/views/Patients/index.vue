<template>

	<paginated-results v-slot="{

		  doSearch,

		  empty,

		  hasNextPage,

		  hasPrevPage,

		  loading,

		  nextPage,

		  prevPage,

		  page,

		  pages,

		  refresh,

		  results,

		  total,

	}" v-bind="{

	action,

	filters,

	search,

	perPage,

	sort,

	sortDescending,

}">

		  <page-header v-bind="{ loading, total }">

				<template #title>Patients</template>

				<template #buttons>

					  <b-button variant="success" @click="() => exportToCSV(total)" title="Export to CSV">

							<font-awesome-icon icon="file-csv" fixed-width />

							<span>Export to CSV</span>

					  </b-button>





					  <b-button variant="primary" :to="{ name: 'patients.add' }" title="Add New">

							<font-awesome-icon icon="plus" fixed-width />

							<span>Add New</span>

					  </b-button>

				</template>

		  </page-header>



		  <b-container fluid class="mt-4">

				<b-row>

					  <b-col cols="12" md="12" lg="4" order="1" class="mb-4 mb-lg-0">

							<b-form @submit.prevent="doSearch">

								  <search-input v-model="search" v-bind="{ loading }" />

							</b-form>

					  </b-col>

					  <b-col cols="6" md="6" lg="2" order="2" class="text-left text-md-left text-lg-left">

							<b-dropdown split @click="filtering = !filtering" :pressed="filtering">

								  <template #button-content>

										<font-awesome-icon icon="filter" fixed-width />

										<span>Filter</span>

								  </template>

								  <b-dropdown-item-button @click="resetFilters" :disabled="loading"

										title="Clear all filters">

										<span>Clear Filters</span>

								  </b-dropdown-item-button>

								  <b-dropdown-item-button @click="refresh" :disabled="loading" title="Refresh">

										<span>Refresh</span>

								  </b-dropdown-item-button>

							</b-dropdown>

					  </b-col>

					  <b-col cols="6" md="6" lg="6" order="3" class="text-right">

							<simple-pagination v-bind="{ loading, nextPage, prevPage, hasPrevPage, hasNextPage }" />

					  </b-col>

				</b-row>

		  </b-container>



		  <b-container fluid>

				<b-row>

					  <b-col cols="12">

							<b-collapse v-model="filtering" class="py-2">

								  <b-form @submit.prevent="doSearch">

										<b-card>

											  <IndexFilters v-model="filters" :disabled="loading" />

										</b-card>

								  </b-form>

							</b-collapse>

					  </b-col>

				</b-row>

		  </b-container>



		  <b-container v-if="loading && empty" class="my-4">

				<loading-indicator size="4x" class="my-5" />

		  </b-container>

		  <b-container fluid v-else-if="!empty" class="mt-4">

				<b-row>

					  <b-col cols="12" class="mb-4">

							<ResultTable :data="results" :loading="loading" :sort.sync="sort"

								  :sort-descending.sync="sortDescending" @clicked="toView" />

					  </b-col>

				</b-row>

		  </b-container>

		  <b-container fluid v-else class="my-4">

				<empty-result>

					  No patients found

					  <template #content> No patients have been created or match your search. </template>

				</empty-result>

		  </b-container>

	</paginated-results>

</template>



<script setup>

import { ref, reactive } from "vue";

import { useRouter } from "vue-router/composables";

import { getIndex } from "@/clients/services/patients";



import ResultTable from "@/clients/components/Patients/Table.vue";

import IndexFilters from "@/clients/components/Patients/Filters.vue";



const defaultFilters = {

	date_of_birth: null,

	sex: null,

	marital_status: null,

};



const filters = reactive({ ...defaultFilters });

const filtering = ref(false);

const resetFilters = () => Object.assign(filters, defaultFilters);



const router = useRouter();

const toView = function (entity) {

	router.push({

		  name: "patients.view",

		  params: {

				id: entity.id,

		  },

	});

};



const action = getIndex;

const search = ref("");

const sort = ref("last_name");

const sortDescending = ref(false);

const perPage = ref(15);

const exportToCSV = async (total) => {

	try {

		  const response = await action({

				search,

				filters,

				sort: sort.value,

				sortDescending: sortDescending.value,

				page: 1, // You might want to fetch all data at once for the CSV export

				perPage: total, // Assuming total is the total number of records

		  });



		  const patients = response.data; // Assuming the response has a 'data' property containing patient records



		  // Format data and create CSV content

		  const csvContent = "Last Name,First Name,Middle Name,Date of Birth,Medical Record Number,Gender,Marital Status\n" +

				patients.map(patient => {

					  // Extract relevant fields from the patient object

					  const { last_name, first_name, middle_name, date_of_birth, medical_record_number, sex, marital_status } = patient;

					  // Format date_of_birth to MM/DD/YYYY

					  const formattedDateOfBirth = new Date(date_of_birth).toLocaleDateString("en-US");

					  // Create a CSV row

					  return `${last_name},${first_name},${middle_name},${formattedDateOfBirth},${medical_record_number},${sex},${marital_status}`;

				}).join("\n");







		  // Create a Blob and download the CSV file

		  const blob = new Blob([csvContent], { type: "text/csv" });

		  const url = URL.createObjectURL(blob);

		  const a = document.createElement("a");

		  a.href = url;

		  a.download = "patients.csv";

		  document.body.appendChild(a);

		  a.click();

		  window.URL.revokeObjectURL(url);

		  document.body.removeChild(a);

	} catch (error) {

		  console.error("Error exporting data to CSV:", error);

	}

};

</script>