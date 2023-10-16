export default function (value) {
	var cleaned = value.replace(/\D+/g, "").replace(/^[01]/, "");

	return 'tel:+1' + cleaned;
}
