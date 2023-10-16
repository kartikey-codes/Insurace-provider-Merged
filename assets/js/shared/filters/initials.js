export default function (value) {
	return value
		.match(/(^\S\S?|\s\S)?/g)
		.map((v) => v.trim())
		.join("")
		.match(/(^\S|\S$)?/g)
		.join("")
		.toLocaleUpperCase();
}
