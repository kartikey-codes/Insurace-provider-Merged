export default function (value) {
	return (
		"$" +
		Intl.NumberFormat("en-US", {
			notation: "compact",
			maximumFractionDigits: 1,
		}).format(value)
	);
}
