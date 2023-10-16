import { groupBy } from "lodash";

/**
 * Commonize grouping of permissions based on keys/values i.e category field
 * @param {array} permissions
 * @return {array}
 */
export function groupPermissions(permissions) {
	return groupBy(permissions, ({ category }) => category);
}
