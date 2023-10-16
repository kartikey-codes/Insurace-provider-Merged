<template>
	<b-tabs card pills v-model="tabIndex">
		<b-tab no-body>
			<template #title>Packet</template>
			<b-card-body>
				<b-row v-if="allFiles.length <= 0">
					<b-col cols="12">
						<b-alert show variant="info" class="mb-0 p-4">
							<font-awesome-icon icon="info-circle" fixed-width class="mr-2" />
							Select PDF files from case and appeal documents to combine into a packet for outgoing
							submission.
						</b-alert>
					</b-col>
				</b-row>
				<b-row v-else>
					<b-col cols="12">
						<p class="text-muted">
							Drag and drop to reorder PDFs. Any existing packet file will be overwritten.
						</p>
						<b-list-group>
							<draggable v-model="orderedList">
								<b-list-group-item
									:disabled="generating"
									v-for="file in orderedList"
									:key="file.key"
									class="cursor-grabbable"
									:variant="file.valid ? '' : 'light'"
								>
									<div class="d-flex justify-content-start align-items-top">
										<b-avatar icon variant="light" class="mr-2">
											<font-awesome-icon icon="sort" fixed-width />
										</b-avatar>
										<div class="flex-fill d-flex justify-content-between align-items-top">
											<div>
												<p class="mb-0">{{ file.basename || file.file }}</p>
												<p v-if="file.extension" class="mb-0">
													<b-badge pill variant="light">
														{{ file.source }}
													</b-badge>

													<span class="small text-muted">{{ file.extension }}</span>
													<b-badge v-if="!file.valid" variant="warning" class="mb-0">
														<font-awesome-icon icon="exclamation-triangle" fixed-width />
														This file type is not supported for merging.
													</b-badge>
												</p>
											</div>
											<div>
												<b-button
													variant="danger"
													@click="removeFile(file)"
													title="Remove File"
												>
													<font-awesome-icon icon="remove" fixed-width />
												</b-button>
											</div>
										</div>
									</div>
								</b-list-group-item>
							</draggable>
						</b-list-group>
					</b-col>
				</b-row>
			</b-card-body>
			<b-card-footer>
				<b-row>
					<b-col cols="12" class="text-right">
						<b-button
							variant="primary"
							@click="generate"
							:disabled="generating || !hasFiles || hasInvalidFiles"
						>
							<span v-if="!generating">Create Packet</span>
							<span v-else>
								<font-awesome-icon icon="circle-notch" spin />
								Generating...
							</span>
						</b-button>
					</b-col>
				</b-row>
			</b-card-footer>
		</b-tab>
		<b-tab no-body lazy :disabled="!exists">
			<template #title>Preview</template>
			<b-card-body>
				<b-row>
					<b-col cols="12" class="text-right">
						<b-button variant="secondary" @click="download" :disabled="generating || !exists">
							Download
						</b-button>
					</b-col>
				</b-row>
			</b-card-body>
			<div class="overflow-y-auto" style="max-height: 40rem">
				<pdf-frame v-show="value.pdf_url" :url="value.pdf_url" />
			</div>
		</b-tab>
		<b-tab no-body lazy :disabled="!exists">
			<template #title>Submit</template>

			<transition name="fade" mode="out-in">
				<b-card-body v-if="submitted">
					<b-alert show variant="success" class="p-4 mb-0">
						<h6 class="h6 font-weight-bold">
							<font-awesome-icon icon="check-circle" fixed-width class="mr-2" />
							Response Submitted!
						</h6>
						<p class="mb-0">Your appeal response packet has been queued for outgoing delivery.</p>
					</b-alert>
				</b-card-body>
				<b-card-body v-else>
					<div v-if="agency">
						<h6>Submit to Agency</h6>

						<div class="p-4 mb-4 d-flex justify-start align-items-top shadow-sm">
							<b-avatar
								rounded
								:variant="agency.active ? 'primary' : 'light'"
								class="mr-3 px-0 text-center"
							>
								<font-awesome-icon icon="building" class="px-0 mx-0" />
							</b-avatar>
							<b-row class="flex-fill">
								<b-col cols="12" class="text-left">
									<h6 class="h6 font-weight-bold mb-1 text-break">
										{{ agency.name }}
									</h6>
									<p v-if="agency.full_address" class="small mb-1 text-muted" title="Facility">
										<font-awesome-icon icon="location-dot" fixed-width class="d-none d-sm-inline" />
										{{ agency.full_address }}
									</p>
									<div>
										<b-badge variant="light" v-if="agency.third_party_contractor">
											3rd Party
										</b-badge>
										<b-badge pill variant="light" v-if="!agency.active"> Inactive </b-badge>
									</div>
								</b-col>
							</b-row>
						</div>

						<b-alert show v-if="!outgoingProfile" variant="warning">
							No delivery settings have been configured for this agency. Outgoing documents will be queued
							for manual delivery.
						</b-alert>
						<div v-else>
							<h6>Primary Method: {{ agency.outgoing_primary_method_label }}</h6>
							<b-list-group>

