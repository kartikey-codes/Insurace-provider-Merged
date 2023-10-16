<template>
	<div v-if="editing">
	  <validation-observer v-bind="$attrs" ref="observer" v-slot="{ invalid }">
		<b-form >
		  <slot name="header" ></slot>
		  <b-card-body>
			<b-row>
			  <b-col cols="12">
				<validation-provider
				  vid="ftpHost"
				  name="Host"
				  :rules="{ required: true, min: 2, max: 50 }"
				  v-slot="validationContext"
				>
				  <b-form-group
					label="Host"
					label-for="ftpHost"
					description="The IP address or domain name of the FTP server."
					label-cols-lg="4"
					label-cols-xl="12"
				  >
					<b-form-input
					  autofocus
					  name="ftpHost"
					  type="text"
					  v-model="config.host"
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
			  <b-col cols="12" xl="6">
				<validation-provider
				  vid="ftpUsername"
				  name="Username"
				  :rules="{ required: true, min: 1, max: 250 }"
				  v-slot="validationContext"
				>
				  <b-form-group
					label="Username"
					label-for="ftpUsername"
					description="FTP server username."
					label-cols-lg="4"
					label-cols-xl="12"
				  >
					<b-form-input
					  name="ftpUsername"
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
  
			  <b-col cols="12" xl="6">
				<validation-provider
				  vid="ftpPassword"
				  name="Password"
				  :rules="{ required: true, min: 1, max: 250 }"
				  v-slot="validationContext"
				>
				  <b-form-group
					label="Password"
					label-for="ftpPassword"
					description="FTP server password."
					label-cols-lg="4"
					label-cols-xl="12"
				  >
					<b-form-input
					  name="ftpPassword"
					  type="password"
					  v-model="config.password"
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
			  <b-col cols="12">
				<validation-provider
				  vid="ftpPath"
				  name="Path"
				  :rules="{ required: true, min: 1, max: 250 }"
				  v-slot="validationContext"
				>
				  <b-form-group
					label="Path"
					label-for="ftpPath"
					description="The path on the FTP server from which to download files."
					label-cols-lg="4"
					label-cols-xl="12"
				  >
					<b-form-input
					  name="ftpPath"
					  type="text"
					  v-model="config.path"
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
			  <b-col cols="12">
				<validation-provider
				  vid="ftpfile"
				  name="file"
				  :rules="{ required: true, min: 1, max: 250 }"
				  v-slot="validationContext"
				>
				  <b-form-group
					label="File"
					label-for="ftpfile"
					description="The File name on the FTP server from which to download files."
					label-cols-lg="4"
					label-cols-xl="12"
				  >
					<b-form-input
					  name="ftpfile"
					  type="text"
					  v-model="config.ftpfile"
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
		  </b-card-body>
		  <hr />
		  <b-card-footer>
			<b-row class="d-flex align-items-center">
			  <b-col cols="6" class="text-left">
				<b-button
				 
				  type="button"
				  variant="light"
				  @click="editing = false"
				  :disabled="saving"
				  class="px-4"
				>
				  Cancel
				</b-button>
			  </b-col>
			  <b-col cols="6" class="text-right">
				<b-button type="submit" variant="primary" @click="download" class="px-4">
				  Download
				</b-button>
			  </b-col>
			</b-row>
		  </b-card-footer>
		</b-form>
	  </validation-observer>
	</div>
	<div v-else>
	  <!-- Display configuration details here -->
	</div>
  </template>

  <script type="text/javascript">
  import { formatErrors, getValidationState } from "@/validation";
  // Add other imports as needed
  import axios from "axios"; // Import Axios


  const INTEGRATION_NAME = "ftp"; // Set integration name accordingly
  
  export default {
	name: "FtpConfiguration",
	data() {
	  return {
		editing: true,
		saving: false,
		config: {
		  host: "",
		  username: "",
		  password: "",
		  path: "",
		  file: "",
		  // Add other FTP configuration properties here
		},
	  };
	},
	computed: {
	  // Add computed properties as needed
	},
	methods: {
	  getValidationState,
	  async download() {
  try {
    // Use Axios to make a request to your PHP backend
    const resp = await axios.post("/client/ftp", {
      host: this.config.host,
      username: this.config.username,
      password: this.config.password,
      path: this.config.path,
	  file: this.config.path,
      // Other parameters you might need
    });

	console.log("yes", resp);

    // Handle the response from the PHP script

  } catch (e) {
    // Handle errors and notify user
  } finally {
    console.log(1);
  }
},

  },
};
  </script>
  
  <style scoped>
  /* Add component-specific styles here */
  </style>
  