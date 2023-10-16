/**
 * Format full address as string
 *
 * @todo Make this better
 * @returns string
 */
export function formatAddress(address1, address2, city, state, zip) {
	if (address2) {
		return `${address1} ${address2}, ${city}, ${state} ${zip}`;
	}

	return `${address1}, ${city}, ${state} ${zip}`;
}
