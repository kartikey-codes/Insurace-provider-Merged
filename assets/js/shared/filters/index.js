import abbreviatedCurrency from "./abbreviatedCurrency";
import currency from "./currency";
import dateIntervalString from "./dateIntervalString";
import fileSize from "./fileSize";
import formatDate from "./formatDate";
import formatDateDb from "./formatDateDb";
import formatDateLong from "./formatDateLong";
import formatNumber from "./formatNumber";
import formatPhone from "./formatPhone";
import formatTime from "./formatTime";
import formatTimestamp from "./formatTimestamp";
import formatUnixTimestamp from "./formatUnixTimestamp";
import fromNow from "./fromNow";
import initials from "./initials";
import linkTel from "./linkTel";
import nl2br from "./nl2br";
import truncatedString from "./truncatedString";

/**
 * Filters were removed in Vue 3, so this class exports
 * everything as a helper function.
 */
export default {
	abbreviatedCurrency,
	currency,
	dateIntervalString,
	fileSize,
	formatDate,
	formatDateDb,
	formatDateLong,
	formatNumber,
	formatPhone,
	formatTime,
	formatTimestamp,
	formatUnixTimestamp,
	fromNow,
	initials,
	linkTel,
	nl2br,
	truncatedString,
};

/**
 * Old Vue 2 way of installing global filters.
 */
// export default {
// install(Vue) {
// 	Vue.filter("currency", currency);
// 	Vue.filter("dateIntervalString", dateIntervalString);
// 	Vue.filter("fileSize", fileSize);
// 	Vue.filter("formatDate", formatDate);
// 	Vue.filter("formatDateDb", formatDateDb);
// 	Vue.filter("formatNumber", formatNumber);
// 	Vue.filter("formatPhone", formatPhone);
// 	Vue.filter("formatTime", formatTime);
// 	Vue.filter("formatTimestamp", formatTimestamp);
// 	Vue.filter("formatUnixTimestamp", formatUnixTimestamp);
// 	Vue.filter("fromNow", fromNow);
// 	Vue.filter("linkTel", linkTel);
// 	Vue.filter("nl2br", nl2br);
// 	Vue.filter("truncatedString", truncatedString);
// }
// }
