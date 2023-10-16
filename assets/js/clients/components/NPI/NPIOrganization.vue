<script>
import { formatAddress } from "@/shared/helpers/addressHelper";

export default {
	name: "NPIOrganization",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					active: true,
					addresses: [],
					authorized_official_first_name: "",
					authorized_official_last_name: "",
					authorized_official_middle_name: "",
					authorized_official_name_prefix: "",
					authorized_official_telephone_number: "",
					authorized_official_title_or_position: "",
					created_epoch: null,
					enumeration_date: null,
					enumeration_type: null,
					identifiers: [],
					last_updated: null,
					last_updated_epoch: null,
					name: "",
					number: null,
					organization_name: "",
					organizational_subpart: false,
					other_names: [],
					taxonomies: [],
				};
			},
		},
	},
	computed: {
		active() {
			return this.value.active ? true : false;
		},
		name() {
			return this.value.name;
		},
		number() {
			return this.value.number;
		},
		contactFullName() {
			const firstName = this.value.authorized_official_first_name ?? "";
			const lastName = this.value.authorized_official_last_name ?? "";

			if (!firstName && !lastName) {
				return "";
			}

			return firstName + " " + lastName;
		},
		primaryAddress() {
			return this.value.addresses.find((address) => address.address_purpose == "LOCATION");
		},
		lastUpdated() {
			return new Date(this.value.last_updated).toLocaleDateString("en-US", {
				year: "numeric",
				month: "long",
				day: "numeric",
			});
		},
		fullPrimaryAddress() {
			return formatAddress(
				this.primaryAddress.address_1,
				this.primaryAddress.address_2,
				this.primaryAddress.city,
				this.primaryAddress.state,
				this.primaryAddress.postal_code
			);
		},
	},
	render() {
		return this.$scopedSlots.default({
			active: this.active,
			value: this.value,
			contactFullName: this.contactFullName,
			lastUpdated: this.lastUpdated,
			name: this.name,
			number: this.number,
			primaryAddress: this.primaryAddress,
			fullPrimaryAddress: this.fullPrimaryAddress,
		});
	},
};
</script>
