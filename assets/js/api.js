import axios from "axios";

/**
 * Grab config from meta tags populated by php/api
 */
const baseURL = document.head.querySelector('meta[name="api-base-url"]').content ?? "/";
const apiToken = document.head.querySelector('meta[name="api-token"]').content ?? "";
const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content ?? "";

/**
 * Return configured axios object for Vuex to use
 */
var apiClient = axios.create({
	baseURL,
	headers: {
		common: {
			Authorization: `Bearer ${apiToken}`,
			Accept: "application/json",
			"Content-Type": "application/json",
			"X-Requested-With": "XMLHttpRequest",
			"X-CSRF-TOKEN": csrfToken,
		},
	},
});

apiClient.getApiToken = () => apiToken;
apiClient.getBaseUrl = () => baseURL;
apiClient.getCsrfToken = () => csrfToken;
apiClient.getAdminClientId = () => apiClient.defaults.headers.common["Admin-Client-Id"];

export default apiClient;
