<script>
import { formatAddress } from "@/shared/helpers/addressHelper";

export default {
	name: "NPIIndividual",
	props: {
		value: {
			type: Object,
			default: () => {
				return {
					active: true,
					addresses: [],
					created_epoch: null,
					credential: "",
					enumeration_date: null,
					enumeration_type: null,
					first_name: "",
					gender: "",
					identifiers: [],
					last_name: "",
					last_updated: null,
					last_updated_epoch: null,
					name: "",
					number: null,
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
		primaryAddress() {
			return this.value.addresses.find((address) => address.address_purpose == "LOCATION");
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
			name: this.name,
			number: this.number,
			primaryAddress: this.primaryAddress,
			fullPrimaryAddress: this.fullPrimaryAddress,
		});
	},
};
</script>
