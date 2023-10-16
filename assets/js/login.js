import "bootstrap/js/dist/util";
import "bootstrap/js/dist/alert";
import "inputmask";

import { debounce } from "lodash";

try {
	window.$ = window.jQuery = require("jquery");
	window.debounce = debounce;

	jQuery(function () {
		new Inputmask("9999999999").mask(".npiNumber");

		new Inputmask("(999) 999-9999").mask(".phoneNumber");

		new Inputmask("9999 9999 9999 9999").mask(".creditCardNumber");

		new Inputmask("999[9]").mask(".creditCardCvv");

		new Inputmask("99999[-9999]", {
			greedy: false,
		}).mask(".zipCode");

		$("form").on("submit", function (e) {
			$(".btn-loader")
				.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`)
				.prop("disabled", true);
		});
	});
} catch (e) {
	console.error("Unable to load jQuery.");
}