<b-list-group-item v-if="outgoingProfile.full_mail_to_address">

	<b-form-checkbox class="mr-3"

		name="Mail"

		v-model="localValue.mail"

		:disabled="busy"

	>

		Mail

	</b-form-checkbox>

	<p v-if="outgoingProfile.mail_to_name" class="mb-0">

		{{ outgoingProfile.mail_to_name }}

	</p>

	<p v-if="outgoingProfile.mail_to_department" class="mb-0">

		{{ outgoingProfile.mail_to_department }}

	</p>

	<p v-if="outgoingProfile.full_mail_to_address" class="mb-0">

		{{ outgoingProfile.full_mail_to_address }}

	</p>

	<!-- <p class="small text-muted mb-0">Mail</p> -->

</b-list-group-item>

<b-list-group-item class="clearfix" v-if="outgoingProfile.email">

	<!-- <p class="small text-muted mb-0">Email</p> -->

	<b-form-checkbox class="mr-3"

		name="Email"

		v-model="localValue.email"

		:disabled="busy"

	>

		Email

	</b-form-checkbox>

	<p class="mb-0">

		{{ outgoingProfile.email }}

	</p>

   

</b-list-group-item>

<b-list-group-item v-if="outgoingProfile.fax_number">

	<!-- <p class="small text-muted mb-0">Fax</p> -->

	<b-form-checkbox class="mr-3"

		name="Fax"

		v-model="localValue.fax"

		:disabled="busy"

	>

		Fax

	</b-form-checkbox>

	<p class="mb-0">

		{{ outgoingProfile.fax_number }}

	</p>

   

</b-list-group-item>

<b-list-group-item v-if="outgoingProfile.electronic_website">

	<b-form-checkbox class="mr-3"

		name="Website"

		v-model="localValue.website"

		:disabled="busy"

	>

		Website

	</b-form-checkbox>

	<p class="mb-0">

		{{ outgoingProfile.electronic_website }}

	</p>

	<!-- <p class="small text-muted mb-0">Website</p> -->

   

</b-list-group-item>



</b-list-group>
						</div>
					</div>
					<!-- <empty-result v-else icon="question-circle">
						No submit settings
						<template #content>
							<p class="font-weight-bold mb-0">
								No delivery settings could be found based on this appeal.
							</p>
							<p class="mb-0">
								Provide an Audit Reviewer to this appeal with an associated Agency to set up delivery.
							</p>
							<p class="mb-0">
								Submitting will place this appeal packet in the outgoing queue for manual delivery.
							</p>
						</template>
					</empty-result> -->
					<!-- Add Search Bar -->
					<!-- <b-form-input v-model="searchText" placeholder="Search agency" class="mt-2"></b-form-input> -->
					
					<!-- Add Search Button -->
					<!-- <b-button @click="delivery" variant="primary" class="mt-2">Sea</b-button> -->
					<b-form-group label="Delivery Method"  label-cols-lg="4">
						<b-form-select label=" Delivery Method " v-model="selectedOption" class="mt-2">
							<option value="Email">Email</option>
							<option value="Fax">Fax</option>
							<option value="Website">Website</option>
						</b-form-select>
					</b-form-group>
				</b-card-body>
			</transition>
			<b-card-footer>
				<b-row>
					<b-col cols="12" class="text-right">
						<b-button v-if="!submitted" variant="primary" @click="submitPacket" :disabled="submitting">
							Submit Packet
						</b-button>
						<b-button v-else variant="info" :to="{ name: 'outgoingDocuments' }">
							View Outgoing Documents
							<font-awesome-icon icon="chevron-right" fixed-width />
						</b-button>
					</b-col>
					
				</b-row>
			</b-card-footer>
		</b-tab>
	</b-tabs>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";
