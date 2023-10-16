<template>
	<div>
	  <div v-if="editing">
		<validation-observer v-bind="$attrs" ref="observer" v-slot="{ invalid }">
		  <b-form @submit.prevent="save">
			<slot name="header" v-bind="{ isConnected, save, saving, loading }"></slot>
			<b-card-body>
			  <b-row>
				<b-col cols="12" xl="6">
				  <validation-provider
					vid="username"
					name="Patient Name"
					:rules="{ required: true, min: 1, max: 250 }"
					v-slot="validationContext"
				  >
					<b-form-group
					  label="Patient Name"
					  label-for="username"
					  description="Enter the complete patient name"
					  label-cols-lg="4"
					  label-cols-xl="12"
					>
					  <b-form-input
						name="username"
						type="text"
						v-model="config.username"
						:state="getValidationState(validationContext)"
						:disabled="saving"
						required
					  />
					  <b-form-invalid-feedback
						v-for="error in validationContext.errors"
						:key="error"
						v-text="error"
					  />
					</b-form-group>
				  </validation-provider>
				</b-col>
			  </b-row>
  
			  <b-row>
				<b-col cols="12" class="text-right">
				  <b-button @click="searchPatient" variant="primary">Search Patient</b-button>
				</b-col>
			  </b-row>
  
			</b-card-body>
		  </b-form>
		</validation-observer>
	  </div>
  
	  <div v-else>
		<b-card-body>
		  <b-row class="d-flex justify-content-betwen align-items-center">
			<b-col cols="6" class="text-left">
			  <div v-if="mirthConnect.description">
				<h6 class="h6">Description</h6>
				<p class="text-muted">{{ mirthConnect.description }}</p>
			  </div>
			</b-col>
  
			<b-col cols="6" class="text-right">
			  <b-button @click="editing=!editing" variant="primary">
				<font-awesome-icon icon="edit" fixed-width />
				Edit Configuration
			  </b-button>
			</b-col>
		  </b-row>
		</b-card-body>
		<b-card-footer class="d-flex justify-content-between align-items-center">
		  <b-badge v-if="mirthConnect.connected" variant="primary" pill class="mr-3">Connected</b-badge>
		  <b-badge v-else variant="warning" pill class="mr-3">Not Connected</b-badge>
  
		  <p class="small text-muted mb-0">Last updated {{ $filters.fromNow(mirthConnect.modified) }}</p>
		</b-card-footer>
	  </div>
  
	  <div v-if="patientDetails && isSearchButtonClicked">
		<h4>Patient Details:</h4>
		<ul>
		  <li><strong>Name:</strong> {{ patientDetails.name }}</li>
		  <li><strong>Date of Birth:</strong> {{ patientDetails.date_of_birth }}</li>
		  <li><strong>Address:</strong> {{ patientDetails.address }}</li>
		  <li><strong>Diseases:</strong> {{ patientDetails.diseases.join(', ') }}</li>
		  <li><strong>Treating Doctor:</strong> {{ patientDetails.treating_doctor }}</li>
		  <li><strong>Appointment Date:</strong> {{ patientDetails.appointment_date }}</li>
		</ul>
	  </div>

	  
	</div>
  </template>
  
  <script>
  import { mapGetters } from "vuex";
  import { formatErrors, getValidationState } from "@/validation";
  import FormApiSwitch from "@/clients/components/Layout/form-api-switch.vue";
  import axios from 'axios';
  
  const INTEGRATION_NAME = "mirthConnect"; // For PHP
  
  export default {
	name: "MirthConnect",
	components: {
	  FormApiSwitch,
	},
	data() {
	  return {
		editing: true,
		saving: false,
		description: "",
		config: {
		  host: "",
		  port: 8080,
		  path: "api",
		  username: "",
		  password: "",
		},
		patients: [
		  {
			name: "John Doe",
			date_of_birth: "1990-05-15",
			address: "123 Main Street, City, Country",
			diseases: ["Flu", "Hypertension"],
			treating_doctor: "Dr. Smith",
			appointment_date: "2023-07-05",
		  },
		  {
			name: "Jane Smith",
			date_of_birth: "1985-10-20",
			address: "456 Elm Avenue, Town, Country",
			diseases: ["Diabetes", "Arthritis"],
			treating_doctor: "Dr. Johnson",
			appointment_date: "2023-07-08",
		  },
			{
				name: "Michael Brown",
				date_of_birth: "1978-03-12",
				address: "789 Oak Road, Village, Country",
				diseases: ["Asthma", "Allergies"],
				treating_doctor: "Dr. Davis",
				appointment_date: "2023-07-10"
			},
			{
				name: "Emily Wilson",
				date_of_birth: "1995-08-25",
				address: "321 Pine Lane, Town, Country",
				diseases: ["Migraine", "Depression"],
				treating_doctor: "Dr. Anderson",
				appointment_date: "2023-07-12"
			},
			{
				name: "David Johnson",
				date_of_birth: "1982-12-07",
				address: "987 Maple Street, City, Country",
				diseases: ["Heart Disease", "High Cholesterol"],
				treating_doctor: "Dr. Wilson",
				appointment_date: "2023-07-15"
			},
			{
				name: "Sarah Davis",
				date_of_birth: "1998-04-30",
				address: "654 Oak Road, Village, Country",
				diseases: ["Anxiety", "Insomnia"],
				treating_doctor: "Dr. Brown",
				appointment_date: "2023-07-18"
			},
			{
				name: "Daniel Thompson",
				date_of_birth: "1973-09-17",
				address: "123 Elm Avenue, Town, Country",
				diseases: ["Arthritis", "Diabetes"],
				treating_doctor: "Dr. Johnson",
				appointment_date: "2023-07-20"
			},
			{
				name: "Olivia Wilson",
				date_of_birth: "1989-06-08",
				address: "456 Pine Lane, Town, Country",
				diseases: ["Allergies", "Asthma"],
				treating_doctor: "Dr. Thompson",
				appointment_date: "2023-07-22"
			},
			{
				name: "Liam Anderson",
				date_of_birth: "1992-01-19",
				address: "789 Oak Road, Village, Country",
				diseases: ["Depression", "Migraine"],
				treating_doctor: "Dr. Wilson",
				appointment_date: "2023-07-25"
			},
			{
				name: "Sophia Brown",
				date_of_birth: "1987-11-03",
				address: "987 Maple Street, City, Country",
				diseases: ["High Cholesterol", "Heart Disease"],
				treating_doctor: "Dr. Thompson",
				appointment_date: "2023-07-28"
			}
		],
		patientDetails: null,
	  };
	},
	computed: {
	  ...mapGetters({
		// Existing computed properties
	  }),
	},
	methods: {
	  save() {
		// Existing save method logic
	  },
	  getValidationState() {
		// Existing getValidationState method logic
	  },
	  searchPatient() {
		const inputValue = this.config.username;
  
		// Search for the patient in the list based on the entered username
		const foundPatient = this.patients.find(
		  (patient) => patient.name.toLowerCase() === inputValue.toLowerCase()
		);
  
		if (foundPatient) {
		  // If patient found, display the patient details
		  this.patientDetails = foundPatient;
  
		  // Save patient details in local storage
		  localStorage.setItem("patientDetails", JSON.stringify(foundPatient));
		} else {
		  // If patient not found, display a message
		  this.patientDetails = null;
		}
		this.isSearchButtonClicked = true;
	  },
	  retrievePatientDetailsFromLocalStorage() {
		const storedPatientDetails = localStorage.getItem("patientDetails");
  
		if (storedPatientDetails) {
		  this.patientDetails = JSON.parse(storedPatientDetails);
		}
	  },
	},
	mounted() {
	  this.retrievePatientDetailsFromLocalStorage();
	},
  };
  </script>
  