import { getExtension, getBasename, extensionMergesIntoPdf } from "@/shared/helpers/fileHelper";
import draggable from "vuedraggable";
import PdfFrame from "@/shared/components/PdfFrame.vue";

export default {
	name: "AppealResponse",
	components: {
		PdfFrame,
		draggable,
	},
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					id: null,
					case_id: null,
					appeal_level: {
						id: null,
						name: null,
					},
					appeal_type: {
						id: null,
						name: null,
					},
					is_overdue: null,
					is_finished: null,
					can_cance: null,
					can_close: null,
					can_delete: null,
					can_reopen: null,
					can_submit: null,
					pdf_title: null,
					pdf_url: null,
				};
			},
		},
		disabled: {
			type: Boolean,
			default: false,
		},
		appealFiles: {
			type: Array,
			default: () => [],
		},
		showCaseFiles: {
			type: Boolean,
			default: false,
		},
		caseFiles: {
			type: Array,
			default: () => [],
		},
	},
	computed: {
		localValue: {
			get() {
				return this.value;
			},
			set(val) {
				this.$emit("input", val);
			},
		},
		allFiles() {
			return [
				...this.caseFiles.map((caseFile) => {
					const extension = getExtension(caseFile);

					return {
						key: "c_" + caseFile, // for v-for
						source: "Case",
						file: caseFile,
						basename: getBasename(caseFile),
						extension: extension,
						valid: extensionMergesIntoPdf(extension),
					};
				}),
				...this.appealFiles.map((appealFile) => {
					const extension = getExtension(appealFile);

					return {
						key: "a_" + appealFile, // for v-for
						source: "Appeal",
						file: appealFile,
						basename: getBasename(appealFile),
						extension: getExtension(appealFile),
						valid: extensionMergesIntoPdf(extension),
					};
				}),
			];
		},
		hasFiles() {
			return this.allFiles.length > 0;
		},
		hasInvalidFiles() {
			return this.allFiles.some((file) => file.valid == false);
		},
		canSubmit() {
			return this.value.can_submit;
		},
		agency() {
			return this.value?.audit_reviewer?.agency ?? false;
		},
		outgoingProfile() {
			return this.value?.audit_reviewer?.agency?.outgoing_profile ?? false;
		},
	},
	data() {
		return {
			tabIndex: 1,
			exists: false,
			downloading: false,
			generating: false,
			submitting: false,
			submitted: false,
			orderedList: this.allFiles,
		};
	},
	mounted() {
		this.checkExists();
	},
	methods: {
		removeFile(file) {
			this.$emit("remove", file);
		},
		async checkExists() {
			const response = await this.$store.dispatch("appealPackets/exists", {
				id: this.value.id,
			});

			this.exists = response.exists || false;
		},
		async download() {
			try {
				this.downloading = true;

				await this.$store.dispatch("appealPackets/download", {
					id: this.value.id,
				});
			} finally {
				this.downloading = false;
			}
		},
		async generate() {
			try {
				this.generating = true;

				const response = await this.$store.dispatch("appealPackets/generate", {
					id: this.value.id,
					case_files: this.caseFiles,
					appeal_files: this.appealFiles,
					ordered_list: this.orderedList,
				});

				this.$emit("generated", response);

				this.$store.dispatch("notify", {
					variant: "primary",
					title: "Packet Created",
					message: `Appeal packet PDF has been generated. You may now preview and submit it.`,
				});

				await this.checkExists();
				this.tabIndex++;
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Packet Generation Failed",
					message:
						"Error attempting to create packet. Please ensure selected files are valid PDFs. Contact support if the issue persists.",
					variant: "warning",
				});
			} finally {
				this.generating = false;
			}
		},
		async submitPacket() {
			try {
				this.submitting = true;

				const response = await this.$store.dispatch("appealPackets/submit", {
					id: this.value.id,
				});

				this.$emit("submitted", response);
				this.submitted = true;

				this.$store.dispatch("notify", {
					variant: "primary",
					title: "Packet Submitted",
					message: `Appeal packet has been submitted.`,
				});

				this.$store.dispatch("outgoingDocuments/count");
			} catch (e) {
				this.$store.dispatch("apiError", {
					error: e,
					title: "Submission Failed",
					message: "Unable to submit packet.",
				});
			} finally {
				this.submitting = false;
			}
		},
	},
	watch: {
		allFiles(newVal, oldVal) {
			this.orderedList = newVal;
		},
	},
};
</script>
